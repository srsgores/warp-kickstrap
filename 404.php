<?php
/**
* @package   Master
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get warp
$warp = Warp::getInstance();

// render error layout
echo $warp['template']->render('error', array('title' => __('Page not found', 'warp'), 'error' => '404', 'message' => sprintf(__('404_page_message', 'warp'), $warp['system']->url, $warp['config']->get('site_name'))));