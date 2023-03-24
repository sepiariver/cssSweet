<?php
/*
 * prefix
 *
 * Output modifier that adds basic browser prefixes to $input strings
 *
 * Examples:
 * [[+my_radius_css:prefix]]
 * Where the value of the placeholder is 'border-radius: 3px;'
 * -webkit-border-radius: 3px;
 * -moz-border-radius: 3px;
 * border-radius: 3px;
 *
 * [[prefix?to=`transition: all 300ms ease;` &options=`all`]]
 * -webkit-transition: all 300ms ease;
 * -moz-transition: all 300ms ease;
 * -ms-transition: all 300ms ease;
 * -o-transition: all 300ms ease;
 * transition: all 300ms ease;
 */
// Grab the cssSweet class
$cssSweetPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
$cssSweetPath .= 'model/csssweet/';
$csssweet = $modx->getService('csssweet', 'CssSweet', $cssSweetPath);
if (!$csssweet || !($csssweet instanceof CssSweet)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[cssSweet.prefix] could not load the required csssweet class!');
    return '';
}

return (new \CssSweet\v2\Snippet\Prefix($csssweet, [
    'input' => $input,
    'options' => $options,
]))->process();