<?php

namespace CssSweet\v2\Traits;

trait JS
{
    /** @var \CssSweet */
    protected $cs;

    /** @var \modX */
    protected $modx;

    /** @var array */
    protected $sp = [];

    protected function rebuildJs(): void
    {
        // Dev mode option
        $mode = $this->modx->getOption('dev_mode', $this->sp, 'custom', true);
        // Letting folks know what's going on
        $this->modx->log(\modX::LOG_LEVEL_INFO, 'saveCustomJs plugin is running in mode: ' . $mode);

        // Override properties with mode props
        $properties = $this->cs->getProperties($this->sp, $mode);

        // Specify a comma-separated list of chunk names in plugin properties
        $chunks = $this->cs->explodeAndClean($this->modx->getOption('js_chunks', $properties, ''));
        // If no chunk names specified, there's nothing to do.
        if (empty($chunks)) {
            $this->modx->log(
                \modX::LOG_LEVEL_WARN,
                'No chunks were set in the saveCustomJs plugin property js_chunks. No action performed.'
            );
            return;
        }

        // Specify an output file name in plugin properties
        $filename = $this->modx->getOption('js_filename', $properties, '');
        if (empty($filename)) {
            return;
        }

        // Optionally minify the output, defaults to 'true'
        $minify_custom_js = (bool) $this->modx->getOption('minify_custom_js', $properties, true);

        // Strip comment blocks; defaults to 'false'
        $strip_comments = (bool) $this->modx->getOption('strip_js_comment_blocks', $properties, false);
        $preserve_comments = !$strip_comments;

        // Get the output path; construct fallback; log for info/debugging
        $csssCustomJsPath = $this->modx->getOption('js_path', $properties, '');
        if (empty($csssCustomJsPath)) {
            $csssCustomJsPath = $this->modx->getOption('assets_path') . 'components/csssweet/' . $mode . '/js/';
        }
        $csssCustomJsPath = rtrim($csssCustomJsPath, '/') . '/';

        $checkDir = $this->cs->checkDir($csssCustomJsPath, 'csssweet.saveCustomJs');

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

        // Comments
        $contents = '/* Contents generated by MODX - this file will be overwritten. */' . PHP_EOL . $contents;
        if ($preserve_comments) {
            // Add '!' token to preserve all comments
            $contents = str_replace(array('/*', '/*!'), '/*!', $contents);
        } else {
            // We discard flagged comments if the strip_js_comment_blocks property is true. Good idea or no?
            $contents = str_replace('/*!', '/*', $contents);
        }

        // Define target file
        $file = $csssCustomJsPath . $filename;

        // Status report
        $status = 'not';
        if ($minify_custom_js) {
            $jshrink = $this->cs->jshrinkInit();
            // If we got the class, try minification. Log failures.
            if ($jshrink) {
                try {
                    $contents = $jshrink::minify($contents, array('flaggedComments' => $preserve_comments));
                    $status = '';
                } catch (\Exception $e) {
                    $this->modx->log(
                        \modX::LOG_LEVEL_ERROR,
                        $e->getMessage() . '— js not compiled. Minification not performed.'
                    );
                }
            } else {
                $this->modx->log(
                    \modX::LOG_LEVEL_ERROR,
                    'Failed to load js Minifier class — js not compiled. Minification not performed.'
                );
            }
        }

        // None of the minifiers seem to handle this correctly?
        $contents = str_replace('!function', PHP_EOL . '!function', $contents);

        // If we didn't minify, output what we have
        file_put_contents($file, $contents);
        if (file_exists($file) && is_readable($file)) {
            $this->modx->log(
                \modX::LOG_LEVEL_INFO,
                'Minification was ' . $status . ' performed. Custom JS saved to file: ' . $file
            );
        }
    }
}
