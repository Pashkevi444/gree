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

        // CSRF-кука нужна фронту на каждом HTML-рендере для последующих API-вызовов.
        App::get(CsrfServiceInterface::class)->readOrIssue();

        $data = $data instanceof BaseViewData ? $data->toArray() : $data;

        Blade::factory()->make(str_replace('/', '.', $template), $data)->render();

        $html = $APPLICATION->EndBufferContentMan();

        $response = new HttpResponse();
        $this->applySecurityHeaders($response);
        $this->flushCookies($response);
        $response->setContent($html);

        return $response;
    }

    protected function json(mixed $data, int $status = 200): HttpResponse
    {
        $response = new HttpResponse();
        $this->applySecurityHeaders($response);
        $this->flushCookies($response);
        $response->addHeader('Content-Type', 'application/json; charset=utf-8');
        // no-store: JSON отдаёт пользовательский стейт, кешировать нельзя.
        $response->addHeader('Cache-Control', 'no-store');
        $response->setStatus($status . ' ' . $this->statusText($status));
        $response->setContent(Json::encode($data));

        return $response;
    }

    /** Вызывать до view() — иначе ShowHead() не подхватит значения. */
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

    /** OG-теги идут raw <meta> через Asset (page properties их не рендерят); пустые поля пропускаются, чтобы не затирать SEO других слоёв. */
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

    /** Скрипты через addString с defer — Asset::addJs() кладёт их в <head> без defer, и main.js падает до построения DOM. */
    protected function addPageAssets(string $page): void
    {
        $asset = \Bitrix\Main\Page\Asset::getInstance();
        $asset->addCss('/dist/styles/main.css');
        $asset->addCss('/dist/styles/' . $page . '.css');
        // custom.css после dist-CSS — выигрывает каскад при равной специфичности.
        $asset->addCss('/local/templates/gree/assets/custom.css');
        $asset->addString('<script defer src="/dist/scripts/main.js"></script>');
        $asset->addString('<script defer src="/dist/scripts/' . $page . '.js"></script>');
    }

    protected function getRequest(): \Bitrix\Main\HttpRequest
    {
        return \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
    }

    private function applySecurityHeaders(HttpResponse $response): void
    {
        $response->addHeader('X-Content-Type-Options', 'nosniff');
        $response->addHeader('X-Frame-Options', 'DENY');
        $response->addHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->addHeader('X-XSS-Protection', '0');
        $response->addHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    }

    /** Без этого вызова куки из HttpContext::setCookie() не уходят на клиента. */
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
