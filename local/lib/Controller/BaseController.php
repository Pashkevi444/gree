<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Bitrix\Main\Web\Json;
use Gree\Contract\Http\HttpContextInterface;
use Gree\Contract\Service\CsrfServiceInterface;
use Gree\Core\App;
use Gree\Core\Blade;
use Gree\DTO\SeoDto;
use Gree\View\BaseViewData;

abstract class BaseController
{
    protected function view(string $template, BaseViewData|array $data = []): HttpResponse
    {
        global $APPLICATION;

        // Issue CSRF cookie on every HTML render so the front-end has a fresh
        // token to mirror into headers on subsequent API calls.
        App::get(CsrfServiceInterface::class)->readOrIssue();

        $data = $data instanceof BaseViewData ? $data->toArray() : $data;

        // BitrixBladeEngine::evaluatePath uses require (no OB wrap) so Bitrix's
        // AddBufferContent can cycle its OBs normally during header/footer rendering.
        Blade::factory()->make(str_replace('/', '.', $template), $data)->render();

        $html = $APPLICATION->EndBufferContentMan();

        $response = new HttpResponse();
        $this->applySecurityHeaders($response);
        $this->flushCookies($response);
        $response->setContent($html);

        return $response;
    }

    /**
     * Return a JSON response for API routes.
     */
    protected function json(mixed $data, int $status = 200): HttpResponse
    {
        $response = new HttpResponse();
        $this->applySecurityHeaders($response);
        $this->flushCookies($response);
        $response->addHeader('Content-Type', 'application/json; charset=utf-8');
        // JSON responses should never be cached by intermediaries — they reveal user state.
        $response->addHeader('Cache-Control', 'no-store');
        $response->setStatus($status . ' ' . $this->statusText($status));
        $response->setContent(Json::encode($data));

        return $response;
    }

    /**
     * Set page meta before calling view() — must be called before view()
     * so that $APPLICATION->ShowHead() picks up the values.
     */
    protected function setMeta(string $title, string $description = '', string $keywords = ''): void
    {
        global $APPLICATION;
        $APPLICATION->SetTitle($title);
        if ($description !== '') {
            $APPLICATION->SetPageProperty('description', $description);
        }
        if ($keywords !== '') {
            $APPLICATION->SetPageProperty('keywords', $keywords);
        }
    }

    /**
     * Apply a resolved SeoDto onto the Bitrix page. Title and meta
     * description/keywords go through page properties (ShowHead() renders
     * them). Open Graph tags can't ride page properties because Bitrix only
     * auto-renders the keys explicitly listed in iblock/site settings — we
     * emit them as raw <meta> via the asset manager.
     *
     * Empty fields are skipped so a page-specific SEO override doesn't blank
     * out values that another layer (default site SEO, IPROPERTY templates)
     * may have set.
     */
    protected function applySeo(SeoDto $seo): void
    {
        global $APPLICATION;

        if ($seo->title !== '') {
            $APPLICATION->SetTitle($seo->title);
            $APPLICATION->SetPageProperty('title', $seo->title);
        }
        if ($seo->description !== '') {
            $APPLICATION->SetPageProperty('description', $seo->description);
        }
        if ($seo->keywords !== '') {
            $APPLICATION->SetPageProperty('keywords', $seo->keywords);
        }

        $asset = \Bitrix\Main\Page\Asset::getInstance();
        $emit = static function (string $property, string $content) use ($asset): void {
            if ($content === '') {
                return;
            }
            $asset->addString(
                '<meta property="' . htmlspecialchars($property, ENT_QUOTES | ENT_HTML5)
                . '" content="' . htmlspecialchars($content, ENT_QUOTES | ENT_HTML5) . '" />',
            );
        };
        $emit('og:title', $seo->ogTitle !== '' ? $seo->ogTitle : $seo->title);
        $emit('og:description', $seo->ogDescription !== '' ? $seo->ogDescription : $seo->description);
        $emit('og:image', $seo->ogImage);
    }

    /**
     * Register page CSS + JS. Assets are served straight from /dist/ — the frontend
     * dev's build directory. No duplication at the project root: drop a new build
     * into /dist/ and these URLs continue to work.
     *
     * Scripts MUST be deferred — Bitrix Asset::addJs() injects them into <head>
     * without defer, which runs them before DOM exists and crashes querySelector
     * calls inside main.js. Using addString preserves the `defer` attribute.
     */
    protected function addPageAssets(string $page): void
    {
        $asset = \Bitrix\Main\Page\Asset::getInstance();
        $asset->addCss('/dist/styles/main.css');
        $asset->addCss('/dist/styles/' . $page . '.css');
        $asset->addString('<script defer src="/dist/scripts/main.js"></script>');
        $asset->addString('<script defer src="/dist/scripts/' . $page . '.js"></script>');
    }

    protected function getRequest(): \Bitrix\Main\HttpRequest
    {
        return \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
    }

    /**
     * Defense-in-depth response hardening:
     *   nosniff     — kills MIME-confusion attacks on JS/HTML
     *   DENY        — clickjacking via <iframe>
     *   strict-origin-when-cross-origin — leaks no path on outbound nav
     *   X-XSS-Protection 0 — disables the legacy IE/Edge XSS auditor (it's
     *                        been a known footgun for years)
     */
    private function applySecurityHeaders(HttpResponse $response): void
    {
        $response->addHeader('X-Content-Type-Options', 'nosniff');
        $response->addHeader('X-Frame-Options', 'DENY');
        $response->addHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->addHeader('X-XSS-Protection', '0');
        $response->addHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    }

    /**
     * Drain queued outgoing cookies (CSRF, cart_token) onto the response.
     * Until this runs, services that called HttpContext::setCookie() haven't
     * actually written anything to the wire.
     */
    private function flushCookies(HttpResponse $response): void
    {
        App::get(HttpContextInterface::class)->flushCookiesInto($response);
    }

    private function statusText(int $code): string
    {
        return match ($code) {
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            default => '',
        };
    }
}
