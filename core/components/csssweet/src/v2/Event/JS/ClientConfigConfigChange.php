<?php
namespace CssSweet\v2\Event\JS;

use CssSweet\v2\Event\Event;
use CssSweet\v2\Traits\JS;

class ClientConfigConfigChange extends Event
{
    use JS;
    public function run()
    {
        $this->rebuildJs();
    }
}