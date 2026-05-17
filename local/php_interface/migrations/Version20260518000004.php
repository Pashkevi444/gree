<?php

namespace Sprint\Migration;

/**
 * SEO-записи для страниц оформления заказа и success-экрана.
 * Дополняет HL «Seo», созданный в Version20260517000004 + сидинг 17000005.
 */
class Version20260518000004 extends Version
{
    protected $description = "SEO записи: order / order_success";

    /** @var array<int, array<string, string>> */
    private array $entries = [
        [
            'UF_PAGE_CODE'         => 'order',
            'UF_TITLE_RU'          => 'Оформление заказа — Gree Узбекистан',
            'UF_TITLE_EN'          => 'Checkout — Gree Uzbekistan',
            'UF_DESCRIPTION_RU'    => 'Заполните данные для доставки и выберите способ оплаты. Доставка по Ташкенту — бесплатно. Рассрочка ANORBANK или UZUM.',
            'UF_DESCRIPTION_EN'    => 'Fill in delivery details and pick a payment method. Free Tashkent delivery. ANORBANK or UZUM installment plans.',
            'UF_KEYWORDS_RU'       => 'оформление заказа, gree, доставка, оплата, ташкент, узбекистан',
            'UF_KEYWORDS_EN'       => 'checkout, gree, delivery, payment, tashkent, uzbekistan',
            'UF_OG_TITLE_RU'       => 'Оформление заказа Gree',
            'UF_OG_TITLE_EN'       => 'Gree checkout',
            'UF_OG_DESCRIPTION_RU' => '',
            'UF_OG_DESCRIPTION_EN' => '',
            'UF_OG_IMAGE'          => '',
        ],
        [
            'UF_PAGE_CODE'         => 'order_success',
            'UF_TITLE_RU'          => 'Заказ принят — Gree Узбекистан',
            'UF_TITLE_EN'          => 'Order received — Gree Uzbekistan',
            'UF_DESCRIPTION_RU'    => 'Спасибо! Наш менеджер свяжется в течение 15 минут в рабочее время.',
            'UF_DESCRIPTION_EN'    => 'Thanks! Our manager will contact you within 15 minutes during business hours.',
            'UF_KEYWORDS_RU'       => 'заказ принят, gree, благодарим',
            'UF_KEYWORDS_EN'       => 'order received, gree, thank you',
            'UF_OG_TITLE_RU'       => 'Благодарим за заказ!',
            'UF_OG_TITLE_EN'       => 'Thank you for your order!',
            'UF_OG_DESCRIPTION_RU' => '',
            'UF_OG_DESCRIPTION_EN' => '',
            'UF_OG_IMAGE'          => '',
        ],
    ];

    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Seo');
        if (!$hlblockId) {
            $this->outError('HL «Seo» не найден');
            return;
        }
        foreach ($this->entries as $row) {
            $helper->Hlblock()->addElement($hlblockId, $row);
            $this->out('  + %s', $row['UF_PAGE_CODE']);
        }
        $this->outSuccess('Загружено: %d', count($this->entries));
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }
}
