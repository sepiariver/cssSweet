<?php
/**
 * Default properties for the lighten output modifier
 *
 * @package cssSweet
 * @subpackage build
 */
$properties = array(
    array(
        'name' => 'percOpt',
        'desc' => 'Can be \'input\', \'spectrum\' or \'average\'.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'input',
    ),
    array(
        'name' => 'threshold',
        'desc' => 'Valid, 6-character hex against which to evaluate light and dark hex values.',
        'type' => 'textfield',
        'options' => '',
        'value' => '999999',
    ),
);

return $properties;