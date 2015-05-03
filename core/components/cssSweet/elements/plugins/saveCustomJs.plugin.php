<?php
/**
 * saveCustomJs
 * @author @sepiariver
 * Copyright 2013 - 2015 by YJ Tso <yj@modx.com> <info@sepiariver.com>
 *
 * saveCustomJs and cssSweet is free software; 
 * you can redistribute it and/or modify it under the terms of the GNU General 
 * Public License as published by the Free Software Foundation; 
 * either version 2 of the License, or (at your option) any later version.
 *
 * saveCustomJs and cssSweet is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * saveCustomJs and cssSweet; if not, write to the Free Software Foundation, Inc., 
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package cssSweet
 *
 */

// In case the wrong event is enabled in plugin properties
if ($modx->event->name !== 'OnSiteRefresh' && $modx->event->name !== 'OnChunkFormSave') return;

// Dev mode option
$mode = ($modx->getOption('dev_mode', $scriptProperties, 0)) ? 'dev' : 'custom';
// Letting folks know what's going on
$modx->log(modX::LOG_LEVEL_INFO, 'saveCustomJs plugin is running in mode: ' . $mode);

// Specify a comma-separated list of chunk names in plugin properties
$chunks = array_filter(array_map('trim', explode(',', $modx->getOption($mode . '_js_chunks', $scriptProperties, ''))));
// If no chunk names specified, there's nothing to do.
if (empty($chunks)) {
    $modx->log(modX::LOG_LEVEL_WARN, 'No chunks were set in the saveCustomJs plugin property: ' . $mode . '_js_chunks. No action performed.');
    return;
}

// Don't run this for every ChunkSave event
if ($modx->event->name === 'OnChunkFormSave' && !in_array($chunk->get('name'), $chunks)) return;

// Specify an output file name in plugin properties
$filename = $modx->getOption($mode . '_js_filename', $scriptProperties, '');
if (empty($filename)) return;

// Optionally minify the output, defaults to 'true' 
$minify_custom_js = (bool) $modx->getOption('minify_custom_js', $scriptProperties, true);

// Strip comment blocks; defaults to 'false'
$strip_comments = (bool) $modx->getOption('strip_js_comment_blocks', $scriptProperties, false);
$preserve_comments = ($strip_comments) ? false : true;

// Get the output path; construct fallback; log for info/debugging
$csssCustomJsPath = $modx->getOption('custom_js_path', $scriptProperties, '');
if (empty($csssCustomJsPath)) $csssCustomJsPath = $modx->getOption('assets_path') . 'components/csssweet/js/';
$modx->log(modX::LOG_LEVEL_INFO, '$csssCustomJsPath is: ' . $csssCustomJsPath . ' on line: ' . __LINE__);
$csssCustomJsPath = rtrim($csssCustomJsPath, '/') . '/';

// If directory exists but isn't writable we have a problem, Houston
if (file_exists($csssCustomJsPath) && !is_writable($csssCustomJsPath)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'The directory at ' . $csssCustomJsPath . 'is not writable!');
    return;
}

// Check if directory exists, if not, create it
if (!file_exists($csssCustomJsPath)) {
    if (mkdir($csssCustomJsPath, 0755, true)) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Directory created at ' . $csssCustomJsPath);
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Directory could not be created at ' . $csssCustomJsPath);
        // We can't continue in this case
        return;
    }
}

// Grab the ClientConfig class
$ccPath = $modx->getOption('clientconfig.core_path', null, $modx->getOption('core_path') . 'components/clientconfig/');
$ccPath .= 'model/clientconfig/';
if (file_exists($ccPath . 'clientconfig.class.php')) $clientConfig = $modx->getService('clientconfig','ClientConfig', $ccPath);
$settings = array();

// If we got the class (which means it's installed properly), include the settings
if ($clientConfig && ($clientConfig instanceof ClientConfig)) {
    $settings = $clientConfig->getSettings();
    /* Make settings available as [[++tags]] */
    $modx->setPlaceholders($settings, '+');
} else { 
    $modx->log(modX::LOG_LEVEL_WARN, 'Failed to load ClientConfig class. ClientConfig settings not included. Check for MODX tags in output.'); 
}

// Parse chunk with $settings array
$contents = '';
foreach ($chunks as $current) {
    $processed = '';
    if ($current) {
        try {
            $modx->log(modX::LOG_LEVEL_INFO, 'Processing chunk: ' . $current);
            $processed = $modx->getChunk($current, $settings);
            if ($processed) {
                $contents .= $processed;
            } else {
                $err = '$modx->getChunk() failed on line: ' . __LINE__ . ' for chunk: ' . $current;
                throw new Exception($err);
            }
        } catch (Exception $err) {
            $modx->log(modX::LOG_LEVEL_ERROR, $err->getMessage());
        }
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to get Chunk ' . $current . '. Chunk contents not saved.');
    }
}
// If there's no result, what's the point?
if (empty($contents)) return;

// Comments
$contents = '/* Contents generated by MODX - this file will be overwritten. */' . PHP_EOL . $contents;
if ($preserve_comments) {
    // Add '!' token to preserve all comments
    $contents = str_replace(array('/*','/*!'), '/*!', $contents);
} else {
    // We discard flagged comments if the strip_js_comment_blocks property is true. Good idea or no?
    $contents = str_replace('/*!', '/*', $contents);
}

// Define target file
$file = $csssCustomJsPath . $filename;

// Status report
$status = 'not';

if ($minify_custom_js) {
    // Grab the JS minifier class
    $cssSweetLibsPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
    $cssSweetLibsPath .= 'model/cssSweet/libs/';
    $cssSweetjsMinFile = 'JShrink/Minifier.php';
    
    if (file_exists($cssSweetLibsPath . $cssSweetjsMinFile)) {
        include_once $cssSweetLibsPath . $cssSweetjsMinFile;
        $jshrink = new Minifier();
    }
    
    // If we got the class, try minification. Log failures.    
    if ($jshrink instanceof Minifier) {
    
        try {
            $contents = $jshrink::minify($contents, array('flaggedComments' => $preserve_comments));
            $status = '';
        } 
        catch (Exception $e) {
            $modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage() . '— js not compiled. Minification not performed.'); 
        }
    
    } else { 
        $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to load js Minifier class — js not compiled. Minification not performed.'); 
    }
}

// If we didnt' minify, output what we have
file_put_contents($file, $contents);
if (file_exists($file) && is_readable($file)) $modx->log(modX::LOG_LEVEL_INFO, 'Minification was '. $status . ' performed. Custom JS saved to file: ' . $file);