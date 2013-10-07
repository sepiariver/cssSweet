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
$input = isset($input) ? $input : null;
if( !$input ) return;
$options = isset($options) ? $options : '0';

// Default method for percentage calculations. Can be 'input', 'spectrum', or 'average'
$availOpts = array('input','spectrum','average');
$percOpt = $modx->getOption('percOpt',$scriptProperties,'input');
if ( !in_array($percOpt, $availOpts) ) $percOpt = 'input';

// Default threshold color for additional options
$threshold = $modx->getOption('threshold',$scriptProperties,'999999');
if( !preg_match('/[a-fA-F0-9]{6}/',$threshold) ) $threshold = '999999';

// Set comparison values for additional options
$inputDec = hexdec($input);
$threshold = hexdec($threshold);
if( $inputDec > $threshold ) { 
  $lightInput = true;
  $darkInput = false;
} else {
  $darkInput = true;
  $lightInput = false;
}

// Set additional options
preg_match('/(max)/',$options,$max);
preg_match('/(rev)/',$options,$rev);
$options = preg_replace('/[^0-9-]/','',$options);

// check stuff
if( substr($input, 0, 1) === '#' ) $input = substr($input, 1, 6);
$len = strlen($input);
if( $len !== 3 && $len !== 6 ) return '/* 3 or 6 hex characters required */';
$bits = str_split($input);
foreach($bits as $bit) { 
    if( !preg_match('/[0-9a-fA-F]/',$bit) ) return "/* invalid hex character '$bit' */";
}

// make color constituents
if( $len === 3 ) {
    $arr[0] = $bits[0] . $bits[0];
    $arr[1] = $bits[1] . $bits[1];
    $arr[2] = $bits[2] . $bits[2];
}
if( $len === 6 ) {
    $arr[0] = $bits[0] . $bits[1];
    $arr[1] = $bits[2] . $bits[3];
    $arr[2] = $bits[4] . $bits[5];
}

// Shortcuts if using 'max' or 'rev' without percentage
if( !$options ) {
    if( $max ) {
        if( $lightInput ) return 'ffffff';
        if( $darkInput ) return '000000';
    }
    if( $rev ) {
        if( $lightInput ) return '000000';
        if( $darkInput ) return 'ffffff';
    }
} else {
    // If ($options) we'll need these 
    $dec = array();
    $perc = (intval($options,10) / 100);

    // These are for special cases
    if( $max || $rev ) {
        $perc = abs(min($perc,1));
        $wht = dechex(255 * $perc);
        $wht = str_pad($wht,2,'0',STR_PAD_LEFT); 
        $wht = str_repeat($wht,3); 
        $blk = dechex( ( 255 - (255 * $perc) ) ); 
        $blk = str_pad($blk,2,'0',STR_PAD_LEFT);
        $blk = str_repeat($blk,3);
    }
    if( $max ) {
        if( $lightInput ) return $wht;
        if( $darkInput ) return $blk;
    }
    if( $rev ) {
        if( $lightInput ) return $blk;
        if( $darkInput ) return $wht;
    }
}

// Lighten or darken the input
for($i=0;$i<3;$i++) {
    $dec = hexdec($arr[$i]);
    if( $percOpt === 'input') $amount = round($dec * $perc);
    if( $percOpt === 'spectrum') $amount = round(255 * $perc);
    if( $percOpt === 'average') $amount = round(($dec + 255) * ($perc / 2));
    $sum = $dec + $amount;

    if($sum > 255) { $result[$i] = '255'; }
    elseif($sum < 0) { $result[$i] = '0'; }
    else $result[$i] = $sum;

    $hex[$i] = dechex($result[$i]);
    $hex[$i] = str_pad($hex[$i],2,'0',STR_PAD_LEFT);
}

// return processed hex color value
return implode($hex);