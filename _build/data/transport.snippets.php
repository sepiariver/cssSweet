<?php
$snippets = array();

/* create the snippets objects */

// lighten
$snippets[0] = $modx->newObject('modSnippet');
$snippets[0]->set('id',1);
$snippets[0]->set('name','lighten');
$snippets[0]->set('description','Accepts a hex value and percentage (+ or -) option. Additionally \'max\' or \'rev\' can be set, with or without a percentage.');
$snippets[0]->set('snippet', getSnippetContent($sources['snippets'] . 'lighten.snippet.php'));
//$snippets[0]->set('category', 'cssSweet');
$properties = include $sources['snippets'].'lighten.properties.inc.php';
$snippets[0]->setProperties($properties);
unset($properties);

// modval
$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->set('id',2);
$snippets[1]->set('name','modval');
$snippets[1]->set('description','Extracts values from CSS value/unit pairs and modifies based on options.');
$snippets[1]->set('snippet', getSnippetContent($sources['snippets'] . 'modval.snippet.php'));
//$snippets[1]->set('category', 'cssSweet');

// prefix
$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->set('id',3);
$snippets[2]->set('name','prefix');
$snippets[2]->set('description','Adds simple browser prefixes to strings.');
$snippets[2]->set('snippet', getSnippetContent($sources['snippets'] . 'prefix.snippet.php'));
//$snippets[2]->set('category', 'cssSweet');

return $snippets;