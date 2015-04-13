<?php
/**
 * saveCustomCss
 * @author @sepiariver
 * Copyright 2013 by YJ Tso <yj@modx.com>
 *
 * saveCustomCss and cssSweet is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * saveCustomCss and cssSweet is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * saveCustomCss and cssSweet; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package cssSweet
 *
 * TO DOs: change file_exists check on line 66 to is_dir? Better check on last line OR more accurate return notice.
 */
// Dev mode option
$mode = ($modx->getOption('dev_mode', $scriptProperties, 0)) ? 'dev' : 'custom';

// In case the wrong event is enabled in plugin properties
if ($modx->event->name !== 'OnSiteRefresh' && $modx->event->name !== 'OnChunkFormSave') return;

// Optionally a comma-separated list of chunk names can be specified in plugin properties
$chunks = array_map('trim', explode(',', $modx->getOption($mode . '_css_chunk', $scriptProperties, 'csss.custom.css')));

// Don't run this for every ChunkSave event
if ($modx->event->name === 'OnChunkFormSave' && !in_array($chunk->get('name'), $chunks)) return;

// Optionally a file name can be specified in plugin properties
$filename = $modx->getOption($mode . '_css_filename', $scriptProperties, 'csss.compiled.css');

// Optionally minify the output, defaults to 'true' **Must be enabled for SCSS processing
$minify_custom_css = (bool) $modx->getOption('minify_custom_css', $scriptProperties, true);

// Optionally choose an output format if not minified
$css_output_format = $modx->getOption('css_output_format', $scriptProperties, 'standard');
$css_output_format = ($css_output_format === 'nested') ? 'scss_formatter_nested' : 'scss_formatter';
$css_output_format = ($minify_custom_css) ? 'scss_formatter_compressed' : $css_output_format;

// strips CSS comment blocks, defaults to 'false'
$strip_comments = (bool) $modx->getOption('strip_css_comment_blocks', $scriptProperties, false);
$preserve_comments = ($strip_comments) ? false : true;

// optionally set base_path for css imports
$scss_import_paths = $modx->getOption('scss_import_paths', $scriptProperties, '');
$scss_import_paths = (empty($scss_import_paths)) ? array() : array_map('trim', explode(',', $scss_import_paths));

// Construct path from system settings - can be set in properties as of v.1.1
$csssCustomCssPath = $modx->getOption('custom_css_path', $scriptProperties, '');
if (empty($csssCustomCssPath)) $csssCustomCssPath = $modx->getOption('assets_path') . 'components/csssweet/';
$modx->log(modX::LOG_LEVEL_INFO, '$csssCustomCssPath is: ' . $csssCustomCssPath . ' on line: ' . __LINE__);
$csssCustomCssPath = rtrim($csssCustomCssPath, '/') . '/';

// Grab the ClientConfig class
$ccPath = $modx->getOption('clientconfig.core_path', null, $modx->getOption('core_path') . 'components/clientconfig/');
$ccPath .= 'model/clientconfig/';
if (file_exists($ccPath . 'clientconfig.class.php')) $clientConfig = $modx->getService('clientconfig','ClientConfig', $ccPath);
$settings = array();

// If we got the class (which means it's installed properly), include the settings
if ($clientConfig instanceof ClientConfig) {
    $settings = $clientConfig->getSettings();
    /* Make settings available as [[++tags]] */
    $modx->setPlaceholders($settings, '+');
} else { 
    $modx->log(modX::LOG_LEVEL_WARN, 'Failed to load ClientConfig class. ClientConfig settings not included.','','saveCustomCssClientConfig'); 
}

// If directory exists but isn't writable we have a problem, Houston
if (file_exists($csssCustomCssPath) && !is_writable($csssCustomCssPath)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'The directory at ' . $csssCustomCssPath . 'is not writable!','','saveCustomCss');
    return;
}
    
// Check if directory exists, if not, create it
if (!file_exists($csssCustomCssPath)) {
    if (mkdir($csssCustomCssPath, 0755, true)) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Directory created at ' . $csssCustomCssPath, '', 'saveCustomCss');
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Directory could not be created at ' . $csssCustomCssPath, '', 'saveCustomCss');
        return;
    }
}

// Parse chunk with $settings array
$contents = '';
foreach ($chunks as $current) {
    $processed = '';
    if ($current) $processed = $modx->getChunk($current, $settings);
    if ($processed) { 
        $contents .= $processed;    
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to get Chunk ' . $current . '. Chunk contents not saved.', '', 'saveCustomCss');
    }
}
// If there's no result, what's the point?
if (empty($contents)) return;

// CSS comments
$contents = '/* Contents generated by MODX - this file will be overwritten. */' . PHP_EOL . $contents;
if ($preserve_comments) $contents = str_replace('/*', '/*!', $contents);

// Define target file
$file = $csssCustomCssPath . $filename;

// Output
$cssSweetLibsPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
$cssSweetLibsPath .= 'model/cssSweet/libs/';
$cssSweetcssMinFile = 'scssphp/scss.inc.php';
    
if (file_exists($cssSweetLibsPath . $cssSweetcssMinFile)) {
    include_once $cssSweetLibsPath . $cssSweetcssMinFile;
    $scssMin = new scssc();
}
    
if ($scssMin instanceof scssc) {

    try {
        $scssMin->setImportPaths($scss_import_paths);
        $scssMin->setFormatter($css_output_format);
        $contents = $scssMin->compile($contents);
    } 
    catch (Exception $e) {
        $modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage() . ' scss not compiled. minification not performed.','','saveCustomCss'); 
    }
        
} else { 
        $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to load scss class. scss not compiled. minification not performed.','','saveCustomCss'); 
}

file_put_contents($file, $contents);
if (file_exists($file) && is_readable($file)) $modx->log(modX::LOG_LEVEL_INFO, 'Success! Custom CSS saved to file "' . $file . '"', '', 'saveCustomCss');