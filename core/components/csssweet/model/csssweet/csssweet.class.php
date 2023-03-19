<?php

use JShrink\Minifier;
use OzdemirBurak\Iris\BaseColor;
use OzdemirBurak\Iris\Exceptions\InvalidColorException;
use ScssPhp\ScssPhp\Compiler;

/**
 * CssSweet wrapper class
 * @package cssSweet
 *
 * @author @sepiariver <yj@modx.com> <info@sepiariver.com>
 * Copyright 2013 - 2015 by YJ Tso
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 **/

class CssSweet
{
    public $modx = null;
    public $namespace = 'csssweet';
    public $options = array();

    public function __construct(modX &$modx, array $options = array())
    {
        $this->modx = &$modx;
        $this->namespace = $this->getOption('namespace', $options, 'csssweet');

        $corePath = $this->getOption(
            'core_path',
            $options,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/csssweet/'
        );
        $assetsPath = $this->getOption(
            'assets_path',
            $options,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/csssweet/'
        );
        $assetsUrl = $this->getOption(
            'assets_url',
            $options,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/csssweet/'
        );

        /* load config defaults */
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php',
        ), $options);

        require_once($this->options['vendorPath'] . 'autoload.php');
    }

    /**
     * Do special stuff to init scssphp classes
     *
     * @param array $paths
     * @param string $formatter The scssphp formatter class selector. Default 'Expanded'
     * @return object An instance of the $scssphp class.
     */
    public function scssphpInit(array $paths = array(), string $formatter = 'expanded')
    {
        // Instantiate Compiler
        $scssphp = new Compiler();

        // Set path
        $scssphp->setImportPaths($paths);
        // Found this helpful
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Applying scssphp formatter class: ' . $formatter);

        $scssphp->setOutputStyle(strtolower($formatter));

        // Ask and you shall receive
        return $scssphp;
    }

    public function jshrinkInit(): Minifier
    {
        // Grab the JS minifier class
        return new Minifier();
    }

    public function getIris(string $value, $format = 'hex'): ?BaseColor
    {
        // Set format class
        $format = '\\OzdemirBurak\\Iris\\Color\\' . ucfirst(strtolower($format));
        // Grab the iris color format class
        $iris = new $format($value);
        if (!($iris instanceof $format)) {
            return null;
        }
        return $iris;
    }

    /**
     * Process and array of chunk (names) with provided $settings
     *
     * @param array $chunks An array of chunk names.
     * @param array $settings An array of settings/properties to pass to the chunks.
     * @return string A concatenated string of all processed chunk output.
     */
    public function processChunks(array $chunks, array $settings)
    {
        // Init var
        $contents = '';
        foreach ($chunks as $current) {
            if ($current) {
                try {
                    $this->modx->log(modX::LOG_LEVEL_INFO, 'Processing chunk: ' . $current);
                    $processed = $this->modx->getChunk($current, $settings);
                    if ($processed) {
                        $contents .= $processed;
                    } else {
                        $err = '$this->modx->getChunk() failed for chunk: ' . $current;
                        throw new Exception($err);
                    }
                } catch (Exception $err) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, $err->getMessage());
                }
            } else {
                $this->modx->log(
                    modX::LOG_LEVEL_ERROR,
                    'Failed to get Chunk ' . $current . '. Chunk contents not saved.'
                );
            }
        }
        // Even if contents is empty, return it
        return $contents;
    }

    /**
     * Instantiate ClientConfig and include settings
     *
     * @param array $settings An array of settings to merge into.
     * @return array Either the original array or a merged one.
     */
    public function getClientConfigSettings($settings): array
    {
        // Init var
        $clientConfig = null;

        // Grab the ClientConfig class
        $ccPath = $this->modx->getOption(
            'clientconfig.core_path',
            null,
            $this->modx->getOption('core_path') . 'components/clientconfig/'
        );
        $ccPath .= 'model/clientconfig/';
        if (file_exists($ccPath . 'clientconfig.class.php')) {
            $clientConfig = $this->modx->getService('clientconfig', 'ClientConfig', $ccPath);
        }

        // If we got the class (which means it's installed properly), include the settings
        if ($clientConfig && ($clientConfig instanceof ClientConfig)) {
            $ccSettings = $clientConfig->getSettings();
            if (is_array($ccSettings)) {
                $settings = array_merge($settings, $ccSettings);
            }
        } else {
            $this->modx->log(
                modX::LOG_LEVEL_WARN,
                'Failed to load ClientConfig class. ClientConfig settings not included.',
                '',
                'saveCustomCssClientConfig'
            );
        }

        // Settings may or may not be modified at this point
        return $settings;
    }

    public function getColorClass($input)
    {
        $input = trim($input);
        // Set color class
        $format = null;
        $unHash = false;
        $color = null;
        if (strpos($input, 'rgba') === 0) {
            $format = 'Rgba';
        } elseif (strpos($input, 'rgb') === 0) {
            $format = 'Rgb';
        } elseif (strpos($input, 'hsla') === 0) {
            $format = 'Hsla';
        } elseif (strpos($input, 'hsl') === 0) {
            $format = 'Hsl';
        } elseif (strpos($input, 'hsv') === 0) {
            $format = 'Hsv';
        } elseif (strpos($input, '#') === 0) {
            $format = 'Hex';
        } elseif (preg_match('/[a-fA-F0-9]{6}/', $input) || preg_match('/[a-fA-F0-9]{3}/', $input)) {
            $format = 'Hex';
            $input = '#' . $input;
            $unHash = true;
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[cssSweet.getColorClass] unsupported color format: ' . $input);
        }

        // Instantiate iris color class
        if ($format) {
            try {
                $color = $this->getIris($input, $format);
            } catch (InvalidColorException $e) {
                $this->modx->log(
                    modX::LOG_LEVEL_ERROR,
                    '[cssSweet.getColorClass] InvalidColorException: ' . $e->getMessage()
                );
            }
        }

        return [
            'format' => $format,
            'unHash' => $unHash,
            'color' => $color,
        ];
    }

    public function getProperties($properties, $mode)
    {
        if (!is_array($properties)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[cssSweet.getProperties] invalid $properties array.');
            return [];
        }
        $mode = (string) $mode;
        if (empty($mode)) {
            // Nothing to do
            return $properties;
        }
        // Override properties with mode props
        foreach ($properties as $key => $val) {
            // skip any mode props
            if (strpos($key, $mode) === 0) {
                continue;
            }
            // these are standard scriptProperties
            $properties[$key] = (isset($properties[$mode . '_' . $key])) ? $properties[$mode . '_' . $key] : $val;
        }
        return $properties;
    }

    public function checkDir($path, $caller = 'csssweet.checkDir'): array
    {
        // If directory exists but isn't writable we have a problem, Houston
        if (file_exists($path) && !is_writable($path)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'The directory at ' . $path . 'is not writable!', '', $caller);
            return [
                'success' => false,
                'message' => 'The directory at ' . $path . 'is not writable!',
            ];
        } elseif (!file_exists($path)) {
            // Check if directory exists, if not, create it
            if (mkdir($path, 0755, true)) {
                $this->modx->log(modX::LOG_LEVEL_INFO, 'Directory created at ' . $path, '', $caller);
                return [
                    'success' => true,
                    'message' => 'Directory created at ' . $path,
                ];
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Directory could not be created at ' . $path, '', $caller);
                return [
                    'success' => false,
                    'message' => 'Directory could not be created at ' . $path,
                ];
            }
        } else {
            return [
                'success' => true,
                'message' => 'Using output directory ' . $path,
            ];
        }
    }

    /* For testing and backwards compatibility */
    public function converting($input, $options = '')
    {
        return (new \CssSweet\v2\Snippet\Convert($this, [
            'input' => $input,
            'options' => $options,
        ]))->process();
    }

    public function extracting($input, $options)
    {
        return (new \CssSweet\v2\Snippet\Extract($this, [
            'input' => $input,
            'options' => $options,
        ]))->process();
    }

    public function lightening($input, $options)
    {
        return (new \CssSweet\v2\Snippet\Lighten($this, [
            'input' => $input,
            'options' => $options,
        ]))->process();
    }

    public function modifying($input, $options)
    {
        return (new \CssSweet\v2\Snippet\Modify($this, [
            'input' => $input,
            'options' => $options,
        ]))->process();
    }

    public function saturating($input, $options)
    {
        return (new \CssSweet\v2\Snippet\Saturate($this, [
            'input' => $input,
            'options' => $options,
        ]))->process();
    }

    /* UTILITY METHODS (@theboxer) */

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption(string $key, array $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    /**
     * Despite the variable name, it takes a string and returns an array
     */
    public function explodeAndClean($array, $delimiter = ','): array
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array

        return $array;
    }

    public function getChunk($tpl, $phs)
    {
        if (strpos($tpl, '@INLINE ') !== false) {
            $content = str_replace('@INLINE', '', $tpl);
            /** @var \modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk', array('name' => 'inline-' . uniqid()));
            $chunk->setCacheable(false);

            return $chunk->process($phs, $content);
        }

        return $this->modx->getChunk($tpl, $phs);
    }
}
