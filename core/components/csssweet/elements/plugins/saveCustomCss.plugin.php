<?php
/**
 * saveCustomCss
 * @author @sepiariver
 * Copyright 2013 - 2015 by YJ Tso <yj@modx.com> <info@sepiariver.com>
 *
 * saveCustomCss and cssSweet is free software;
 * you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation;
 * either version 2 of the License, or (at your option) any later version.
 *
 * saveCustomCss and cssSweet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * saveCustomCss and cssSweet; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package cssSweet
 *
 */

// Never fire on the front end
if ($modx->context->get('key') !== 'mgr') return;

// In case the wrong event is enabled in plugin properties
$allowedEvents = array('OnSiteRefresh', 'OnChunkFormSave', 'ClientConfig_ConfigChange');
if (!in_array($modx->event->name, $allowedEvents)) return;

// Grab the cssSweet class
$csssweet = null;
$cssSweetPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
$cssSweetPath .= 'model/csssweet/';
if (file_exists($cssSweetPath . 'csssweet.class.php')) $csssweet = $modx->getService('csssweet', 'CssSweet', $cssSweetPath);

if (!$csssweet || !($csssweet instanceof CssSweet)) {

    $modx->log(modX::LOG_LEVEL_ERROR, '[SaveCustomCss] could not load the required csssweet class!');
    return;
}

// Dev mode option
$mode = $modx->getOption('dev_mode', $scriptProperties, 'custom', true);
// Letting folks know what's going on
$modx->log(modX::LOG_LEVEL_INFO, 'saveCustomCss plugin is running in mode: ' . $mode);

// Override properties with mode props
$properties = $csssweet->getProperties($scriptProperties, $mode);

// Specify a comma-separated list of chunk names in plugin properties
$chunks = $csssweet->explodeAndClean($modx->getOption('scss_chunks', $properties, ''));
// If no chunk names specified, there's nothing to do.
if (empty($chunks)) {
    $modx->log(modX::LOG_LEVEL_WARN, 'No chunks were set in the saveCustomCss plugin property scss_chunks. No action performed.');
    return;
}

// Don't run this for every ChunkSave event
if ($modx->event->name === 'OnChunkFormSave' && !in_array($chunk->get('name'), $chunks)) return;

// Specify an output file name in plugin properties
$filename = $modx->getOption('css_filename', $properties, '');
if (empty($filename)) return;

// Optionally choose an output format if not minified
$css_output_format = $modx->getOption('css_output_format', $properties, 'Expanded');
$css_output_format_options = array('Expanded', 'Nested', 'Compact');
if (!in_array($css_output_format, $css_output_format_options)) $css_output_format = 'Expanded';

// Optionally minify the output, defaults to 'true'
$minify_custom_css = (bool) $modx->getOption('minify_custom_css', $properties, true);
$css_output_format = ($minify_custom_css) ? 'Compressed' : $css_output_format;

// Strip CSS comment blocks; defaults to 'false'
$strip_comments = (bool) $modx->getOption('strip_css_comment_blocks', $properties, false);
$css_output_format = ($minify_custom_css && $strip_comments) ? 'Crunched' : $css_output_format;

// Optionally set base_path for scss imports
$scss_import_paths = $modx->getOption('scss_import_paths', $properties, '');
$scss_import_paths = (empty($scss_import_paths)) ? array() : $csssweet->explodeAndClean($scss_import_paths);

// Get the output path; construct fallback; log for debugging
$csssCustomCssPath = $modx->getOption('css_path', $properties, '');
if (empty($csssCustomCssPath)) $csssCustomCssPath = $modx->getOption('assets_path') . 'components/csssweet/' . $mode . '/';
$csssCustomCssPath = rtrim($csssCustomCssPath, '/') . '/';

$checkDir = $csssweet->checkDir($csssCustomCssPath, 'csssweet.saveCustomCss');
if ($checkDir['success']) {
    $modx->log(modX::LOG_LEVEL_WARN, $checkDir['message']);
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, '$csssCustomJsPath error: ' . $checkDir['message']);
    return;
}

// Initialize settings array
$settings = array();

// Get context settings
$settings_ctx = $modx->getOption('context_settings_context', $properties, '');
if (!empty($settings_ctx)) {
    $settings_ctx = $modx->getContext($settings_ctx);
    if ($settings_ctx && is_array($settings_ctx->config)) $settings = array_merge($settings, $settings_ctx->config);
}

// Attempt to get Client Config settigs
$settings = $csssweet->getClientConfigSettings($settings);

/* Make settings available as [[++tags]] */
$modx->setPlaceholders($settings, '+');

// Parse chunk with $settings array
$contents = $csssweet->processChunks($chunks, $settings);
// If there's no result, what's the point?
if (empty($contents)) return;

// CSS comments
$contents = '/* Contents generated by MODX - this file will be overwritten. */' . PHP_EOL . $contents;
// The scssphp parser keeps comments with !
if (!$strip_comments) $contents = str_replace('/*', '/*!', $contents);

// Define target file
$file = $csssCustomCssPath . $filename;

// Init scssphp
$scssMin = $csssweet->scssphpInit($scss_import_paths, $css_output_format);
if ($scssMin) {
    try {
        $contents = $scssMin->compile($contents);
    } catch (Exception $e) {
        $modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage() . ' scss not compiled. minification not performed.', '', 'saveCustomCss');
    }
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Failed to load scss class. scss not compiled. minification not performed.', '', 'saveCustomCss');
}

// If we failed scss and minification at least output what we have
file_put_contents($file, $contents);
if (file_exists($file) && is_readable($file)) $modx->log(modX::LOG_LEVEL_INFO, 'Success! Custom CSS saved to file "' . $file . '"', '', 'saveCustomCss');