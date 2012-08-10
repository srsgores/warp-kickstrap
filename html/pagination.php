<?php
/**
* @package   yoo_master
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// include config and layout
$base = dirname(__FILE__);
include($base.'/config.php');
include($warp['path']->path('layouts:'.preg_replace('/'.preg_quote($base, '/').'/', '', __FILE__, 1)));