<?php

namespace Sprint\Migration;

class Version20260514000008 extends Version
{
    protected $description = "Тестовые картинки для слайдера, технологий и товаров";

    private string $imagesDir;

    public function up()
    {
        $this->imagesDir = __DIR__ . '/images';
        $helper = $this->getHelperManager();

        $this->addSliderImages($helper);
        $this->addTechnologyImages($helper);
        $this->addProductImages($helper);
    }

    private function addSliderImages(HelperManager $helper): void
    {
        $id   = $helper->Iblock()->getIblockIdIfExists('home_slider');
        $file = \CFile::MakeFileArray($this->imagesDir . '/hero-slide.png');

        foreach (['slide-main', 'slide-about'] as $code) {
            $helper->Iblock()->saveElement($id, [
                'CODE'            => $code,
                'PREVIEW_PICTURE' => $file,
            ]);
            $this->out('Картинка слайда «%s» добавлена', $code);
        }

        $this->outSuccess('Картинки слайдера добавлены');
    }

    private function addTechnologyImages(HelperManager $helper): void
    {
        $id   = $helper->Iblock()->getIblockIdIfExists('home_technologies');
        $file = \CFile::MakeFileArray($this->imagesDir . '/technology.png');

        $codes = ['tech-extreme', 'tech-smart', 'tech-selfclean', 'tech-inverter', 'tech-ifeel', 'tech-ionizer'];

        foreach ($codes as $code) {
            $helper->Iblock()->saveElement($id, [
                'CODE'            => $code,
                'PREVIEW_PICTURE' => $file,
            ]);
            $this->out('Картинка технологии «%s» добавлена', $code);
        }

        $this->outSuccess('Картинки технологий добавлены');
    }

    private function addProductImages(HelperManager $helper): void
    {
        $id   = $helper->Iblock()->getIblockIdIfExists('products');
        $file = \CFile::MakeFileArray($this->imagesDir . '/product.png');

        $codes = [
            'gree-bora-9000',
            'gree-lomo-12000',
            'gree-pular-18000',
            'gree-fairy-24000',
            'gree-u-crown-36000',
            'haier-tide-9000',
            'midea-all-easy-12000',
            'daikin-ftxb-18000',
        ];

        foreach ($codes as $code) {
            $helper->Iblock()->saveElement($id, [
                'CODE'            => $code,
                'PREVIEW_PICTURE' => $file,
            ]);
            $this->out('Картинка товара «%s» добавлена', $code);
        }

        $this->outSuccess('Картинки товаров добавлены');
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $map = [
            'home_slider'       => ['slide-main', 'slide-about'],
            'home_technologies' => ['tech-extreme', 'tech-smart', 'tech-selfclean', 'tech-inverter', 'tech-ifeel', 'tech-ionizer'],
            'products'          => ['gree-bora-9000', 'gree-lomo-12000', 'gree-pular-18000', 'gree-fairy-24000', 'gree-u-crown-36000', 'haier-tide-9000', 'midea-all-easy-12000', 'daikin-ftxb-18000'],
        ];

        foreach ($map as $iblockCode => $codes) {
            $id = $helper->Iblock()->getIblockIdIfExists($iblockCode);
            foreach ($codes as $code) {
                $helper->Iblock()->saveElement($id, [
                    'CODE'            => $code,
                    'PREVIEW_PICTURE' => false,
                ]);
            }
            $this->out('Картинки из «%s» удалены', $iblockCode);
        }

        $this->outSuccess('Тестовые картинки удалены');
    }
}
