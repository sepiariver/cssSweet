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
 * [[+inches:modval=`/2`]]
 * Where the value of the placeholder is '18 inches'
 * '9 inches'
 */

/* Get input: numbers go in an array, everything else is assumed
 * as a unit.
 */
$input = isset($input) ? $input : '';
if( !$input ) return;
preg_match('/([0-9.]+)/',$input,$valArr);
$unit = preg_replace('/([0-9.]+)/','',$input);

// Get options: numbers go in one array, operators in another
preg_match('/([0-9.]+)/',$options,$optValArr);
preg_match('/[\+\-\*\/]/',$options,$op);

// Default operator
if( !$op ) $op[0] = '+';

// Simple math only
if( $op[0] == '+' ) $val = ($valArr[0] + $optValArr[0]);
if( $op[0] == '-' ) $val = ($valArr[0] - $optValArr[0]);
if( $op[0] == '*' ) $val = ($valArr[0] * $optValArr[0]);
if( $op[0] == '/' ) $val = ($valArr[0] / $optValArr[0]);

// Results
return $val . $unit;