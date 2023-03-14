<?php

$chunks = array();

/* create the plugin object */
$chunks[0] = $modx->newObject('modChunk');
$chunks[0]->set('id', 1);
$chunks[0]->set('name', 'csss.custom.css');
$chunks[0]->set('description', 'Chunk with custom, dynamic CSs that will be parsed on Cache Refresh.');
$chunks[0]->set('snippet', file_get_contents($sources['chunks'] . 'csss.custom.css.tpl'));
//$chunks[0]->set('category', 'cssSweet');

return $chunks;
