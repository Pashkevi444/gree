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

        // Reset options cache so each request gets fresh values.
        \Gree\Core\Options::reset();

        // Auto-detect locale on first visit (CIS Accept-Language → ru, else → en),
        // store it in the session — subsequent requests respect the stored value.
        \Gree\Core\App::container()
            ->get(\Gree\Contract\Service\LanguageServiceInterface::class)
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
