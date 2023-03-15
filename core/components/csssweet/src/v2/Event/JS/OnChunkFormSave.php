<?php
namespace CssSweet\v2\Event\JS;

use CssSweet\v2\Event\Event;
use CssSweet\v2\Traits\JS;

class OnChunkFormSave extends Event
{
    use JS;
    public function run()
    {
        $chunks = $this->cs->explodeAndClean($this->getOption('js_chunks'));
        $chunk = $this->getOption('chunk');
        if (!isset($chunk) || !in_array($chunk->get('name'), $chunks)) {
            return;
        }
        $this->rebuildJs();
    }
}