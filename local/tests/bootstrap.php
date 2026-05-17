<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

/**
 * Bootstrap mode is picked from the `GREE_TEST_INTEGRATION` env var.
 *
 *   - `=1`     → загрузить настоящий Bitrix-prolog. `App::get(...)` отдаёт
 *                боевые репозитории, `TransactionService` гоняет реальные
 *                START TRANSACTION / ROLLBACK по живой БД. Запуск:
 *                `composer test:integration`.
 *
 *   - не задано → юнит-режим. Подмешиваем class_alias-стабы, чтобы тесты
 *                гонялись без живой БД. Запуск: `composer test` или
 *                `composer test:unit`.
 *
 * Подмешивать оба режима нельзя: `class_alias` захватывает имена `\Bitrix\…`
 * и настоящие классы к ним больше не подгрузить.
 */

/**
 * Authoritative switch: загрузить Bitrix-пролог, если
 *   - явно выставили `GREE_TEST_INTEGRATION=1` (composer test:integration),
 *   - ИЛИ пользователь вручную попросил Integration-сьют через CLI:
 *     `phpunit --testsuite Integration` / `phpunit local/tests/Integration/...`.
 *
 * Без аргументов `./vendor/bin/phpunit` идёт в Unit-режим (defaultTestSuite),
 * стабы класс-алиаситсятся, БД не нужна.
 */
$integrationMode = getenv('GREE_TEST_INTEGRATION') === '1';
if (!$integrationMode) {
    $argv = $_SERVER['argv'] ?? [];
    foreach ($argv as $i => $arg) {
        if (str_contains($arg, 'local/tests/Integration')) {
            $integrationMode = true;
            break;
        }
        if ($arg === '--testsuite' && (($argv[$i + 1] ?? '') === 'Integration')) {
            $integrationMode = true;
            break;
        }
        if (preg_match('/^--testsuite[=]?\s*Integration/i', $arg)) {
            $integrationMode = true;
            break;
        }
    }
}

if ($integrationMode) {
    $root = dirname(__DIR__, 2);
    $_SERVER['DOCUMENT_ROOT'] = $root;

    // Bitrix `tools.php` использует короткие теги `<?`. PHP 8.4 их по
    // умолчанию выключает — парсер падает с «unclosed brace». Опцию нельзя
    // менять рантаймом (PHP_INI_PERDIR), поэтому либо запускать через
    // `composer test:integration` (он подкидывает `-d short_open_tag=On`),
    // либо самому: `php -d short_open_tag=On vendor/bin/phpunit ...`.
    // Падение в этом месте полезнее, чем нечитаемый Parse-error дальше.
    if (!ini_get('short_open_tag')) {
        fwrite(
            STDERR,
            "Integration suite requires short_open_tag=On (Bitrix tools.php).\n"
            . "  Run via composer:        composer test:integration\n"
            . "  Or pass -d вручную:      php -d short_open_tag=On vendor/bin/phpunit --testsuite Integration\n",
        );
        exit(2);
    }

    if (!defined('NO_KEEP_STATISTIC')) {
        define('NO_KEEP_STATISTIC', true);
    }
    if (!defined('NO_AGENT_STATISTIC')) {
        define('NO_AGENT_STATISTIC', true);
    }
    if (!defined('NOT_CHECK_PERMISSIONS')) {
        define('NOT_CHECK_PERMISSIONS', true);
    }
    // `LANGUAGE_ID` обычно определяет prolog_before.php — мы его не зовём,
    // объявим вручную. Highloadblock-таблицы читают эту константу для
    // подбора локализованных меток полей; без неё падают на add/select.
    if (!defined('LANGUAGE_ID')) {
        define('LANGUAGE_ID', 'ru');
    }
    if (!defined('SITE_ID')) {
        define('SITE_ID', 's1');
    }

    // MySQL socket для CLI-mysqli. Bitrix берёт `host=localhost` из
    // `.settings.php`, что превращается в unix-сокет. CLI-php в macOS/Linux
    // часто не знает, где этот сокет — нужен явный путь. Сначала смотрим
    // env-override `GREE_TEST_MYSQL_SOCKET`, потом перебираем известные
    // дефолты (MAMP, brew, Linux-distro).
    $socketCandidates = array_filter([
        getenv('GREE_TEST_MYSQL_SOCKET') ?: null,
        '/Applications/MAMP/tmp/mysql/mysql.sock',
        '/tmp/mysql.sock',
        '/var/run/mysqld/mysqld.sock',
        '/var/lib/mysql/mysql.sock',
        '/opt/homebrew/var/mysql/mysql.sock',
    ]);
    foreach ($socketCandidates as $candidate) {
        if (file_exists($candidate)) {
            ini_set('mysqli.default_socket', $candidate);
            ini_set('pdo_mysql.default_socket', $candidate);
            break;
        }
    }

    // Полный Bitrix-пролог. С `short_open_tag=On` (выставлен в composer-скрипте
    // `test:integration`) обфусцированная лицензионная проверка в `tools.php`
    // парсится корректно — никакой пляски с ручной инициализацией
    // $APPLICATION / $USER_FIELD_MANAGER / Context не нужно, пролог делает всё
    // сам.
    //
    // Эпилог нам не нужен — он рассчитан на HTTP shutdown и в CLI цепляет
    // диагностические хэндлеры, которые падают на shutdown в момент уничтожения
    // autoloader'а. Закрытие БД-коннекшена сделает деструктор Application.
    //
    // Bitrix в CLI плюёт лицензионный HTML в stdout — оборачиваем в буфер,
    // чтобы не сломать репорт PHPUnit'а.
    ob_start();
    try {
        require_once $root . '/bitrix/modules/main/include/prolog_before.php';
    } finally {
        ob_end_clean();
    }

    // Bitrix-пролог цепляет свой ExceptionHandler, который под CLI/PHPUnit
    // ломается в самой обработке ошибок (LogFormatter падает на `new DateTime`
    // в момент уничтожения autoloader'а) и съедает реальный exception. С
    // дефолтным PHP-хэндлером stack trace будет настоящий — для тестов он
    // важнее bitrix-овской файловой записи.
    restore_exception_handler();
    restore_error_handler();
    return;
}

// ── Unit-mode stubs ─────────────────────────────────────────────────────────
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
