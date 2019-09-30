<?php
use PHPUnit\Framework\TestCase;

class SCSSPHPTest extends TestCase
{
    protected $projectPath;
    protected $scssphp;

    protected function setUp(): void
    {
        $this->projectPath = dirname(dirname(dirname(__FILE__)));
        require_once($this->projectPath . '/core/components/csssweet/model/vendor/autoload.php');
        $this->scssphp = new ScssPhp\ScssPhp\Compiler();
    }
    public function testInstantiation()
    {
        $this->assertTrue($this->scssphp instanceof \ScssPhp\ScssPhp\Compiler);
    }

    public function testCompile()
    {
        $scss = file_get_contents('assets/test.scss');
        $expected = file_get_contents('assets/expected.css');
        $compiled = $this->scssphp->compile($scss);

        $this->assertSame($expected, $compiled);
    }
}