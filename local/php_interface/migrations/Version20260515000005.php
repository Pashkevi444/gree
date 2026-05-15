<?php

namespace Sprint\Migration;

/**
 * EN-сид для контента инфоблоков: каждый элемент → SetPropertyValuesEx с
 * заполненными _EN свойствами. Поиск по CODE — должен совпадать с сидерами
 * из Version20260514000003/000007/000010.
 *
 * Зависит от Version20260515000003 (свойства _EN созданы).
 */
class Version20260515000005 extends Version
{
    protected $description = "EN-переводы для контента инфоблоков";

    /**
     * @var array<string, array<string, array<string, string>>>
     * iblock CODE → [element CODE → [property_EN_code => value]]
     */
    private array $seeds = [
        'products' => [
            'gree-bora-x-07' => [
                'NAME_EN' => 'Gree BORA X 07',
                'PREVIEW_TEXT_EN' => 'Inverter wall-mounted air conditioner, 7000 BTU. Coverage up to 20 m². Noise level from 20 dB. Energy class A++.',
                'DETAIL_TEXT_EN' => '<p>Gree BORA X — the flagship line of wall-mounted inverter air conditioners. Powered by G-Tech for maximum efficiency and minimum noise.</p><p>Built-in Cold Catalyst air purifier removes bacteria, viruses, and odors.</p>',
            ],
            'gree-bora-x-09' => [
                'NAME_EN' => 'Gree BORA X 09',
                'PREVIEW_TEXT_EN' => 'Inverter wall-mounted air conditioner, 9000 BTU. Coverage up to 25 m². Wi-Fi control via the GREE+ app.',
                'DETAIL_TEXT_EN' => '<p>BORA X 09 is the optimal choice for bedrooms and small living rooms. Supports control via the GREE+ mobile app, works with Alice and Google Home.</p>',
            ],
            'gree-pular-12' => [
                'NAME_EN' => 'Gree Pular 12',
                'PREVIEW_TEXT_EN' => 'Inverter wall-mounted air conditioner, 12000 BTU. Coverage up to 35 m². Built-in evaporator self-cleaning.',
                'DETAIL_TEXT_EN' => '<p>The Pular series combines high performance with intelligent self-cleaning. The evaporator is automatically frozen and dried every 8 hours of operation.</p>',
            ],
            'gree-lomo-dc-09' => [
                'NAME_EN' => 'Gree Lomo DC 09',
                'PREVIEW_TEXT_EN' => 'Inverter wall-mounted air conditioner, 9000 BTU in a sleek body. Coverage up to 25 m². Champagne color.',
                'DETAIL_TEXT_EN' => '<p>Gree Lomo DC is a designer series for those who value aesthetics. The smooth lines and champagne finish blend into any interior. Full Gree inverter tech stack inside.</p>',
            ],
            'gree-hansol-iii-07' => [
                'NAME_EN' => 'Gree Hansol III 07',
                'PREVIEW_TEXT_EN' => 'Budget wall-mounted air conditioner, 7000 BTU, no inverter. Coverage up to 20 m². A reliable option at a friendly price.',
                'DETAIL_TEXT_EN' => '<p>Gree Hansol III is a proven series for those who need reliability without overpaying. Simple controls, standard cooling/heating, 3-year warranty.</p>',
            ],
            'gree-free-match-12' => [
                'NAME_EN' => 'Gree Free Match 12',
                'PREVIEW_TEXT_EN' => 'Inverter column air conditioner, 12000 BTU. Coverage up to 35 m². For stores, offices, large living rooms.',
                'DETAIL_TEXT_EN' => '<p>Free Match is a floor/ceiling series for spaces where wall mounting is impossible or undesired. Installs on the floor or under the ceiling. Powerful Gree G-Tech inverter compressor.</p>',
            ],
            'gree-gwh18agd' => [
                'NAME_EN' => 'Gree GWH18AGD-K3DNA',
                'PREVIEW_TEXT_EN' => 'Column air conditioner, 18000 BTU. Coverage up to 50 m². For large retail and office spaces.',
                'DETAIL_TEXT_EN' => '<p>Powerful floor/ceiling air conditioner for large open spaces. Dual airflow direction — up and down. Built-in drain pump.</p>',
            ],
            'gree-vir09hp' => [
                'NAME_EN' => 'Gree VIR09HP115V1B',
                'PREVIEW_TEXT_EN' => 'Industrial air conditioner, 9000 BTU. Coverage up to 25 m². For server rooms and production facilities.',
                'DETAIL_TEXT_EN' => '<p>The Gree VIR industrial series is built for round-the-clock operation. Reinforced housing, IP54 dust/moisture protection, working range from -40 °C to +55 °C.</p>',
            ],
        ],

        'home_slider' => [
            'slide-main' => [
                'NAME_EN' => 'Perfect air conditioners for Uzbekistan',
                'SUBTITLE_EN' => 'Cool at <span>+50 °C</span> and warm at <span>−30 °C</span>',
                'BUTTON_TEXT_EN' => 'Choose an air conditioner',
            ],
            'slide-about' => [
                'NAME_EN' => 'Gree — global leader among air conditioner manufacturers',
                'SUBTITLE_EN' => 'Over <span>60 million</span> AC units per year — a guarantee of quality and experience',
                'BUTTON_TEXT_EN' => 'Learn about the brand',
            ],
        ],

        'home_gree_cards' => [
            'guarantee'      => ['NAME_EN' => 'Warranty',       'PREVIEW_TEXT_EN' => '10-year warranty on the air conditioner inverter'],
            'delivery'       => ['NAME_EN' => 'Delivery',       'PREVIEW_TEXT_EN' => 'Free delivery anywhere in the city'],
            'installment'    => ['NAME_EN' => 'Installment',    'PREVIEW_TEXT_EN' => 'Get comfort now, pay later'],
            'service-center' => ['NAME_EN' => 'Service center', 'PREVIEW_TEXT_EN' => 'In-house service center — fast issue resolution'],
        ],

        'home_gree_stats' => [
            'stat-world-first'  => ['NAME_EN' => '#1 in the world', 'PREVIEW_TEXT_EN' => 'In split-system manufacturing in 2024', 'NUMBER_PREFIX_EN' => '#', 'NUMBER_SUFFIX_EN' => 'in the world'],
            'stat-technologies' => ['NAME_EN' => '46 technologies', 'PREVIEW_TEXT_EN' => 'Used by other brands in their air conditioners', 'NUMBER_PREFIX_EN' => '', 'NUMBER_SUFFIX_EN' => 'technologies'],
            'stat-factories'    => ['NAME_EN' => '18 factories',    'PREVIEW_TEXT_EN' => 'Worldwide, plus 1411 laboratories',         'NUMBER_PREFIX_EN' => '', 'NUMBER_SUFFIX_EN' => 'factories'],
        ],

        'home_app_features' => [
            'remote-control' => ['NAME_EN' => 'Control from anywhere', 'PREVIEW_TEXT_EN' => 'Manage your air conditioner at home, at the office, or on the road'],
            'energy-saving'  => ['NAME_EN' => 'Energy saving',         'PREVIEW_TEXT_EN' => 'Turn the air conditioner on only when you actually need it'],
        ],

        'home_technologies' => [
            'tech-extreme'   => ['NAME_EN' => 'Operation in extreme conditions', 'PREVIEW_TEXT_EN' => 'Stable operation from 130 V mains and temperatures from −30 °C to +53 °C.'],
            'tech-smart'     => ['NAME_EN' => 'Intelligent control',             'PREVIEW_TEXT_EN' => 'Wi-Fi module, voice assistants, and the GREE+ app for complete control.'],
            'tech-selfclean' => ['NAME_EN' => 'Self-cleaning system',             'PREVIEW_TEXT_EN' => 'Automatic freezing and drying of the evaporator every 8 hours of operation.'],
            'tech-inverter'  => ['NAME_EN' => 'Inverter technology',              'PREVIEW_TEXT_EN' => 'Smooth power modulation reduces noise and energy consumption by 40%.'],
            'tech-ifeel'     => ['NAME_EN' => 'I-FEEL function',                  'PREVIEW_TEXT_EN' => 'Measures temperature near you, not just at the indoor unit.'],
            'tech-ionizer'   => ['NAME_EN' => 'Air ionization',                   'PREVIEW_TEXT_EN' => 'Cold Plasma neutralizes bacteria and viruses, keeping the air clean.'],
        ],

        'brand_history' => [
            'history' => [
                'NAME_EN' => 'Brand history',
                'DETAIL_TEXT_EN' => '<p>GREE\'s story began in 1991, when two companies — Guanxiong Plastic Company and Haili Air Conditioner Factory — merged into Gree Air Conditioner Factory in Zhuhai, southern China.</p>'
                    . '<p>The company started with a single factory producing window air conditioners for the domestic market. Initially 200 employees turned out fewer than 20,000 units per year.</p>'
                    . '<p>Today, GREE employs more than 90,000 people, including 16,000 R&D staff and over 30,000 technicians.</p>'
                    . '<p>GREE is the largest air conditioner manufacturer in China and one of the largest in the world.</p>',
            ],
        ],

        'brand_why_gree' => [
            'why-gree' => [
                'NAME_EN' => 'Why people choose Gree',
                'PREVIEW_TEXT_EN' => 'Gree is the world leader in air conditioner manufacturing, with proprietary technology, strict quality control, and solutions for every use case.',
                'BUTTON_TEXT_EN' => 'Learn more about Gree',
            ],
        ],

        'brand_gree_cards' => [
            'guarantee'      => ['NAME_EN' => 'Warranty',       'PREVIEW_TEXT_EN' => '10-year warranty on the air conditioner inverter'],
            'delivery'       => ['NAME_EN' => 'Delivery',       'PREVIEW_TEXT_EN' => 'Free delivery anywhere in the city'],
            'installment'    => ['NAME_EN' => 'Installment',    'PREVIEW_TEXT_EN' => 'Get comfort now, pay later'],
            'service-center' => ['NAME_EN' => 'Service center', 'PREVIEW_TEXT_EN' => 'In-house service center — fast issue resolution'],
        ],

        'brand_gree_stats' => [
            'stat-clients'   => ['NAME_EN' => '500M customers', 'PREVIEW_TEXT_EN' => 'Happy customers', 'NUMBER_PREFIX_EN' => '', 'NUMBER_SUFFIX_EN' => ''],
            'stat-factories' => ['NAME_EN' => '18 factories',   'PREVIEW_TEXT_EN' => 'Factories',       'NUMBER_PREFIX_EN' => '', 'NUMBER_SUFFIX_EN' => ''],
            'stat-labs'      => ['NAME_EN' => '1411 labs',      'PREVIEW_TEXT_EN' => 'Laboratories',    'NUMBER_PREFIX_EN' => '', 'NUMBER_SUFFIX_EN' => ''],
            'stat-engineers' => ['NAME_EN' => '16000 engineers','PREVIEW_TEXT_EN' => 'Engineers',       'NUMBER_PREFIX_EN' => '', 'NUMBER_SUFFIX_EN' => ''],
        ],

        'brand_about_cards' => [
            'achievements' => ['NAME_EN' => 'Achievements',     'DETAIL_TEXT_EN' => 'GREE is a global leader in air-conditioner production. In-house research, thousands of patents, and millions of happy customers worldwide. We manufacture both Gree units and OEM products for other global brands.'],
            'mission'      => ['NAME_EN' => 'Mission',          'DETAIL_TEXT_EN' => 'We build smart climate solutions that make life more comfortable, cleaner, and quieter. GREE — technology that works for you every day, with no noise, no overheating, no compromise. Climate you can trust.'],
            'quality'      => ['NAME_EN' => 'Quality control',  'DETAIL_TEXT_EN' => 'From the first screw to the final test — every GREE air conditioner goes through multi-level quality control. We don\'t outsource — all assembly and development is under our direct control. That\'s the quality guarantee.'],
            'innovations'  => ['NAME_EN' => 'Innovations',      'DETAIL_TEXT_EN' => 'GREE operates 152 R&D centers worldwide. We build the technology of tomorrow: from intelligent inverters to air-purification systems. Every model is a result of deep engineering, not just assembly.'],
        ],

        'brand_technologies' => [
            'tech-smps'    => ['NAME_EN' => 'Innovative SMPS transformer', 'DETAIL_TEXT_EN' => 'The innovative SMPS switching transformer keeps the air conditioner stable under voltage fluctuations, reduces power consumption, and improves overall reliability. A modern solution for efficient and durable climate equipment.'],
            'tech-silence' => ['NAME_EN' => 'Low noise level',             'DETAIL_TEXT_EN' => 'Thanks to inverter technology and optimized fan design, Gree air conditioners run almost silently. Comfort without the annoying hum — ideal for bedrooms, kids\' rooms, and offices.'],
            'tech-ifeel'   => ['NAME_EN' => 'I-FEEL function',             'DETAIL_TEXT_EN' => 'A temperature sensor in the wireless remote measures the air temperature near you and transmits it to the indoor unit. The AC then targets your specific spot, not just where the unit is mounted.'],
            'tech-night'   => ['NAME_EN' => 'Comfortable night mode',      'DETAIL_TEXT_EN' => 'Night mode automatically lowers noise and gently regulates temperature, creating optimal sleep conditions. No swings, drafts, or overheating — just comfort and deep rest all night.'],
        ],
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $touched = 0;
        foreach ($this->seeds as $iblockCode => $elements) {
            $iblockId = $this->iblockIdByCode($iblockCode);
            if (!$iblockId) {
                $this->out('Инфоблок "%s" не найден', $iblockCode);
                continue;
            }

            $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();

            foreach ($elements as $elementCode => $props) {
                $row = $entity::query()
                    ->where('CODE', $elementCode)
                    ->setSelect(['ID'])
                    ->exec()
                    ->fetch();

                if (!$row) {
                    $this->out('  %s/%s: элемент не найден', $iblockCode, $elementCode);
                    continue;
                }

                \CIBlockElement::SetPropertyValuesEx((int) $row['ID'], $iblockId, $props);
                $touched++;
            }
            $this->out('  %s: %d элементов', $iblockCode, count($elements));
        }

        $this->outSuccess('Обновлено EN-значений: %d', $touched);
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }

    private function iblockIdByCode(string $code): int
    {
        $row = \Bitrix\Iblock\IblockTable::query()
            ->where('CODE', $code)
            ->setSelect(['ID'])
            ->exec()
            ->fetch();
        return (int) ($row['ID'] ?? 0);
    }
}
