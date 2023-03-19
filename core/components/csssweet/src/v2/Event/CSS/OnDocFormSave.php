<?php
namespace CssSweet\v2\Event\CSS;

use CssSweet\v2\Event\Event;
use CssSweet\v2\Traits\CSS;

class OnDocFormSave extends Event
{
    use CSS;
    public function run()
    {
        $resource = $this->getOption('resource');
        if (!isset($resource) || !$resource instanceof \modResource) {
            return;
        }
        if ($resource->get('contentType') !== 'text/css') {
            return;
        }
        $this->rebuildCss();
    }
}