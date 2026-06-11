<?php

declare(strict_types=1);

namespace Gree\Core\Event;

use Bitrix\Main\Loader;

final class Module
{
    public static function onBeforeProlog(): void
    {
        Loader::includeModule('iblock');
        Loader::includeModule('highloadblock');

        \Gree\Core\Options::reset();

        // Auto-detect locale при первом визите; дальше сидит в сессии.
        \Gree\Core\App::get(\Gree\Contract\Service\LanguageServiceInterface::class)
            ->detectAndStore(\Bitrix\Main\Application::getInstance()->getContext()->getRequest());
    }

    public static function onAdminIblockElementEditForm(): void
    {
        global $APPLICATION;

        if (!$APPLICATION->GetCurPage() || !str_contains($APPLICATION->GetCurPage(), 'iblock_element_edit.php')) {
            return;
        }

        $APPLICATION->AddHeadScript('/local/js/admin-iblock-form.js');
    }

    /** @param array<string, mixed> $arFields  OnAfterIBlockElementAdd/Update/Delete — в $arFields всегда есть IBLOCK_ID. */
    public static function onIblockElementChanged(array $arFields): void
    {
        self::invalidateIblockCache((int) ($arFields['IBLOCK_ID'] ?? 0));
    }

    /** OnAfterIBlockElementSetPropertyValuesEx: ($elementId, $iblockId, ...). Свойства правятся из админки именно этим путём. */
    public static function onIblockPropertyValuesChanged(mixed $elementId, mixed $iblockId): void
    {
        self::invalidateIblockCache((int) $iblockId);
    }

    /** Сбрасывает D7 query-кеш (репо setCacheTtl) и тегированный iblock_id_X — иначе правки из админки отстают на TTL. */
    private static function invalidateIblockCache(int $iblockId): void
    {
        if ($iblockId <= 0) {
            return;
        }

        try {
            Loader::includeModule('iblock');
            // wakeUp бросает для инфоблоков без API_CODE — событие летит для всех, не только наших.
            $entityClass = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
            if ($entityClass !== null) {
                $entityClass::getEntity()->cleanCache();
            }
        } catch (\Throwable) {
            // Не наш iblock — D7-кеша по нему нет.
        }

        \Bitrix\Main\Application::getInstance()
            ->getTaggedCache()
            ->clearByTag('iblock_id_' . $iblockId);
    }
}
