<?php
namespace CssSweet\v2\Snippet;

use CssSweet\v2\Traits\Modifier;

class Saturate extends Snippet
{
    use Modifier;
    public function modify($input, $options)
    {
        // Set color class
        $cc = $this->cs->getColorClass($input);
        if (!$cc['color']) {
            return '';
        }
        $format = $cc['format'];
        $unHash = $cc['unHash'];
        $color = $cc['color'];

        // Clean options
        if (empty($options)) {
            return $color;
        }

        // Saturate
        $perc = intval($options);
        $result = ($perc >= 0) ? $color->saturate($perc) : $color->desaturate(abs($perc));

        // return processed hex color value
        if ($unHash && (strpos($result, '#') === 0)) {
            $result = substr($result, 1);
        }

        return $result;
    }
}