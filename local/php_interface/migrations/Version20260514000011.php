<?php

namespace Sprint\Migration;

class Version20260514000011 extends Version
{
    protected $description = "Инфоблоки: скрыть лишние поля в форме редактирования элемента";

    private array $iblockCodes = [
        'brands', 'products', 'blog',
        'home_slider', 'home_gree_cards', 'home_gree_stats', 'home_app_features', 'home_technologies',
        'brand_history', 'brand_why_gree', 'brand_gree_cards', 'brand_gree_stats', 'brand_about_cards', 'brand_technologies',
    ];

    public function up()
    {
        $helper = $this->getHelperManager();

        // Отключаем разделы у всех типов инфоблоков (скрывает вкладку «Разделы»)
        foreach (['catalog', 'content', 'brand', 'home'] as $typeId) {
            $type = $helper->Iblock()->getIblockType($typeId);
            if (!$type) {
                continue;
            }
            $helper->Iblock()->saveIblockType(array_merge($type, ['SECTIONS' => 'N']));
            $this->out('Тип "%s": разделы отключены', $typeId);
        }

        foreach ($this->iblockCodes as $code) {
            $iblockId = $helper->Iblock()->getIblockIdIfExists($code);
            if (!$iblockId) {
                $this->out('Инфоблок "%s" не найден, пропущен', $code);
                continue;
            }

            $helper->Iblock()->saveIblockFields($iblockId, [
                // Скрыть период активности
                'ACTIVE_FROM' => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
                'ACTIVE_TO'   => ['DEFAULT_VALUE' => '', 'IS_REQUIRED' => 'N'],
                // Символьный код — автотранслит
                'CODE' => [
                    'DEFAULT_VALUE' => ['TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'REPLACE_SPACE' => '-', 'REPLACE_OTHER' => '-', 'DELETE_UNSET' => 'N', 'UNIQUE' => 'Y'],
                    'IS_REQUIRED' => 'N',
                ],
                // XML_ID не обязателен
                'XML_ID' => ['IS_REQUIRED' => 'N'],
                // Теги не используем
                'TAGS' => ['IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''],
            ]);

            $this->out('Инфоблок "%s" [id=%d]: поля настроены', $code, $iblockId);
        }

        $this->outSuccess('Готово');
    }

    public function down()
    {
        $this->outSuccess('Откат не требуется');
    }
}
