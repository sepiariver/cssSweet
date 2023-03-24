<?php
namespace CssSweet\v2\Snippet;

use CssSweet\v2\Traits\CSS;
use CssSweet\v2\Traits\Modifier;

class Convert extends Snippet
{
    use CSS;
    use Modifier;
    public function modify($input, $options)
    {
        // Set color class
        $cc = $this->getColorClass(trim($input));
        if (!$cc['color']) {
            return '';
        }
        $format = $cc['format'];
        $color = $cc['color'];

        // Clean options
        $options = ucfirst(strtolower(trim($options)));
        if (empty($options) || $options === $format) {
            return $color;
        }

        // Convert
        switch ($options) {
            case 'Rgb':
                return $color->toRgb();
                break;
            case 'Rgba':
                return $color->toRgba();
                break;
            case 'Hsl':
                return $color->toHsl();
                break;
            case 'Hsla':
                return $color->toHsla();
                break;
            case 'Hsv':
                return $color->toHsv();
                break;
            case 'Hex':
            default:
                return $color->toHex();
                break;
        }
    }
}