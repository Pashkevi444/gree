<?php

declare(strict_types=1);

namespace Gree\Tests\Integration;

use Gree\Contract\DB\TransactionServiceInterface;
use Gree\Core\App;
use PHPUnit\Framework\TestCase;

/**
 * Базовый класс интеграционных тестов.
 *
 * Bootstrap: настоящий Bitrix-prolog (см. `local/tests/bootstrap.php`,
 * срабатывает по env `GREE_TEST_INTEGRATION=1`). Запуск:
 *
 *     composer test:integration
 *
 * Что даёт база:
 *
 *   1. **Транзакция-обёртка вокруг каждого теста.** setUp() стартует
 *      `TransactionService::startTransaction()`, tearDown() катит её назад.
 *      Любые записи через наши репозитории (`OrderRepository::insert`,
 *      `CartItemRepository::insert` и т. д.) делаются на том же DB-коннекшене
 *      Bitrix-приложения и автоматически откатываются. **Никакого ручного
 *      cleanup'а** — БД остаётся чистой после прогона.
 *
 *   2. **Доступ к настоящему DI-контейнеру** через `App::get(...)`. Тесты
 *      зовут реальные сервисы / репозитории, как продакшен.
 *
 *   3. **cURL-помощники** (`fetch`, `jsonPost`, `send`) — для тестов, которые
 *      проверяют HTTP-слой (рендер головы, маршруты). Транзакция не покроет
 *      записи в веб-процессе — он отдельный, со своим коннекшеном — поэтому
 *      такие тесты должны быть **read-only** (статика, SEO, хлебные крошки).
 *      Любые тесты, которые мутируют БД, идут in-process через
 *      `App::get(SomeService::class)->method(...)`.
 */
abstract class IntegrationTestCase extends TestCase
{
    protected string $baseUrl;
    protected string $cookieJar;
    private ?TransactionServiceInterface $tx = null;

    protected function setUp(): void
    {
        $this->baseUrl = rtrim((string) ($_ENV['TEST_BASE_URL'] ?? 'https://gree:8890'), '/');
        $this->cookieJar = tempnam(sys_get_temp_dir(), 'gree-test-');

        // Bitrix должен быть забутстрапен — гоняем `composer test:integration`.
        if (!class_exists(\Bitrix\Main\Application::class, false)
            || !method_exists(\Bitrix\Main\Application::class, 'getConnection')
        ) {
            $this->markTestSkipped(
                'Integration suite requires Bitrix bootstrap. Run `composer test:integration` '
                . 'or set GREE_TEST_INTEGRATION=1 before phpunit.',
            );
        }

        $this->tx = App::get(TransactionServiceInterface::class);
        try {
            $this->tx->startTransaction();
        } catch (\Bitrix\Main\DB\ConnectionException $e) {
            // CLI часто не может подключиться к MySQL через сокет `localhost` —
            // FPM-сокет и CLI-сокет различаются (особенно под docker/ddev/colima).
            // Скипаем с понятным сообщением, не валим весь прогон.
            $this->tx = null;
            $this->markTestSkipped(
                'MySQL connect failed: ' . $e->getMessage() . '. '
                . 'CLI обычно не видит сокет, который слушает FPM. '
                . 'Поправь в bitrix/.settings.php "host" с "localhost" на "127.0.0.1" '
                . 'или запускай тесты из того же окружения, где живёт веб-сервер.',
            );
        }
    }

    protected function tearDown(): void
    {
        if ($this->tx !== null) {
            try {
                $this->tx->rollbackTransaction();
            } catch (\Throwable) {
                // tearDown не должен мешать репорту другого падения.
            }
        }
        if (isset($this->cookieJar) && is_file($this->cookieJar)) {
            @unlink($this->cookieJar);
        }
    }

    // ── HTTP helpers (для read-only тестов) ─────────────────────────────────

    protected function fetch(string $path): string
    {
        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'Gree-Integration-Test/1.0',
            CURLOPT_COOKIEJAR      => $this->cookieJar,
            CURLOPT_COOKIEFILE     => $this->cookieJar,
        ]);
        $body = curl_exec($ch);
        curl_close($ch);
        return is_string($body) ? $body : '';
    }

    /**
     * @param array<int, string> $extraHeaders
     * @return array{0:int, 1:string}
     */
    protected function send(string $method, string $path, ?string $body = null, array $extraHeaders = []): array
    {
        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'Gree-Integration-Test/1.0',
            CURLOPT_COOKIEJAR      => $this->cookieJar,
            CURLOPT_COOKIEFILE     => $this->cookieJar,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_HTTPHEADER     => $extraHeaders,
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $response = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$code, is_string($response) ? $response : ''];
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<int, string>   $extraHeaders
     * @return array{0:int, 1:string}
     */
    protected function jsonPost(string $path, array $payload, array $extraHeaders = []): array
    {
        return $this->send(
            'POST',
            $path,
            json_encode($payload),
            array_merge([
                'Content-Type: application/json',
                'Accept: application/json',
                'Origin: ' . $this->baseUrl,
            ], $extraHeaders),
        );
    }

    protected function isServerReachable(): bool
    {
        $ch = curl_init($this->baseUrl . '/');
        curl_setopt_array($ch, [
            CURLOPT_NOBODY         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT        => 5,
        ]);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code >= 200 && $code < 500;
    }
}
