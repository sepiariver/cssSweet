<?php
namespace CssSweet\v2\Snippet;

use CssSweet\v2\Traits\CSS;
use CssSweet\v2\Traits\Modifier;

class Lighten extends Snippet
{
    use CSS;
    use Modifier;
    public function modify($input, $options)
    {
        // Set color class
        $cc = $this->cs->getColorClass($input);
        if (!$cc['color']) {
            return '';
        }
        $unHash = $cc['unHash'];
        $color = $cc['color'];

        // Set additional options
        preg_match('/(max)/', $options, $max);
        preg_match('/(rev)/', $options, $rev);
        $options = preg_replace('/[^0-9-]/', '', $options);

        // Light vs Dark
        $darkInput = $color->isDark();
        $lightInput = $color->isLight();

        // Shortcuts if using 'max' or 'rev' without percentage
        if (!$options) {
            // Default to 0 if no options percentage provided
            $perc = 0;
            $neg = false;
            // Set max/rev outputs
            $wht = ($unHash) ? 'ffffff' : '#ffffff';
            $blk = ($unHash) ? '000000' : '#000000';
            // Return for max/rev
            if ($max) {
                if ($lightInput) {
                    return $wht;
                }
                if ($darkInput) {
                    return $blk;
                }
            }
            if ($rev) {
                if ($lightInput) {
                    return $blk;
                }
                if ($darkInput) {
                    return $wht;
                }
            }
        } else {
            // If ($options) process percentage
            $perc = (intval($options, 10) / 100);
            $neg = ($perc <= 0);
            $perc = min(abs($perc), 1) * 100;
            // Set max/rev outputs
            $wht = $color->tint($perc);
            $blk = $color->shade($perc);
            // Return for max/rev
            if ($max) {
                if ($lightInput) {
                    return $wht;
                }
                if ($darkInput) {
                    return $blk;
                }
            }
            if ($rev) {
                if ($lightInput) {
                    return $blk;
                }
                if ($darkInput) {
                    return $wht;
                }
            }
        }

        // Lighten or darken the input
        $result = ($neg) ? $color->darken($perc) : $color->lighten($perc);

        // return processed hex color value
        if ($unHash && (strpos($result, '#') === 0)) {
            $result = substr($result, 1);
        }

        return $result;
    }
}