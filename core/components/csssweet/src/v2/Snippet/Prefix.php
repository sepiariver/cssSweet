<?php
namespace CssSweet\v2\Snippet;

use CssSweet\v2\Traits\Modifier;

class Prefix extends Snippet
{
    use Modifier;
    public function modify($input, $options)
    {
        // Get input
        $input = isset($input) ? $input : '';
// If $to property is set, use that instead
        $input = (isset($to)) ? $to : $input;
// Check it
        if (empty($input)) {
            return;
        }

// Get options and defaults
        $options = isset($options) ? $options : 'webkit,moz';
        if ($options === 'all') {
            $options = 'webkit,moz,ms,o';
        }

// Which prefix?
        $prefixes = ['webkit', 'moz', 'ms', 'o'];
        $output = '';
        $selects = explode(',', $options);
        foreach ($selects as $select) {
            if (in_array($select, $prefixes)) {
                $output .= "-$select-$input" . PHP_EOL;
            }
        }
        $output .= $input;
        return $output;
    }
}