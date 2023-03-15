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
// Grab the cssSweet class
$cssSweetPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
$cssSweetPath .= 'model/csssweet/';
$csssweet = $modx->getService('csssweet', 'CssSweet', $cssSweetPath);
if (!$csssweet || !($csssweet instanceof CssSweet)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[cssSweet.convert] could not load the required csssweet class!');
    return '';
}

return (new \CssSweet\v2\Snippet\Extract($csssweet, [
    'input' => $input,
    'options' => $options,
]))->process();
