<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: WarpMenuDropdown
		Menu base class
*/
class WarpMenuDropdown extends WarpMenu {

	/*
		Function: process

		Returns:
			Object
	*/	
	public function process($module, $element) {
		
		foreach ($element->find('ul.level2') as $ul) {
			
			// get parent li
			$li = $ul->parent();

			// get columns
			$columns = (int) $li->attr('data-menu-columns');

			if ($columns > 1) {

				$children = $ul->children('li');
				$colrows  = ceil($children->length / $columns);
				$column   = 0;
				$i        = 0;

				foreach ($children as $child) {
					$col = intval($i / $colrows);
					
					if ($column != $col) {
						$column = $col;
					}

					if ($li->children('ul')->length == $column) {
						$li->append('<ul class="level2"></ul>');
					}
					
					if ($column > 0) {
						$li->children('ul')->item($column)->append($child);
					}

					$i++;
				}

			} else {
				$columns = 1;
			}

			// get width
			$width = (int) $li->attr('data-menu-columnwidth');
			$style = $width > 0 ? sprintf(' style="width:%spx;"', $columns * $width) : null;

			// append dropdown divs		
			$li->append(sprintf('<div class="dropdown columns%d"%s><div class="dropdown-bg"><div></div></div></div>', $columns, $style));
			$div = $li->first('div.dropdown div.dropdown-bg div:first');

			foreach ($li->children('ul') as $i => $u) {
				$div->append(sprintf('<div class="width%d column"></div>', floor(100 / $columns)))->children('div')->item($i)->append($u);
			}
		}

		return $element;
	}

}