<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

function pagination_list_render($list) {
	// Initialize variables
	$html = '<div class="pagination">';
	
	if ($list['start']['active']==1)   $html .= $list['start']['data'];
	if ($list['previous']['active']==1) $html .= $list['previous']['data'];

	foreach ($list['pages'] as $page) {
		$html .= $page['data'];
	}

	if ($list['next']['active']==1) $html .= $list['next']['data'];
	if ($list['end']['active']==1)  $html .= $list['end']['data'];

	$html .= "</div>";
	
	return $html;
}

function pagination_item_active(&$item) {
	
	$cls = '';
	
    if ($item->text == JText::_('JNEXT')) { $item->text = '»'; $cls = "next"; }
    if ($item->text == JText::_('JPREV')) { $item->text = '«'; $cls = "previous"; }
	if ($item->text == JText::_('JLIB_HTML_START')) { $cls = "first"; }
    if ($item->text == JText::_('JLIB_HTML_END')) { $cls = "last"; }
	
    return "<a class=\"".$cls."\" href=\"".$item->link."\" title=\"".$item->text."\">".$item->text."</a>";
}

function pagination_item_inactive(&$item) {
	return "<strong>".$item->text."</strong>";
}