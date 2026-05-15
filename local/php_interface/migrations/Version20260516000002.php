<?php

namespace Sprint\Migration;

/**
 * Сиды для секции «Почему выбирают Gree» на странице каталога:
 *   catalog_gree_cards — 4 преимущества
 *   catalog_gree_stats — 3 статистики
 *
 * Каждое текстовое поле сразу с RU + EN значением.
 * Зависит от Version20260516000001 (структура инфоблоков).
 */
class Version20260516000002 extends Version
{
    protected $description = "Сиды для catalog_gree_cards / catalog_gree_stats";

    /** @var array<int, array<string, mixed>> */
    private array $cards = [
        [
            'fields' => ['CODE' => 'guarantee', 'ACTIVE' => 'Y', 'SORT' => 100],
            'props'  => [
                'NAME_RU'         => 'Гарантия',
                'NAME_EN'         => 'Warranty',
                'PREVIEW_TEXT_RU' => '10 лет гарантии на инвертор кондиционера',
                'PREVIEW_TEXT_EN' => '10-year warranty on the air conditioner inverter',
                'ICON_CODE'       => 'thumbs-up',
            ],
        ],
        [
            'fields' => ['CODE' => 'delivery', 'ACTIVE' => 'Y', 'SORT' => 200],
            'props'  => [
                'NAME_RU'         => 'Доставка',
                'NAME_EN'         => 'Delivery',
                'PREVIEW_TEXT_RU' => 'Бесплатно доставим в любую точку города',
                'PREVIEW_TEXT_EN' => 'Free delivery anywhere in the city',
                'ICON_CODE'       => 'truck',
            ],
        ],
        [
            'fields' => ['CODE' => 'installment', 'ACTIVE' => 'Y', 'SORT' => 300],
            'props'  => [
                'NAME_RU'         => 'Рассрочка',
                'NAME_EN'         => 'Installment',
                'PREVIEW_TEXT_RU' => 'Приобретайте комфорт сейчас, а платите потом',
                'PREVIEW_TEXT_EN' => 'Get comfort now, pay later',
                'ICON_CODE'       => 'dollar',
            ],
        ],
        [
            'fields' => ['CODE' => 'service-center', 'ACTIVE' => 'Y', 'SORT' => 400],
            'props'  => [
                'NAME_RU'         => 'Сервисный центр',
                'NAME_EN'         => 'Service center',
                'PREVIEW_TEXT_RU' => 'Свой сервисный центр — быстро решаем все вопросы',
                'PREVIEW_TEXT_EN' => 'In-house service center — fast issue resolution',
                'ICON_CODE'       => 'wrench',
            ],
        ],
    ];

    /** @var array<int, array<string, mixed>> */
    private array $stats = [
        [
            'fields' => ['CODE' => 'stat-world-first', 'ACTIVE' => 'Y', 'SORT' => 100],
            'props'  => [
                'NAME_RU'          => '№1 в мире',
                'NAME_EN'          => '#1 in the world',
                'PREVIEW_TEXT_RU'  => 'По производству сплит-систем в 2024 году',
                'PREVIEW_TEXT_EN'  => 'In split-system manufacturing in 2024',
                'NUMBER_PREFIX_RU' => '№',
                'NUMBER_PREFIX_EN' => '#',
                'NUMBER_SUFFIX_RU' => 'в мире',
                'NUMBER_SUFFIX_EN' => 'in the world',
                'NUMBER_VALUE'     => 1,
            ],
        ],
        [
            'fields' => ['CODE' => 'stat-technologies', 'ACTIVE' => 'Y', 'SORT' => 200],
            'props'  => [
                'NAME_RU'          => '46 технологий',
                'NAME_EN'          => '46 technologies',
                'PREVIEW_TEXT_RU'  => 'Их используют другие бренды в своих кондиционерах',
                'PREVIEW_TEXT_EN'  => 'Used by other brands in their air conditioners',
                'NUMBER_PREFIX_RU' => '',
                'NUMBER_PREFIX_EN' => '',
                'NUMBER_SUFFIX_RU' => 'технологий',
                'NUMBER_SUFFIX_EN' => 'technologies',
                'NUMBER_VALUE'     => 46,
            ],
        ],
        [
            'fields' => ['CODE' => 'stat-factories', 'ACTIVE' => 'Y', 'SORT' => 300],
            'props'  => [
                'NAME_RU'          => '18 заводов',
                'NAME_EN'          => '18 factories',
                'PREVIEW_TEXT_RU'  => 'По всему миру, а также 1411 лабораторий',
                'PREVIEW_TEXT_EN'  => 'Worldwide, plus 1411 laboratories',
                'NUMBER_PREFIX_RU' => '',
                'NUMBER_PREFIX_EN' => '',
                'NUMBER_SUFFIX_RU' => 'заводов',
                'NUMBER_SUFFIX_EN' => 'factories',
                'NUMBER_VALUE'     => 18,
            ],
        ],
    ];

    public function up(): void
    {
        $helper = $this->getHelperManager();

        $cardsId = $helper->Iblock()->getIblockIdIfExists('catalog_gree_cards');
        $statsId = $helper->Iblock()->getIblockIdIfExists('catalog_gree_stats');

        if (!$cardsId || !$statsId) {
            $this->outError('Инфоблоки catalog_gree_cards / catalog_gree_stats не найдены — запусти Version20260516000001');
            return;
        }

        foreach ($this->cards as $card) {
            // NAME поле всё ещё обязательно — кладём RU как служебную метку.
            $fields = array_merge($card['fields'], ['NAME' => $card['props']['NAME_RU']]);
            $id = $helper->Iblock()->saveElement($cardsId, $fields, $card['props']);
            $this->out('  card "%s" [id=%d]', $card['fields']['CODE'], $id);
        }

        foreach ($this->stats as $stat) {
            $fields = array_merge($stat['fields'], ['NAME' => $stat['props']['NAME_RU']]);
            $id = $helper->Iblock()->saveElement($statsId, $fields, $stat['props']);
            $this->out('  stat "%s" [id=%d]', $stat['fields']['CODE'], $id);
        }

        $this->outSuccess('Загружено: %d карточек, %d статистик', count($this->cards), count($this->stats));
    }

    public function down(): void
    {
        $helper = $this->getHelperManager();
        $cardsId = $helper->Iblock()->getIblockIdIfExists('catalog_gree_cards');
        $statsId = $helper->Iblock()->getIblockIdIfExists('catalog_gree_stats');

        if ($cardsId) {
            foreach ($this->cards as $card) {
                $helper->Iblock()->deleteElementIfExists($cardsId, $card['fields']['CODE']);
            }
        }
        if ($statsId) {
            foreach ($this->stats as $stat) {
                $helper->Iblock()->deleteElementIfExists($statsId, $stat['fields']['CODE']);
            }
        }
        $this->outSuccess('Сиды catalog_gree_* удалены');
    }
}
