<?php
namespace CssSweet\v2\Snippet;

use CssSweet\v2\Traits\CSS;
use CssSweet\v2\Traits\Modifier;

class Extract extends Snippet
{
    use CSS;
    use Modifier;
    public function modify($input, $options)
    {
        // Set color class
        $cc = $this->getColorClass($input);
        if (!$cc['color']) {
            return '';
        }
        $color = $cc['color'];

        // Channel map
        $channels = [
            'red' => 0,
            'green' => 1,
            'blue' => 2,
            'alpha' => 3,
            'r' => 0,
            'g' => 1,
            'b' => 2,
            'a' => 3,
            '0' => 0,
            '1' => 1,
            '2' => 2,
            '3' => 3,
            'hue' => 0,
            'saturation' => 1,
            'lightness' => 2,
            'value' => 2,
            'h' => 0,
            's' => 1,
            'l' => 2,
            'v' => 2
        ];

        // Clean options
        // Harder to troubleshoot if a color is returned here?
        if (empty($options)) {
            return '';
        }
        $o = (string) trim($options);
        if (!isset($channels[$o])) {
            return '';
        }

        $i = $channels[$o];
        $values =  $color->values();
        return $values[$i];
    }
}