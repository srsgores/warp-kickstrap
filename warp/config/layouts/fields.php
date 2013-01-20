<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

printf('<ul class="properties" %s>', $this['field']->attributes($attr));

$profile = preg_match('/^profile_data\[(.+)\]$/', $prefix, $matches);

foreach ($fields->find('field') as $field) {

    $name  = $field->attr('name');
    $type  = $field->attr('type');
    $label = $field->attr('label');
    $desc  = $field->attr('description');
	$value = $values->get($name, $field->attr('default'));
	$class = $profile && $matches[1] != 'default' && $values->get($name) === null ? ' class="ignore"' : null; 

	if ($type == 'separator') {
		printf('<li class="separator">%s</li>', $name);
	} else {
		printf('<li%s><div class="wlabel">%s</div><div class="field">%s</div><div class="description">%s</div></li>', $class, $label, $this['field']->render($type, $prefix.'['.$name.']', $value, $field, compact('config')), $desc);
	}
}

if ($profile) {
	printf('<li style="display:none;"><input type="hidden" name="%s[present]" value="1" /></li>', $prefix);
}

echo '</ul>';