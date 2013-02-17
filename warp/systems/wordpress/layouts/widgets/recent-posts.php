<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

if ($ul = $this['dom']->create($module->content)->first('ul:first')) {
    echo $ul->attr('class', 'line')->html();
} else {
    echo $module->content;
}