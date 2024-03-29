<?php
namespace CssSweet\v2\Event\CSS;

use CssSweet\v2\Event\Event;
use CssSweet\v2\Traits\CSS;

class OnChunkFormSave extends Event
{
    use CSS;
    public function run()
    {
        $mode = $this->modx->getOption('dev_mode', $this->sp, 'custom', true);
        $properties = $this->cs->getProperties($this->sp, $mode);
        $chunks = $this->cs->explodeAndClean($this->modx->getOption('scss_chunks', $properties, ''));
        $chunk = $this->getOption('chunk');
        if (!isset($chunk) || !in_array($chunk->get('name'), $chunks)) {
            return;
        }
        $this->modx->log(\modX::LOG_LEVEL_INFO, 'CssSweet rebuilding on saving ' . $chunk->get('name'));
        $this->rebuildCss();
    }
}
