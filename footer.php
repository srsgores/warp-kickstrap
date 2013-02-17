<?php
/**
* @package   Master
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get warp
$warp = Warp::getInstance();

// get content from output buffer and set a slot for the template renderer
$warp['template']->set('content', ob_get_clean());

// load main template file, located in /layouts/template.php
echo $warp['template']->render('template');