<?php

declare(strict_types=1);

namespace Gree\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Hits the live site via cURL and asserts SEO is wired correctly.
 *
 * Why HTTP and not a Bitrix-bootstrapped functional test: the assertion target
 * is the rendered `<head>` block, which is the integration of HL block storage
 * + SeoService + BaseController::applySeo() + Bitrix's ShowHead(). Mocking any
 * one of those would defeat the purpose. cURL gives us exactly what the search
 * crawler sees.
 *
 * Skips itself when the dev server is unreachable so unit-only runs aren't
 * blocked. Override TEST_BASE_URL env to point at a different host.
 */
final class SeoHttpTest extends TestCase
{
    private string $baseUrl;
    private string $cookieJar;

    protected function setUp(): void
    {
        $this->baseUrl = rtrim((string) ($_ENV['TEST_BASE_URL'] ?? 'https://gree:8890'), '/');

        if (!$this->serverIsUp()) {
            $this->markTestSkipped('Dev server ' . $this->baseUrl . ' is not reachable');
        }

        // Fresh cookie jar per test, default locale = RU.
        // Server stores locale in session; sticky cookie jar carries it forward.
        $this->cookieJar = tempnam(sys_get_temp_dir(), 'gree-seo-test-');
        $this->fetch('/lang/ru/');
    }

    protected function tearDown(): void
    {
        if (isset($this->cookieJar) && is_file($this->cookieJar)) {
            @unlink($this->cookieJar);
        }
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     *   url → [path, expected title substring, expected description substring]
     *
     * URLs here are intentionally hardcoded — not built via Gree\Helpers\Route::to().
     * Integration tests sit at the public boundary and lock down what a real
     * user (or search engine) sees. If we resolved paths via Route::to(), a
     * rename of /cart/ → /basket/ inside web.php would silently make the test
     * pass while breaking every external link that points at /cart/. Hardcoded
     * paths force the test to fail loudly when the public contract changes,
     * which is the whole point of an integration suite.
     *
     * Substrings are picked to be specific enough to fail loudly if the wrong
     * SEO record is loaded, but not so brittle they break on copy tweaks in
     * admin.
     */
    public static function pageProvider(): array
    {
        return [
            'home'                  => ['/',                          'идеальные кондиционеры', 'Каждый третий кондиционер'],
            'catalog hub'           => ['/catalog/',                  'Каталог сплит-систем',   'Настенные'],
            'catalog wall'          => ['/catalog/nastennie/',        'настенных кондиционеров', 'до 80 м²'],
            'catalog column'        => ['/catalog/kolonnye/',         'колонных кондиционеров',  'до 200 м²'],
            'catalog industrial'    => ['/catalog/promyshlennye/',    'промышленных',            'климат-контроль'],
            'blog'                  => ['/blog/',                     'Блог Gree',               'Полезные советы'],
            'cart'                  => ['/cart/',                     'Корзина',                 ''],
        ];
    }

    #[DataProvider('pageProvider')]
    public function testStaticPageRendersSeoFromHlblock(string $path, string $titleNeedle, string $descNeedle): void
    {
        $html = $this->fetch($path);

        $title = $this->extractTitle($html);
        $description = $this->extractMeta($html, 'description');

        $this->assertNotSame('', $title, "page <title> is empty on $path");

        if ($titleNeedle !== '') {
            $this->assertStringContainsStringIgnoringCase(
                $titleNeedle,
                $title,
                "expected <title> on $path to contain '$titleNeedle'; got: '$title'"
            );
        }
        if ($descNeedle !== '') {
            $this->assertStringContainsStringIgnoringCase(
                $descNeedle,
                $description,
                "expected meta[description] on $path to contain '$descNeedle'; got: '$description'"
            );
        }
    }

    public function testHomePageRendersOpenGraphTags(): void
    {
        $html = $this->fetch('/');

        $ogTitle = $this->extractMetaProperty($html, 'og:title');
        $ogImage = $this->extractMetaProperty($html, 'og:image');

        $this->assertNotSame('', $ogTitle, 'og:title missing on home');
        $this->assertNotSame('', $ogImage, 'og:image missing on home');
        $this->assertStringStartsWith('/', $ogImage, 'og:image should be a site-relative path');
    }

    public function testEnglishLocaleUsesEnglishSeoRecord(): void
    {
        $this->fetch('/lang/en/');
        $html = $this->fetch('/');

        $title = $this->extractTitle($html);
        $this->assertStringContainsStringIgnoringCase(
            'perfect air conditioners',
            $title,
            "EN home <title> should use the English SEO record; got: '$title'"
        );
    }

    /**
     * Issues a GET and returns the response body. The cookie jar is shared
     * across the whole test, so /lang/* persistence works.
     */
    private function fetch(string $path): string
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
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertSame(200, $code, "GET $path returned HTTP $code");
        $this->assertIsString($body);
        return $body;
    }

    private function serverIsUp(): bool
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

    private function extractTitle(string $html): string
    {
        return preg_match('#<title>(.*?)</title>#is', $html, $m)
            ? trim(html_entity_decode($m[1]))
            : '';
    }

    private function extractMeta(string $html, string $name): string
    {
        $name = preg_quote($name, '#');
        if (preg_match('#<meta\s+name="' . $name . '"\s+content="([^"]*)"#i', $html, $m)) {
            return html_entity_decode($m[1]);
        }
        return '';
    }

    private function extractMetaProperty(string $html, string $property): string
    {
        $property = preg_quote($property, '#');
        if (preg_match('#<meta\s+property="' . $property . '"\s+content="([^"]*)"#i', $html, $m)) {
            return html_entity_decode($m[1]);
        }
        return '';
    }
}
