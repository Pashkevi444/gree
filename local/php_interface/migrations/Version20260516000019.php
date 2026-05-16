<?php

namespace Sprint\Migration;

/**
 * Доливаем 2 строки UI-переводов под hero страницы блога.
 */
class Version20260516000019 extends Version
{
    protected $description = "UI-переводы: blog.hero.title / .description";

    /** @var array<string, array{ru: string, en: string}> */
    private array $entries = [
        'blog.hero.title' => [
            'ru' => 'Блог Gree',
            'en' => 'Gree blog',
        ],
        'blog.hero.description' => [
            'ru' => 'Полезные советы и новости от экспертов Gree для вашего дома.',
            'en' => 'Helpful tips and news from Gree experts for your home.',
        ],
    ];

    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('Translations');
        if (!$hlblockId) {
            $this->outError('Highloadblock «Translations» не найден');
            return;
        }

        foreach ($this->entries as $code => $values) {
            $helper->Hlblock()->addElement($hlblockId, [
                'UF_CODE'     => $code,
                'UF_VALUE_RU' => $values['ru'],
                'UF_VALUE_EN' => $values['en'],
            ]);
        }

        $this->outSuccess('Загружено переводов: %d', count($this->entries));
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }
}
