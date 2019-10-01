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

return $csssweet->converting($input, $options);