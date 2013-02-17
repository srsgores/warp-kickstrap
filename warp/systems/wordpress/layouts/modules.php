<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if (function_exists('dynamic_sidebar')) {
	
	// get widgets
	ob_start();
	$result = dynamic_sidebar($position);
	$position_output = ob_get_clean();

	if ($position == 'menu') {
	    $result = true;
	    $position_output = $this->render('menu').((string) $position_output);
	}
	
	// handle preview
	if (!$result && $this['system']->isPreview($position)) {
	    $result = true;
		$position_output = $this->render('preview', compact('position'));
	}
	
	if ($result) {
		
		$parts   = explode('<!--widget-end-->', $position_output);
		$modules = array();
		$output  = array();
		
		//prepare modules
		foreach ($parts as $part) {

			if (!preg_match('/<!--widget-([a-z0-9-_]+)(?:<([^>]*)>)?-->/smU', $part, $matches)) continue;

			$module  = $this['widgets']->get($matches[1]);
			$suffix  = isset($matches[2]) ? $matches[2] : '';
			$content = str_replace($matches[0], '', $part);
			$title   = '';		

			// display it ?
            if (!$module->display) continue;

			// has title ?
			if (preg_match('/<!--title-start-->(.*)<!--title-end-->/smU', $content, $matches)) {
				$content = str_replace($matches[0], '', $content);
				$title = $matches[1]; 
			}

			$module->title     = strip_tags($title);
            $module->showtitle = isset($module->options['title']) ? $module->options['title'] : 1;
			$module->content   = $content;
            $module->position  = $position;
			$module->menu      = $module->type == 'nav_menu';
			$module->suffix    = $suffix;
			
			$modules[] = $module;
		}
		
		$count = count($modules);

		//output modules
        for ($i = 0; $i < $count; $i++) {
			
			$module = $modules[$i];
          
			// set params
            $params           = $module->options;
		    $params['count']  = $count;
            $params['order']  = $i + 1;
            $params['first']  = $params['order'] == 1;
            $params['last']   = $params['order'] == $count;
            $params['suffix'] = $module->suffix;

			// pass through menu params
			if (isset($menu)) {
				$params['menu'] = $menu;
			}

			// set position params
			$module->position_params = $params;

			// core overrides
			if (in_array($module->type, array('search', 'links', 'categories', 'pages', 'archives', 'recent-posts', 'recent-comments', 'calendar', 'meta', 'rss', 'tag_cloud', 'text'))) {
                $module->content = $this->render('widgets/'.$module->type, compact('module'));
			}
			
			// render module
           	$output[] = $this->render('module', compact('module', 'params'));
		}

		// render module layout
		echo (isset($layout) && $layout) ? $this->render("modules/layouts/{$layout}", array('modules' => $output)) : implode("\n", $output);
	}

}