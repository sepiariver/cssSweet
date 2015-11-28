<?php

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
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, 'csssweet');

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/csssweet/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/csssweet/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/csssweet/');
        $scssphpPath = $corePath . 'model/csssweet/libs/scssphp/';
        $jshrinkPath = $corePath . 'model/csssweet/libs/jshrink/';
        
        /* load config defaults */
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'scssphpPath' => $scssphpPath,
            'jshrinkPath' => $jshrinkPath,
        ), $options);
               
    }
    
    /**
     * Do special stuff to init scssphp classes
     *
     * @param array $path An array of import paths.
     * @param string $formatter The scssphp formatter class selector. Default 'Expanded'
     * @return object An instance of the $scssphp class.
     */
    public function scssphpInit($paths=array(), $formatter='Expanded') 
    {
        $scssphp = null;
        // Check min PHP requirement
        if (version_compare(PHP_VERSION, '5.4') < 0) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'scssphp requires PHP 5.4 or above!');
            return $scssphp;
        }
   
        // Check class but don't autoload
        if (! class_exists('scssc', false)) {
            include_once $this->options['scssphpPath'] . 'src/Base/Range.php';
            include_once $this->options['scssphpPath'] . 'src/Block.php';
            include_once $this->options['scssphpPath'] . 'src/Colors.php';
            include_once $this->options['scssphpPath'] . 'src/Compiler.php';
            include_once $this->options['scssphpPath'] . 'src/Compiler/Environment.php';
            include_once $this->options['scssphpPath'] . 'src/Formatter.php';
            include_once $this->options['scssphpPath'] . 'src/Formatter/Compact.php';
            include_once $this->options['scssphpPath'] . 'src/Formatter/Compressed.php';
            include_once $this->options['scssphpPath'] . 'src/Formatter/Crunched.php';
            include_once $this->options['scssphpPath'] . 'src/Formatter/Debug.php';
            include_once $this->options['scssphpPath'] . 'src/Formatter/Expanded.php';
            include_once $this->options['scssphpPath'] . 'src/Formatter/Nested.php';
            include_once $this->options['scssphpPath'] . 'src/Formatter/OutputBlock.php';
            include_once $this->options['scssphpPath'] . 'src/Node.php';
            include_once $this->options['scssphpPath'] . 'src/Node/Number.php';
            include_once $this->options['scssphpPath'] . 'src/Parser.php';
            include_once $this->options['scssphpPath'] . 'src/Type.php';
            include_once $this->options['scssphpPath'] . 'src/Util.php';
            include_once $this->options['scssphpPath'] . 'src/Version.php';
            //include_once $this->options['scssphpPath'] . 'src/Server.php';
        }
        
        $scssphp = new Leafo\ScssPhp\Compiler();
        
        // Set path
        $scssphp->setImportPaths($paths);
        
        // Set formatter
        $formatter = 'Leafo\ScssPhp\Formatter\\' . $formatter;
        // Found this helpful
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Applying scssphp formatter class: ' . $formatter);
        
        $scssphp->setFormatter($formatter);
        
        // Ask and you shall receive
        return $scssphp; 
        
    }
    
    /**
     * Process and array of chunk (names) with provided $settings
     *
     * @param array $chunks An array of chunk names.
     * @param array $settings An array of settings/properties to pass to the chunks.
     * @return string A concatenated string of all processed chunk output.
     */
    public function processChunks(array $chunks, array $settings) {
	    $contents = '';
	    foreach ($chunks as $current) {
	    
		    $processed = '';
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
		        $this->modx->log(modX::LOG_LEVEL_ERROR, 'Failed to get Chunk ' . $current . '. Chunk contents not saved.');
		    }
		    
		}
		return $contents;
	    
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
    public function getOption($key, $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
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
    public function explodeAndClean($array, $delimiter = ',')
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