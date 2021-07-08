<?php
/**
 * Handles dynamic search
 *
 * @package tinymce
 */
require_once dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';
$list = array();
$ugroups = $modx->user->getUserGroupNames();
$ug = $modx->newQuery('modUserGroup');
$ug->where(array('name:IN'=>$ugroups));
$groupsin = $modx->getCollection('modUserGroup',$ug);
if(!empty($groupsin)){
    foreach($groupsin as $gi){
        $webContextAccess = $modx->newQuery('modAccessContext');
        $webContextAccess->where(array(
            'principal' =>$gi->get('id'),
            'AND:target:!=' => 'mgr',
        ));
        $gi_cntx = $modx->getCollection('modAccessContext', $webContextAccess);
        if(!empty($gi_cntx)){
            foreach($gi_cntx AS $acl){
                if(!in_array($acl->get('target'), $list))
                    $list[] =$acl->get('target');
            }
        }
    }
}
$searchMode = $modx->getOption('search-mode',$_REQUEST,'pagetitle');
$query = $modx->getOption('q',$_REQUEST,'');

$c = $modx->newQuery('modResource');
$c->where(array(
    $searchMode.':LIKE' => '%'.$query.'%',
    'context_key:IN' => $list,
));

$count = $modx->getCount('modResource',$c);

$c->select(array('id','pagetitle','alias'));
$c->limit(10);

$resources = $modx->getCollection('modResource',$c);

foreach ($resources as $resource) {
     echo $resource->get('pagetitle').' ('.$resource->get('id').')|'.$resource->get('id')."\n";
}
die();
