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

// Get input
$input = isset($input) ? $input : '';

// If $to property is set, use that instead
$input = (isset($to)) ? $to : $input;

// Check it
if (empty($input)) return;

// Get options and defaults
$options = isset($options) ? $options : 'webkit,moz';
if ($options === 'all') $options = 'webkit,moz,ms,o';

// Which prefix?
$prefixes = ['webkit', 'moz', 'ms', 'o'];
$output = '';
$selects = explode(',', $options);
foreach ($selects as $select) {
    if (in_array($select, $prefixes)) $output .= "-$select-$input" . PHP_EOL;
}
$output .= $input;

return $output;