<?php

use OzdemirBurak\Iris\Exceptions\InvalidColorException;
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

    public function testIris()
    {
        $irisHex = $this->cssSweet->getIris('#ff00ff');
        $this->assertTrue($irisHex instanceof \OzdemirBurak\Iris\Color\Hex);
        $irisRgb = $this->cssSweet->getIris('rgb(255, 0, 255)', 'rgb');
        $this->assertTrue($irisRgb instanceof \OzdemirBurak\Iris\Color\Rgb);
        $this->assertEquals($irisHex->toRgb(), $irisRgb);

        $hex = $this->cssSweet->getIris('#333');
        $this->assertEquals($hex->lighten(20), '#666666');
        $this->assertEquals($hex->darken(20), '#000000');
        $this->assertEquals($hex->brighten(20), '#666666');
    }
    public function testLightening()
    {
        $this->assertEquals($this->cssSweet->lightening('#333', '20'), '#666666');
        $this->assertEquals($this->cssSweet->lightening('rgb(51,51,51)', '20'), 'rgb(125,125,125)');
        $this->assertEquals($this->cssSweet->lightening('#333', '-20'), '#000000');
        $this->assertEquals($this->cssSweet->lightening('rgb(51,51,51)', '-20'), 'rgb(23,23,23)');
        $this->assertEquals($this->cssSweet->lightening('333', '20'), '666666');
        $this->assertEquals($this->cssSweet->lightening('333', '-20'), '000000');
        $this->assertEquals($this->cssSweet->lightening('#333', 'max'), '#000000');
        $this->assertEquals($this->cssSweet->lightening('#333', 'rev'), '#ffffff');
        $this->assertEquals($this->cssSweet->lightening('#aaa', 'max'), '#ffffff');
        $this->assertEquals($this->cssSweet->lightening('#aaa', 'rev'), '#000000');
        $this->assertEquals($this->cssSweet->lightening('#000', 'rev50'), '#808080');
        $this->assertEquals($this->cssSweet->lightening('#fff', 'rev50'), '#808080');
        $this->assertEquals($this->cssSweet->lightening('#zzz', '20'), '');
        $this->assertEquals($this->cssSweet->lightening('#333', '0'), '#333333');
        $this->assertEquals($this->cssSweet->lightening('#333', 'foobar'), '#333333');
        
    }
}
