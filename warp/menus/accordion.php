<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: WarpMenuAccordion
		Menu base class
*/
class WarpMenuAccordion extends WarpMenu {

	/*
		Function: process

		Returns:
			Object
	*/	
	public function process($module, $element) {

		$remove = array();

		// remove all but active or seperators (spans)
		foreach ($element->find('li.level1 ul') as $ul) {
			if ($ul->parent()->hasClass('active') || $ul->prev()->tag() == 'span') continue;
			$remove[] = $ul;
		}

		foreach ($remove as $ul) {
			$ul->parent()->removeChild($ul);
		}

		return $element;
	}

}