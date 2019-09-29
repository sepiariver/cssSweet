<?php

use PHPUnit\Framework\TestCase;

class CssSweetTest extends TestCase
{
    protected $projectPath;
    protected $modx;
    protected $cssSweet;
    protected $scssphp;
    
    protected function setUp(): void
    {
        $this->projectPath = dirname(dirname(dirname(__FILE__)));
        require_once($this->projectPath . '/test/config.core.php');
        require_once(MODX_CORE_PATH . 'model/modx/modx.class.php');
        $this->modx = new modX();
        require_once($this->projectPath . '/core/components/csssweet/model/csssweet/csssweet.class.php');
        $this->cssSweet = new CssSweet($this->modx);

        $this->scssphp = $this->cssSweet->scssphpInit([], 'Nested');
    }
    public function testInstantiation()
    {
        $this->assertTrue($this->modx instanceof modX);
        $this->assertTrue($this->cssSweet instanceof CssSweet);
    }
    public function testInit()
    {   
        $this->assertTrue($this->scssphp instanceof \ScssPhp\ScssPhp\Compiler);
    }

    public function testCompile()
    {
        $scss = file_get_contents('cases/test.scss');
        $expected = file_get_contents('cases/expected.css');
        $compiled = $this->scssphp->compile($scss);

        $this->assertSame($expected, $compiled);
    }
}
