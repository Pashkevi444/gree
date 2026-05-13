<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Contract\Service\BrandServiceInterface;
use Gree\View\BrandViewData;

final class BrandController extends BaseController
{
    public function __construct(private readonly BrandServiceInterface $brandService) {}

    public function show(string $code): HttpResponse
    {
        $this->setMeta('О бренде Gree');
        $this->addPageAssets('brand');

        $data = new BrandViewData(
            history: $this->brandService->getHistory(),
            whyGree: $this->brandService->getWhyGree(),
            greeCards: $this->brandService->getGreeCards(),
            greeStats: $this->brandService->getGreeStats(),
            aboutCards: $this->brandService->getAboutCards(),
            technologies: $this->brandService->getTechnologies(),
        );

        return $this->view('brand/show', $data);
    }
}
