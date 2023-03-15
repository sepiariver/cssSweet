<?php
namespace CssSweet\v2\Event\CSS;

use CssSweet\v2\Event\Event;
use CssSweet\v2\Traits\CSS;

class ClientConfigConfigChange extends Event
{
    use CSS;
    public function run()
    {
        $this->rebuildCss();
    }
}