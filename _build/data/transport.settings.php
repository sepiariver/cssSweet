<?php

$s = array(
    'custom_css_path' => '{assets_path}components/csssweet/',
    'custom_css_chunk' => 'csss.custom.css',
    'custom_css_filename' => 'csss-custom.css',
    'minify_custom_css' => true,
    'strip_css_comment_blocks' => false,
);

$settings = array();

foreach ($s as $key => $value) {
    if (is_string($value) || is_int($value)) {
        $type = 'textfield';
    } elseif (is_bool($value)) {
        $type = 'combo-boolean';
    } else {
        $type = 'textfield';
    }

    $parts = explode('.', $key);
    if (count($parts) == 1) {
        $area = 'Default';
    } else {
        $area = $parts[0];
    }

    $settings['csss.' . $key] = $modx->newObject('modSystemSetting');
    $settings['csss.' . $key]->set('key', 'csss.' . $key);
    $settings['csss.' . $key]->fromArray(array(
        'value' => $value,
        'xtype' => $type,
        'namespace' => 'cssSweet',
        'area' => $area
    ));
}

return $settings;
