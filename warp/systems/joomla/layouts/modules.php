<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// load modules
$modules = $this['modules']->load($position);
$count   = count($modules);
$output  = array();

foreach ($modules as $index => $module) {

	// set module params
	$params           = array();
	$params['count']  = $count;
	$params['order']  = $index + 1;
	$params['first']  = $params['order'] == 1;
	$params['last']   = $params['order'] == $count;
	$params['suffix'] = $module->parameter->get('moduleclass_sfx', '');

	// pass through menu params
	if (isset($menu)) {
		$params['menu'] = $menu;
	}

	// get class suffix params
	$parts = preg_split('/[\s]+/', $params['suffix']);

	foreach ($parts as $part) {
		if (strpos($part, '-') !== false) {
			list($name, $value) = explode('-', $part, 2);
			$params[$name] = $value;
		}
	}

	// remove used parameters from suffix so we dont end up having the wrong css-classes
	$params['suffix'] = trim(preg_replace("/(style|color|badge|icon|header)-[a-z0-9]+/", "", $params['suffix']));

	// render module
	$output[] = $this->render('module', compact('module', 'params'));
}

// render module layout
echo (isset($layout) && $layout) ? $this->render("modules/layouts/{$layout}", array('modules' => $output)) : implode("\n", $output);