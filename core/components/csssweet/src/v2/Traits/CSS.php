<?php

namespace CssSweet\v2\Traits;

use ScssPhp\ScssPhp\Exception\SassException;

trait CSS
{
    /** @var \CssSweet */
    protected $cs;

    /** @var \modX */
    protected $modx;

    /** @var array */
    protected $sp = [];

    protected function rebuildCss(): void
    {
        $this->debug('Rebuilding CSS');
        // Dev mode option
        $mode = $this->modx->getOption('dev_mode', $this->sp, 'custom', true);
        // Letting folks know what's going on
        $this->modx->log(\modX::LOG_LEVEL_INFO, 'saveCustomCss plugin is running in mode: ' . $mode);

        // Override properties with mode props
        $properties = $this->cs->getProperties($this->sp, $mode);

        // Specify a comma-separated list of chunk names in plugin properties
        $chunks = $this->cs->explodeAndClean($this->modx->getOption('scss_chunks', $properties, ''));
        // If no chunk names specified, there's nothing to do.
        if (empty($chunks)) {
            $this->modx->log(
                \modX::LOG_LEVEL_WARN,
                'No chunks were set in the saveCustomCss plugin property scss_chunks. No action performed.'
            );
            return;
        }

        // Specify an output file name in plugin properties
        $filename = $this->modx->getOption('css_filename', $properties, '');
        if (empty($filename)) {
            return;
        }

        // Optionally choose an output format if not minified
        $css_output_format = $this->modx->getOption('css_output_format', $properties, 'Expanded');
        $css_output_format_options = array('Expanded', 'expanded', 'Compressed', 'compressed');
        if (!in_array($css_output_format, $css_output_format_options)) {
            $css_output_format = 'Expanded';
        }

        // Optionally minify the output, defaults to 'true'
        $minify_custom_css = (bool) $this->modx->getOption('minify_custom_css', $properties, true);
        $css_output_format = ($minify_custom_css) ? 'compressed' : $css_output_format;

        // Strip CSS comment blocks; defaults to 'false'
        $strip_comments = (bool) $this->modx->getOption('strip_css_comment_blocks', $properties, false);
        $css_output_format = ($minify_custom_css && $strip_comments) ? 'compressed' : $css_output_format;

        // Optionally set base_path for scss imports
        $scss_import_paths = $this->modx->getOption('scss_import_paths', $properties, '');
        $scss_import_paths = (empty($scss_import_paths)) ? array() : $this->cs->explodeAndClean($scss_import_paths);

        // Get the output path; construct fallback; log for debugging
        $csssCustomCssPath = $this->modx->getOption('css_path', $properties, '');
        if (empty($csssCustomCssPath)) {
            $csssCustomCssPath = $this->modx->getOption('assets_path') . 'components/csssweet/' . $mode . '/';
        }
        $csssCustomCssPath = rtrim($csssCustomCssPath, '/') . '/';

        $checkDir = $this->cs->checkDir($csssCustomCssPath, 'csssweet.saveCustomCss');
        if ($checkDir['success']) {
            $this->modx->log(\modX::LOG_LEVEL_WARN, $checkDir['message']);
        } else {
            $this->modx->log(\modX::LOG_LEVEL_ERROR, '$csssCustomJsPath error: ' . $checkDir['message']);
            return;
        }

        // Initialize settings array
        $settings = array();

        // Get context settings
        $settings_ctx = $this->modx->getOption('context_settings_context', $properties, '');
        if (!empty($settings_ctx)) {
            $settings_ctx = $this->modx->getContext($settings_ctx);
            if ($settings_ctx && is_array($settings_ctx->config)) {
                $settings = array_merge($settings, $settings_ctx->config);
            }
        }

        // Attempt to get Client Config settigs
        $settings = $this->cs->getClientConfigSettings($settings);

        /* Make settings available as [[++tags]] */
        $this->modx->setPlaceholders($settings, '+');

        // Parse chunk with $settings array
        $contents = $this->cs->processChunks($chunks, $settings);
        // If there's no result, what's the point?
        if (empty($contents)) {
            return;
        }

        // CSS comments
        $contents = '/* Contents generated by MODX - this file will be overwritten. */' . PHP_EOL . $contents;
        // The scssphp parser keeps comments with !
        if (!$strip_comments) {
            $contents = str_replace('/*', '/*!', $contents);
        }

        // Define target file
        $file = $csssCustomCssPath . $filename;

        // Init scssphp
        $scssMin = $this->cs->scssphpInit($scss_import_paths, $css_output_format);
        if ($scssMin) {
            try {
                $contents = $scssMin->compileString($contents);
            } catch (SassException $e) {
                $this->modx->log(
                    \modX::LOG_LEVEL_ERROR,
                    $e->getMessage() . ' scss not compiled. minification not performed.',
                    '',
                    'saveCustomCss'
                );
            }
        } else {
            $this->modx->log(
                \modX::LOG_LEVEL_ERROR,
                'Failed to load scss class. scss not compiled. minification not performed.',
                '',
                'saveCustomCss'
            );
        }

        // If we failed scss and minification at least output what we have
        file_put_contents($file, $contents->getCss());
        if (file_exists($file) && is_readable($file)) {
            $this->modx->log(
                \modX::LOG_LEVEL_INFO,
                'Success! Custom CSS saved to file "' . $file . '"',
                '',
                'saveCustomCss'
            );
        }
    }
}