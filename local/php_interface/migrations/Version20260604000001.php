<?php

namespace Sprint\Migration;

/**
 * Выделяет help-iblock'и (создавались как content в Version20260603000001) в
 * собственный тип «help», по аналогии с типом «contacts» из
 * Version20260603000009.
 *
 * Идемпотентна: если тип уже есть — saveIblockType просто обновит; если
 * iblock уже в нужном типе — Update тоже безвреден.
 *
 * Iblock'и (CODE):
 *   help_payment_methods, help_delivery, help_exchange_steps,
 *   help_refund_steps, help_service_features, help_service_hero,
 *   help_service_cards
 */
class Version20260604000001 extends Version
{
    protected $description = "Тип iblock 'help' + перенос help_* iblock'ов из 'content'";

    /** @var string[] */
    private array $iblocks = [
        'help_payment_methods',
        'help_delivery',
        'help_exchange_steps',
        'help_refund_steps',
        'help_service_features',
        'help_service_hero',
        'help_service_cards',
    ];

    public function up(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $helper = $this->getHelperManager();

        $helper->Iblock()->saveIblockType([
            'ID'        => 'help',
            'SECTIONS'  => 'N',
            'IN_RSS'    => 'N',
            'SORT'      => 600,
            'LANG'      => [
                'ru' => ['NAME' => 'Помощь', 'SECTION_NAME' => 'Разделы', 'ELEMENT_NAME' => 'Элементы'],
            ],
        ]);

        $this->moveAll('help');
    }

    public function down(): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $this->moveAll('content');

        try {
            $this->getHelperManager()->Iblock()->deleteIblockTypeIfExists('help');
        } catch (\Throwable) {
            // тип удаляется только когда пуст; если внутри ещё что-то — оставим
        }
    }

    private function moveAll(string $targetType): void
    {
        $moved = 0;
        foreach ($this->iblocks as $code) {
            $row = \Bitrix\Iblock\IblockTable::query()
                ->where('CODE', $code)
                ->setSelect(['ID', 'IBLOCK_TYPE_ID'])
                ->exec()
                ->fetch();
            if (!$row) {
                $this->out('  %s: iblock не найден', $code);
                continue;
            }
            if ((string) ($row['IBLOCK_TYPE_ID'] ?? '') === $targetType) {
                continue;
            }
            $ib = new \CIBlock();
            $ok = $ib->Update((int) $row['ID'], ['IBLOCK_TYPE_ID' => $targetType]);
            if (!$ok) {
                $this->outError('  %s: Update провалился — %s', $code, $ib->LAST_ERROR ?: 'unknown');
                continue;
            }
            $moved++;
        }

        \Bitrix\Iblock\IblockTable::cleanCache();
        \Bitrix\Main\Application::getInstance()->getManagedCache()->cleanDir('b_iblock');

        $this->outSuccess('Iblock\'и перемещены в тип «%s»: %d', $targetType, $moved);
    }
}
