<?php

declare(strict_types=1);

namespace Gree\Tests\Unit\Core;

use Gree\Core\BitrixBladeEngine;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use PHPUnit\Framework\TestCase;

final class BitrixBladeEngineTest extends TestCase
{
    private string $tmpDir;
    private BitrixBladeEngine $engine;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/gree_blade_test_' . uniqid();
        mkdir($this->tmpDir, 0755, true);

        $files    = new Filesystem();
        $compiler = new BladeCompiler($files, $this->tmpDir);
        $this->engine = new BitrixBladeEngine($compiler, $files);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpDir . '/*') ?: []);
        rmdir($this->tmpDir);
    }

    public function testEvaluatePathOutputsDirectlyAndReturnsEmptyString(): void
    {
        $tpl = $this->tmpDir . '/plain.php';
        file_put_contents($tpl, '<?php echo "hello"; ?>');

        ob_start();
        $result = $this->engine->get($tpl, []);
        $output = ob_get_clean();

        $this->assertSame('', $result, 'evaluatePath must return empty string');
        $this->assertSame('hello', $output, 'template output must go directly to current OB');
    }

    public function testExtractedVariablesAreAvailableInTemplate(): void
    {
        $tpl = $this->tmpDir . '/vars.php';
        file_put_contents($tpl, '<?php echo $greeting . " " . $name; ?>');

        ob_start();
        $this->engine->get($tpl, ['greeting' => 'Hello', 'name' => 'World']);
        $output = ob_get_clean();

        $this->assertSame('Hello World', $output);
    }
}
