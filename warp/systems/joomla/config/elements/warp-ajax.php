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
$data   = JFactory::getApplication()->input->get('jform', array(), 'array');
$templ  = isset($data['template']) ? $data['template'] : '';
$config = JPATH_ROOT."/templates/{$templ}/config.php";

if ($templ && file_exists($config)) {

	// load template config
	require_once($config);

	// trigger save config
	$warp = Warp::getInstance();
	$warp['system']->saveConfig();	

}