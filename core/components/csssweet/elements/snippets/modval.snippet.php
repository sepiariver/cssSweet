<?php
/*
 * modval
 *
 * Output modifier that accepts a numeric value and modifies it.
 * Identifies strings as units and separates them.
 *
 * Examples:
 * [[modval?input=`4px`&options=`*3`]]
 * '12px'
 *
 * [[+color:modval=`/2`]]
 * Where the value of the placeholder is '18 inches'
 * '9inches'
 */
// Grab the cssSweet class
$cssSweetPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
$cssSweetPath .= 'model/csssweet/';
$csssweet = $modx->getService('csssweet', 'CssSweet', $cssSweetPath);
if (!$csssweet || !($csssweet instanceof CssSweet)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[cssSweet.modval] could not load the required csssweet class!');
    return '';
}

return (new \CssSweet\v2\Snippet\Modify($csssweet, [
    'input' => $input,
    'options' => $options,
]))->process();