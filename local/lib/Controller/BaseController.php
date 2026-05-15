<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Bitrix\Main\Web\Json;
use Gree\Core\Blade;
use Gree\View\BaseViewData;

abstract class BaseController
{
    protected function view(string $template, BaseViewData|array $data = []): HttpResponse
    {
        global $APPLICATION;

        $data = $data instanceof BaseViewData ? $data->toArray() : $data;

        // BitrixBladeEngine::evaluatePath uses require (no OB wrap) so Bitrix's
        // AddBufferContent can cycle its OBs normally during header/footer rendering.
        Blade::factory()->make(str_replace('/', '.', $template), $data)->render();

        $html = $APPLICATION->EndBufferContentMan();

        $response = new HttpResponse();
        $response->setContent($html);

        return $response;
    }

    /**
     * Return a JSON response for API routes.
     */
    protected function json(mixed $data, int $status = 200): HttpResponse
    {
        $response = new HttpResponse();
        $response->addHeader('Content-Type', 'application/json; charset=utf-8');
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
