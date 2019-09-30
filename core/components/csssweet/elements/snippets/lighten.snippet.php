<?php
/* 
 * lighten
 *
 * Output modifier that accepts a hex value and percentage (+ or -) option. 
 * Additionally, 'max' or 'rev' can be set, with or without a percentage. 
 *
 * Examples:
 * [[+color:lighten=`20`]]
 * Lightens the $input hex color by 20%
 *
 * [[+color:lighten=`-30`]]
 * Darkens the $input color by 30%
 *
 * [[+color:lighten=`max`]]
 * If the $input value is above the $threshold value, 'ffffff' will be
 * returned, else '000000' will be returned.
 *
 * [[+color:lighten=`rev60`]]
 * This would output the reverse of the $input hex (white or black) * 60%
 * (so the result would be more of a medium gray)
 *
 * Variables other than $options must be set in snippet properties tab if 
 * used as output modifier.
 *
 */

// Get values
if (empty($input)) return '';
$input = trim($input);
$options = isset($options) ? $options : '0';

// Grab the cssSweet class
$csssweet = null;
$cssSweetPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
$cssSweetPath .= 'model/csssweet/';
if (file_exists($cssSweetPath . 'csssweet.class.php')) $csssweet = $modx->getService('csssweet', 'CssSweet', $cssSweetPath);
if (!$csssweet || !($csssweet instanceof CssSweet)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[cssSweet.lighten] could not load the required csssweet class!');
    return '';
}

return $csssweet->lightening($input, $options);