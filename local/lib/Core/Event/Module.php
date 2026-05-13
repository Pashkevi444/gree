<?php

declare(strict_types=1);

namespace Gree\Core\Event;

use Bitrix\Main\Loader;

final class Module
{
    public static function onBeforeProlog(): void
    {
        Loader::includeModule('iblock');

        // Reset options cache so each request gets fresh values.
        \Gree\Core\Options::reset();
    }

    public static function onAdminIblockElementEditForm(): void
    {
        global $APPLICATION;

        if (!$APPLICATION->GetCurPage() || !str_contains($APPLICATION->GetCurPage(), 'iblock_element_edit.php')) {
            return;
        }

        $APPLICATION->AddHeadScript('/local/js/admin-iblock-form.js');
    }

    public static function clearTaggedCache(array $arFields): void
    {
        if (empty($arFields['IBLOCK_ID'])) {
            return;
        }

        \Bitrix\Main\Application::getInstance()
            ->getTaggedCache()
            ->clearByTag('iblock_id_' . $arFields['IBLOCK_ID']);
    }
}
