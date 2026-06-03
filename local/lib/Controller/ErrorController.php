<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Helpers\Language;
use Gree\View\NotFoundViewData;

/**
 * Системные страницы ошибок. Сейчас один обработчик — 404. Точка входа
 * `/404.php` вызывает {@see self::notFound()} напрямую: BaseController::view()
 * сам отрендерит layouts.app + header.php + footer.php, мы только метим
 * статус 404 на HttpResponse, чтобы Bitrix не показал свой дефолтный шаблон.
 */
final class ErrorController extends BaseController
{
    public function notFound(): HttpResponse
    {
        $this->setMeta(Language::t('404.title'), Language::t('404.description'));
        $this->addPageAssets('404');

        $response = $this->view('errors/404', new NotFoundViewData());
        $response->setStatus('404 Not Found');

        return $response;
    }
}
