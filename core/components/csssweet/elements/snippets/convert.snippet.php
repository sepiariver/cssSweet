<?php
/*
 * convert
 *
 * Output modifier that accepts a color value and converts it to
 * another format.
 *
 * Examples:
 * [[convert? &input=`#333` &options=`rgba`]]
 * 'rgba(51,51,51,1)'
 *
 * [[+color:convert]]
 * Where the value of the placeholder is 'rgba(51,51,51,1)'
 * '#333333'
 */
// Grab the cssSweet class
$cssSweetPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
$cssSweetPath .= 'model/csssweet/';
$csssweet = $modx->getService('csssweet', 'CssSweet', $cssSweetPath);
if (!$csssweet || !($csssweet instanceof CssSweet)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[cssSweet.convert] could not load the required csssweet class!');
    return '';
}

return (new \CssSweet\v2\Snippet\Convert($csssweet, [
    'input' => $input,
    'options' => $options,
]))->process();