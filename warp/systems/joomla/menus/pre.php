<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: WarpMenuPre
		Menu base class
*/
class WarpMenuPre extends WarpMenu {

	/*
		Function: process

		Returns:
			Object
	*/		
	public function process($module, $element) {

		// has ul ?
		if (!$element->first('ul:first')) {
			return false;
		}

		// init vars
		$menu   = JFactory::getApplication()->getMenu();
		$images = strpos($module->parameter->get('class_sfx'), 'images-off') === false;        

		foreach ($element->find('li') as $li) {

			// get menu item
			if (preg_match('/item-(\d+)/', $li->attr(version_compare(JVERSION, '1.7.0', '>=') ? 'class' : 'id'), $matches)) {
				$item = $menu->getItem($matches[1]);
			}

			// set id
			if (isset($item)) {
				$li->attr('data-id', $item->id);
			}

			// set current and active
			if ($li->hasClass('active')) {
				$li->attr('data-menu-active', $li->hasClass('current') == 'current' ? 2 : 1);
			}

			// set columns and width
			if (isset($item) && strpos($item->params->get('pageclass_sfx'), 'column') !== false) {

				if (preg_match('/columns-(\d+)/', $item->params->get('pageclass_sfx'), $matches)) {
					$li->attr('data-menu-columns', $matches[1]);
				}
				
				if (preg_match('/columnwidth-(\d+)/', $item->params->get('pageclass_sfx'), $matches)) {
					$li->attr('data-menu-columnwidth', $matches[1]);
				}
				
			}
			
			// set image
			if (isset($item) && $images && ($image = $item->params->get('menu_image'))) {
				if ($image != -1) {
					$li->attr('data-menu-image', JURI::base().$image);
				}
			}
			
			// set title span and clean empty text nodes
			foreach ($li->children('a,span') as $child) {
				$child->html(sprintf('<span>%s</span>', trim($child->text())));
			}

			$li->removeAttr('id')->removeAttr('class');
		}
				
		return $element;
	}

}