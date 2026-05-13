<?php

define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_CHECK', true);
define('NO_AGENT_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

/** @var \CMain $APPLICATION */
$APPLICATION->SetTitle('Страница не найдена');

header('HTTP/1.1 404 Not Found');

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Gree\Core\Blade;

$html = Blade::factory()->make('errors.404')->render();
echo $html;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
