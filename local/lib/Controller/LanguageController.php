<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\LanguageServiceInterface;
use Gree\Enum\Locale;

final class LanguageController extends BaseController
{
    public function __construct(private readonly LanguageServiceInterface $language) {}

    public function switch(string $locale): HttpResponse
    {
        $value = Locale::tryFrom($locale);
        if ($value !== null) {
            $this->language->set($value);
        }

        $referer = $this->getRequest()->getHeader('Referer');
        $target = is_string($referer) && $referer !== '' ? $referer : '/';

        $response = new HttpResponse();
        $response->setStatus('302 Found');
        $response->addHeader('Location', $target);

        return $response;
    }
}
