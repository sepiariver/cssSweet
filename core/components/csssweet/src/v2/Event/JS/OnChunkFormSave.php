<?php
namespace CssSweet\v2\Event\JS;

use CssSweet\v2\Event\Event;
use CssSweet\v2\Traits\JS;

class OnChunkFormSave extends Event
{
    use JS;
    public function run()
    {
        $mode = $this->modx->getOption('dev_mode', $this->sp, 'custom', true);
        $properties = $this->cs->getProperties($this->sp, $mode);
        $chunks = $this->cs->explodeAndClean($this->modx->getOption('js_chunks', $properties, ''));
        $chunk = $this->getOption('chunk');
        if (!isset($chunk) || !in_array($chunk->get('name'), $chunks)) {
            return;
        }
        $this->modx->log(\modX::LOG_LEVEL_INFO, 'CssSweet rebuilding on saving ' . $chunk->get('name'));
        $this->rebuildJs();
    }
}
