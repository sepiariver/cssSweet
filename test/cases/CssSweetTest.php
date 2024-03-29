<?php

use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use PHPUnit\Framework\TestCase;
use CssSweet\v2\Snippet\Convert;
use CssSweet\v2\Snippet\Extract;
use CssSweet\v2\Snippet\Lighten;
use CssSweet\v2\Snippet\Prefix;
use CssSweet\v2\Snippet\Saturate;

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
        $this->modx->initialize('web');
        require_once($this->projectPath . '/core/components/csssweet/model/csssweet/csssweet.class.php');
        $this->cssSweet = new CssSweet($this->modx);
    }
    public function testInstantiation()
    {
        $this->assertTrue($this->modx instanceof modX);
        $this->assertTrue($this->modx->context instanceof modContext);
        $this->assertEquals($this->modx->context->key, 'web');
        $this->assertTrue($this->cssSweet instanceof CssSweet);
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
    public function testModifying()
    {
        $this->assertEquals($this->cssSweet->modifying('10px', '+5'), '15px');
        $this->assertEquals($this->cssSweet->modifying('10px', '-5'), '5px');
        $this->assertEquals($this->cssSweet->modifying('10in', '*2'), '20in');
        $this->assertEquals($this->cssSweet->modifying('10in', '/2'), '5in');
        $this->assertEquals($this->cssSweet->modifying('-90deg', '*2'), '-180deg');
    }
    public function testConverting()
    {
        $this->assertEquals($this->cssSweet->converting('#333'), '#333333');
        $this->assertEquals($this->cssSweet->converting('#333', 'foobar'), '#333333');
        $this->assertEquals($this->cssSweet->converting('rgb(51,51,51)', 'hex'), '#333333');
        $this->assertEquals($this->cssSweet->converting('#333', 'rgb'), 'rgb(51,51,51)');
        $this->assertEquals($this->cssSweet->converting('#333', 'rgba'), 'rgba(51,51,51,1)');
        $this->assertEquals($this->cssSweet->converting('#333', 'hsla'), 'hsla(0,0%,20%,1)');
        $this->assertEquals($this->cssSweet->converting('#333', 'hsv'), 'hsv(0,0%,20%)');
    }
    public function testSaturating()
    {
        $this->assertEquals($this->cssSweet->saturating('#80e61a', 20), '#80ff00');
        $this->assertEquals($this->cssSweet->saturating('rgb(128,230,26)', -20), 'rgb(128,204,51)');
    }
    public function testExtracting()
    {
        $this->assertEquals($this->cssSweet->extracting('#80e61a', 'red'), '80');
        $this->assertEquals($this->cssSweet->extracting('#80e61a', 'g'), 'e6');
        $this->assertEquals($this->cssSweet->extracting('#80e61a', 'blue'), '1a');
        $this->assertEquals($this->cssSweet->extracting('rgba(128, 230, 26, 0.5)', 'a'), '0.5');
        $this->assertEquals($this->cssSweet->extracting('hsla(0, 0%, 20%, 1)', 'hue'), '0');
        $this->assertEquals($this->cssSweet->extracting('hsla(0, 0%, 20%, 1)', 's'), '0%');
        $this->assertEquals($this->cssSweet->extracting('hsla(0, 0%, 20%, 1)', 'l'), '20%');
        $this->assertEquals($this->cssSweet->extracting('hsla(0, 0%, 20%, 1)', 'alpha'), '1');
        $this->assertEquals($this->cssSweet->extracting('hsv(0,0%,20%)', 'value'), '20%');
    }
    public function testSnippets()
    {
        $this->assertEquals($this->modx->runSnippet('csssweet.convert', ['input' => 'rgb(51,51,51)', 'options' => 'hex']), '#333333');
        $this->assertEquals($this->modx->runSnippet('csssweet.extract', ['input' => 'rgba(51,51,51,0.5)', 'options' => 'alpha']), '0.5');
        $this->assertEquals($this->modx->runSnippet('csssweet.extract', ['input' => '#333', 'options' => 'r']), '33');
        $this->assertEquals($this->modx->runSnippet('csssweet.lighten', ['input' => '#333', 'options' => '20']), '#666666');
        $this->assertEquals($this->modx->runSnippet('csssweet.modval', ['input' => '10px', 'options' => '+5']), '15px');
        $this->assertEquals($this->modx->runSnippet('csssweet.saturate', ['input' => '#80e61a', 'options' => '20']), '#80ff00');
        $expected = addslashes('-webkit-transition: 300ms all ease;
-moz-transition: 300ms all ease;
-ms-transition: 300ms all ease;
-o-transition: 300ms all ease;
transition: 300ms all ease;');
        $this->assertEquals($this->modx->runSnippet('csssweet.prefix', ['input' => 'transition: 300ms all ease;', 'options' => 'all']), $expected);
    }
}
