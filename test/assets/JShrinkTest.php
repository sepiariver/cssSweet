<?php

use PHPUnit\Framework\TestCase;

class JShrinkTest extends TestCase
{
    protected $projectPath;
    protected $jShrink;

    protected function setUp(): void
    {
        $this->projectPath = dirname(dirname(dirname(__FILE__)));
        require_once($this->projectPath . '/core/components/csssweet/model/vendor/autoload.php');
        $this->jShrink = new JShrink\Minifier();
    }
    public function testInstantiation()
    {
        $this->assertTrue($this->jShrink instanceof \JShrink\Minifier);
    }

    public function testCompile()
    {
        $js = file_get_contents('assets/jquery.js');
        $expected = file_get_contents('assets/jquery.min.js');
        $compiled = $this->jShrink->minify($js);
        file_put_contents($compiled, 'assets/compiled.js');
        $this->assertSame($expected, $compiled);
    }
}
