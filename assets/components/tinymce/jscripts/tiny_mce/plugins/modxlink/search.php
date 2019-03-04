<?php
/**
 * Handles dynamic search
 * @package tinymce
 */
require_once dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$searchMode = $modx->getOption('search-mode', $_REQUEST, 'pagetitle');
$query = $modx->getOption('q', $_REQUEST, '');

$c = $modx->newQuery('modResource');
$c->where(array(
    $searchMode . ':LIKE' => '%' . $query . '%',
));

$count = $modx->getCount('modResource', $c);

$c->select(array('id', 'pagetitle', 'alias', 'context_key'));
$c->limit(10);

$resources = $modx->getCollection('modResource', $c);

$output = array();
/** @var modResource $resource */
foreach ($resources as $resource) {
    $output[] = array(
        'id' => $resource->get('id'),
        'context' => $resource->get('context_key'),
        'title' => $resource->get('pagetitle'),
    );
}

$contexts = array_unique(array_column($output, 'context'));

echo implode(PHP_EOL, array_map(function ($row) use($contexts) {
    return count($contexts) > 1
        ? sprintf('%s - %s (%d)|%d', $row['context'], $row['title'], $row['id'], $row['id'])
        : sprintf('%s (%d)|%d', $row['title'], $row['id'], $row['id']);
}, $output));
die();
