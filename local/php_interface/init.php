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
