<?php
/* 
 * saturate
 *
 * Output modifier that accepts a color value and changes saturation. 
 *
 * Examples:
 * [[saturate? &input=`#80e61a` &options=`20`]]
 * '#80ff00'
 *
 * [[+color:saturate=`-20`]]
 * Where the value of the placeholder is 'rgb(128,230,26)'
 * 'rgb(128,204,51)'
 */

// Get values
if (empty($input)) return '';

// Grab the cssSweet class
$csssweet = null;
$cssSweetPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
$cssSweetPath .= 'model/csssweet/';
if (file_exists($cssSweetPath . 'csssweet.class.php')) $csssweet = $modx->getService('csssweet', 'CssSweet', $cssSweetPath);
if (!$csssweet || !($csssweet instanceof CssSweet)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[cssSweet.convert] could not load the required csssweet class!');
    return '';
}

return $csssweet->saturating($input, $options);