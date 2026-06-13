<?php

namespace Sprint\Migration;

/**
 * Переносит iblock `blog` (создавался как content в Version20260514000001) в
 * собственный тип «blog», по аналогии с Version20260604000001 (help) и
 * Version20260603000009 (contacts).
 *
 * Идемпотентна: saveIblockType обновит при повторе; CIBlock::Update пропустит
 * если iblock уже в нужном типе.
 */
class Version20260604000008 extends Version
{
    protected $description = "Тип iblock 'blog' + перенос blog iblock'а из 'content'";

    public function up(): void
    {
        $this->move('blog');
    }

    public function down(): void
    {
        $this->move('content');
        try {
            $this->getHelperManager()->Iblock()->deleteIblockTypeIfExists('blog');
        } catch (\Throwable) {
        }
    }

    private function move(string $targetType): void
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        if ($targetType === 'blog') {
            $this->getHelperManager()->Iblock()->saveIblockType([
                'ID'        => 'blog',
                'SECTIONS'  => 'N',
                'IN_RSS'    => 'N',
                'SORT'      => 500,
                'LANG'      => [
                    'ru' => ['NAME' => 'Блог', 'SECTION_NAME' => 'Разделы', 'ELEMENT_NAME' => 'Статьи'],
                ],
            ]);
        }

        $row = \Bitrix\Iblock\IblockTable::query()
            ->where('CODE', 'blog')
            ->setSelect(['ID', 'IBLOCK_TYPE_ID'])
            ->exec()
            ->fetch();

        if (!$row) {
            $this->out('Iblock blog не найден');
            return;
        }
        if ((string) ($row['IBLOCK_TYPE_ID'] ?? '') === $targetType) {
            $this->outSuccess('blog уже в типе «%s» — пропуск', $targetType);
            return;
        }

        $ib = new \CIBlock();
        if (!$ib->Update((int) $row['ID'], ['IBLOCK_TYPE_ID' => $targetType])) {
            $this->outError('Update провалился: %s', $ib->LAST_ERROR ?: 'unknown');
            return;
        }

        \Bitrix\Iblock\IblockTable::cleanCache();
        \Bitrix\Main\Application::getInstance()->getManagedCache()->cleanDir('b_iblock');

        $this->outSuccess('Iblock blog перемещён в тип «%s»', $targetType);
    }
}
