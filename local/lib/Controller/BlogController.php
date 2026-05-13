<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;

final class BlogController extends BaseController
{
    public function index(): HttpResponse
    {
        $this->setMeta('Блог Gree');
        $this->addPageAssets('blog');

        return $this->view('blog/index');
    }

    public function show(string $code): HttpResponse
    {
        $this->setMeta('Статья блога Gree');
        $this->addPageAssets('blog-item');

        return $this->view('blog/show', ['code' => $code]);
    }
}
