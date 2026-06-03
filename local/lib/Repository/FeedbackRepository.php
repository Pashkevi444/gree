<?php

declare(strict_types=1);

namespace Gree\Repository;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Type\DateTime;
use Gree\Contract\Repository\FeedbackRepositoryInterface;
use Gree\Enum\HlblockCode;

/**
 * Универсальный writer в feedback-HL'ы. Не наследует BaseHlblockRepository —
 * там hlblock() компилируется один раз в protected-метод, а здесь нужно
 * динамически по HlblockCode из канала.
 */
final class FeedbackRepository implements FeedbackRepositoryInterface
{
    public function insert(HlblockCode $hlblock, array $fields): int
    {
        $dataClass = $this->dataClassFor($hlblock);

        $fields['UF_CREATED_AT'] = new DateTime();

        $result = $dataClass::add($fields);
        if (!$result->isSuccess()) {
            throw new \RuntimeException(
                sprintf('Failed to insert feedback into «%s»: %s', $hlblock->value, implode('; ', $result->getErrorMessages()))
            );
        }
        return (int) $result->getId();
    }

    /**
     * @return class-string
     */
    private function dataClassFor(HlblockCode $hlblock): string
    {
        \Bitrix\Main\Loader::includeModule('highloadblock');

        $row = HighloadBlockTable::query()
            ->where('NAME', $hlblock->value)
            ->setSelect(['ID', 'NAME', 'TABLE_NAME'])
            ->setLimit(1)
            ->exec()
            ->fetch();

        if (!$row) {
            throw new \RuntimeException("HL-блок «{$hlblock->value}» не найден");
        }
        return HighloadBlockTable::compileEntity($row)->getDataClass();
    }
}
