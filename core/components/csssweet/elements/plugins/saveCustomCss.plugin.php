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
if ($modx->context->get('key') !== 'mgr') {
    return;
}

// Grab the cssSweet class
$cssSweetPath = $modx->getOption('csssweet.core_path', null, $modx->getOption('core_path') . 'components/csssweet/');
$cssSweetPath .= 'model/csssweet/';
$csssweet = $modx->getService('csssweet', 'CssSweet', $cssSweetPath);

if (!$csssweet || !($csssweet instanceof CssSweet)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[SaveCustomCss] could not load the required CssSweet class!');
    return;
}
$eventName = str_replace('_', '', $modx->event->name);
$pluginClass = "\\CssSweet\\v2\\Event\\CSS\\{$eventName}";

if (class_exists($pluginClass)) {
    /** @var \CssSweet\v2\Event\Event $event */
    $event = new $pluginClass($csssweet, $scriptProperties);
    $event->run();
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, "[SaveCustomCss] Class {$pluginClass} not found");
}