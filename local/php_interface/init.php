<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

\Gree\Core\Env::load(dirname(__DIR__, 2) . '/.env');

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'main',
    'OnBeforeProlog',
    static fn() => \Gree\Core\Event\Module::onBeforeProlog()
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'main',
    'OnEpilog',
    static fn() => \Gree\Core\Event\Module::onAdminIblockElementEditForm()
);

// ── Инвалидация ORM-кеша при правках инфоблоков ────────────────────────────
// Репозитории кешируют D7-запросы (setCacheTtl), а админка правит элементы
// старым API, которое про D7-кеш не знает. Без этих хуков правка из админки
// не видна на сайте до часа (TTL). См. Module::invalidateIblockCache().
foreach (['OnAfterIBlockElementAdd', 'OnAfterIBlockElementUpdate', 'OnAfterIBlockElementDelete'] as $iblockEvent) {
    \Bitrix\Main\EventManager::getInstance()->addEventHandler(
        'iblock',
        $iblockEvent,
        static fn(array $arFields) => \Gree\Core\Event\Module::onIblockElementChanged($arFields)
    );
}
\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'iblock',
    'OnAfterIBlockElementSetPropertyValuesEx',
    static fn($elementId, $iblockId) => \Gree\Core\Event\Module::onIblockPropertyValuesChanged($elementId, $iblockId)
);
