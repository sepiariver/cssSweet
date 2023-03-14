<?php

/*
 * extract
 *
 * Output modifier that accepts a color value and extracts
 * a channel.
 *
 * Examples:
 * [[extract? &input=`#80e61a` &options=`red`]]
 * '80'
 *
 * [[+color:extract=`a`]]
 * Where the value of the placeholder is 'rgba(51,51,51,0.5)'
 * '0.5'
 */

// Get values
if (empty($input)) {
    return '';
}

// Grab the cssSweet class
$csssweet = null;
$cssSweetPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
$cssSweetPath .= 'model/csssweet/';
if (file_exists($cssSweetPath . 'csssweet.class.php')) {
    $csssweet = $modx->getService('csssweet', 'CssSweet', $cssSweetPath);
}
if (!$csssweet || !($csssweet instanceof CssSweet)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[cssSweet.extract] could not load the required csssweet class!');
    return '';
}

return $csssweet->extracting($input, $options);
