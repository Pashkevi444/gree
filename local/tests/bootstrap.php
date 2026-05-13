<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Bitrix stubs for unit tests
if (!class_exists(\Bitrix\Main\Data\Cache::class)) {
    class_alias(\Gree\Tests\Stub\BitrixDataCache::class, \Bitrix\Main\Data\Cache::class);
}

if (!class_exists(\Bitrix\Main\Loader::class)) {
    class_alias(\Gree\Tests\Stub\BitrixLoader::class, \Bitrix\Main\Loader::class);
}

if (!class_exists(\Bitrix\Iblock\IblockTable::class)) {
    class_alias(\Gree\Tests\Stub\BitrixIblockTable::class, \Bitrix\Iblock\IblockTable::class);
}

if (!class_exists(\Bitrix\Main\Type\ParameterDictionary::class)) {
    class_alias(\Gree\Tests\Stub\BitrixParameterDictionary::class, \Bitrix\Main\Type\ParameterDictionary::class);
}

if (!class_exists(\Bitrix\Main\HttpRequest::class)) {
    class_alias(\Gree\Tests\Stub\BitrixHttpRequest::class, \Bitrix\Main\HttpRequest::class);
}

if (!class_exists(\Bitrix\Main\Application::class)) {
    class_alias(\Gree\Tests\Stub\BitrixApplication::class, \Bitrix\Main\Application::class);
}
