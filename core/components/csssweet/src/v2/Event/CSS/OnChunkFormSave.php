<?php
namespace CssSweet\v2\Event\CSS;

use CssSweet\v2\Event\Event;
use CssSweet\v2\Traits\CSS;

class OnChunkFormSave extends Event
{
    use CSS;
    public function run()
    {
        $chunks = $this->cs->explodeAndClean($this->getOption('scss_chunks'));
        $chunk = $this->getOption('chunk');
        if (!isset($chunk) || !in_array($chunk->get('name'), $chunks)) {
            return;
        }
        $this->modx->log(\modX::LOG_LEVEL_INFO, 'CssSweet rebuilding on saving ' . $chunk->get('name'));
        $this->rebuildCss();
    }
}