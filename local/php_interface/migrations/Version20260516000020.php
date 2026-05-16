<?php

namespace Sprint\Migration;

/**
 * UI-перевод для строки времени чтения статьи блога.
 */
class Version20260516000020 extends Version
{
    protected $description = "UI-перевод: blog.reading_minutes";

    /** @var array<string, array{ru: string, en: string}> */
    private array $entries = [
        'blog.reading_minutes' => [
            'ru' => ':minutes мин. на чтение',
            'en' => ':minutes min read',
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

        $this->outSuccess('Загружено: %d', count($this->entries));
    }

    public function down(): void
    {
        $this->outSuccess('Откат не требуется');
    }
}
