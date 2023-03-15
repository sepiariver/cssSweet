<?php
namespace CssSweet\v2\Event\JS;

use CssSweet\v2\Event\Event;
use CssSweet\v2\Traits\JS;

class OnDocFormSave extends Event
{
    use JS;
    public function run()
    {
        $resource = $this->getOption('resource');
        if (!isset($resource) || !$resource instanceof \modResource) {
            return;
        }
        if ($resource->get('contentType') !== 'text/javascript') {
            return;
        }
        $this->rebuildJs();
    }
}