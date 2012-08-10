<?php
/**
* @package   yoo_master
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include config	
include_once(dirname(__FILE__).'/config.php');

// get warp
$warp = Warp::getInstance();

// set messages
$title   = $this->title;
$error   = $this->error->get('code');
$message = $this->error->get('message');

// set 404 messages
if ($error == '404') {
	$title   = JText::_('TPL_WARP_404_PAGE_TITLE');
	$message = JText::sprintf('TPL_WARP_404_PAGE_MESSAGE', JURI::root(false), $warp['config']->get('site_name'));
}

// render error layout
echo $warp['template']->render('error', compact('title', 'error', 'message'));