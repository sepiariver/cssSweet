<?php

namespace CssSweet\v2\Traits;

trait Modifier
{

    public function process()
    {
        $input = $this->getOption('input');
        $options = $this->getOption('options');
        if (empty($input)) {
            return;
        }
        return $this->modify($input, $options);
    }

    protected function modify($input, $options)
    {
        return $input;
    }
}
