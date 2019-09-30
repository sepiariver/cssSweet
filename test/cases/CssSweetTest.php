<?php

use PHPUnit\Framework\TestCase;

class CssSweetTest extends TestCase
{
    protected $projectPath;
    protected $modx;
    protected $cssSweet;
    protected $scssphp;
    protected $jShrink;
    
    protected function setUp(): void
    {
        $this->projectPath = dirname(dirname(dirname(__FILE__)));
        require_once($this->projectPath . '/test/config.core.php');
        require_once(MODX_CORE_PATH . 'model/modx/modx.class.php');
        $this->modx = new modX();
        require_once($this->projectPath . '/core/components/csssweet/model/csssweet/csssweet.class.php');
        $this->cssSweet = new CssSweet($this->modx);

        $this->scssphp = $this->cssSweet->scssphpInit([], 'Nested');
        $this->jShrink = $this->cssSweet->jshrinkInit();
    }
    public function testInstantiation()
    {
        $this->assertTrue($this->modx instanceof modX);
        $this->assertTrue($this->cssSweet instanceof CssSweet);
    }
    public function testInit()
    {   
        $this->assertTrue($this->scssphp instanceof \ScssPhp\ScssPhp\Compiler);
        $this->assertTrue($this->jShrink instanceof \JShrink\Minifier);
    }

    public function testCompile()
    {
        $scss = file_get_contents('assets/test.scss');
        $expectedcss = file_get_contents('assets/expected.css');
        $compiledcss = $this->scssphp->compile($scss);

        $this->assertSame($expectedcss, $compiledcss);

        $js = file_get_contents('assets/test.js');
        $expectedjs = file_get_contents('assets/expected.js');
        $compiledjs = $this->jShrink->minify($js);

        $this->assertSame($expectedjs, $compiledjs);
    }
}
