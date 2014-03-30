<?php
/**
 * Default properties for the lighten output modifier
 *
 * @package cssSweet
 * @subpackage build
 */
$properties = array(
    array(
        'name' => 'csss.custom_css_chunk',
        'desc' => 'Name of chunk to parse custom CSS.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'csss.custom.css',
    ),
    array(
        'name' => 'csss.custom_css_filename',
        'desc' => 'Name of file to output custom CSS.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'csss-custom.css',
    ),
    array(
        'name' => 'csss.custom_css_path',
        'desc' => 'Optionally override the path in system settings.',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => 'csss.minify_custom_css',
        'desc' => 'Enable CSS minify on output.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '1',
    ),
    array(
        'name' => 'csss.strip_css_comment_blocks',
        'desc' => 'Strips CSS comment blocks on output.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '0',
    ),
);

return $properties;
