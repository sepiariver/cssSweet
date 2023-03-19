<?php

/*
 * lighten
 *
 * Output modifier that accepts a color value in any supported format and
 * percentage (+ or -) option.
 * Additionally, 'max' or 'rev' can be set, with or without a percentage,
 * translating into a tint or shade.
 *
 * Examples:
 * [[+color:lighten=`20`]]
 * Lightens the $input color by 20%
 *
 * [[+color:lighten=`-30`]]
 * Darkens the $input color by 30%
 *
 * [[+color:lighten=`max`]]
 * If the $input value is dark, 'ffffff' will be returned,
 * else '000000' will be returned. Accepts '#' prefix or without.
 *
 * [[+color:lighten=`rev60`]]
 * This would tint or shade the $input color by 60%
 * (so the result would be more of a medium gray)
 *
 * Variables other than $options must be set in snippet properties tab if
 * used as output modifier.
 *
 */
// Grab the cssSweet class
$cssSweetPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
$cssSweetPath .= 'model/csssweet/';
$csssweet = $modx->getService('csssweet', 'CssSweet', $cssSweetPath);
if (!$csssweet || !($csssweet instanceof CssSweet)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[cssSweet.convert] could not load the required csssweet class!');
    return '';
}

return (new \CssSweet\v2\Snippet\Lighten($csssweet, [
    'input' => $input,
    'options' => $options,
]))->process();
