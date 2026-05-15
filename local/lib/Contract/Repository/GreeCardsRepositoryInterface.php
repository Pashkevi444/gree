<?php

declare(strict_types=1);

namespace Gree\Contract\Repository;

use Gree\Collection\GreeCardCollection;
use Gree\Collection\GreeStatCollection;
use Gree\Enum\IblockCode;

interface GreeCardsRepositoryInterface
{
    /**
     * Read «benefit» cards (icon + title + description) from an iblock identified
     * by the given code. Works for any "Why Gree" cards iblock — home/brand/catalog —
     * because they share the same schema (NAME_RU/EN, PREVIEW_TEXT_RU/EN, ICON_CODE).
     */
    public function getCards(IblockCode $code): GreeCardCollection;

    /**
     * Read «stat» rows (number + prefix/suffix + description) from an iblock
     * identified by the given code. Schema:
     *   NAME_RU/EN, PREVIEW_TEXT_RU/EN, NUMBER_VALUE, NUMBER_PREFIX_RU/EN, NUMBER_SUFFIX_RU/EN.
     */
    public function getStats(IblockCode $code): GreeStatCollection;
}
