<?php

$plugins = array();

/* create the plugin object */
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('id', 1);
$plugins[0]->set('name', 'saveCustomCSS');
$plugins[0]->set('description', 'Saves chunk content to file custom.css in the theme folder on cache refresh. ');
$plugins[0]->set('plugincode', getSnippetContent($sources['plugins'] . 'saveCustomCss.plugin.php'));
//$plugins[0]->set('category', 'cssSweet');
$properties = include $sources['plugins'] . 'saveCustomCss.properties.inc.php';
$plugins[0]->setProperties($properties);
unset($properties);

$events = include $sources['plugins'] . 'saveCustomCss.events.php';
if (is_array($events) && !empty($events)) {
    $plugins[0]->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_INFO, 'Packaged in ' . count($events) . ' Plugin Events for saveCustomCss.');
    flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not find plugin events for saveCustomCss!');
}
unset($events);



return $plugins;
