<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// get template data/path
$data   = JRequest::getVar('jform', array(), 'post', 'array');
$config = JPATH_ROOT."/templates/{$data['template']}/config.php";

if (file_exists($config)) {

	// load template config
	require_once($config);

	// trigger save config
	$warp = Warp::getInstance();
	$warp['system']->saveConfig();	

}