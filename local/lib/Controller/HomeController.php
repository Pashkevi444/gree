<?php

declare(strict_types=1);

namespace Gree\Controller;

use Bitrix\Main\HttpResponse;
use Gree\Collection\AppFeatureCollection;
use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Collection\SliderItemCollection;
use Gree\Collection\TechnologyCollection;
use Gree\Contract\Service\HomeServiceInterface;
use Gree\View\HomeViewData;

final class HomeController extends BaseController
{
    public function __construct(private readonly HomeServiceInterface $homeService) {}

    public function index(): HttpResponse
    {
        $this->setMeta('Gree — официальный дистрибьютор в Узбекистане');
        $this->addPageAssets('home');

        $data = new HomeViewData(
            slider:       $this->homeService->getSlider(),
            greeCards:    $this->homeService->getGreeCards(),
            greeStats:    $this->homeService->getGreeStats(),
            appFeatures:  $this->homeService->getAppFeatures(),
            technologies: $this->homeService->getTechnologies(),
        );

        return $this->view('home/index', $data);
    }
}
