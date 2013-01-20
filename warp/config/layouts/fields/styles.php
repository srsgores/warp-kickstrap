<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

$style_folder = $this['path']->path('template:styles');
$skip_folders = array('.', '..', 'mobile');

// add default option as first option in the list
$styles_list  = array("default");

printf('<select %s>', $this['field']->attributes(compact('name')));

if ($style_folder) {

    // fill the list with all styles found in the folder
    foreach (scandir($style_folder) as $style) {
        
        if (in_array($style, $skip_folders) || !is_dir($style_folder.'/'.$style)) {
            continue;
        }

        $styles_list[] = $style;
    }
}

// output
foreach ($styles_list as $option) {
    // set attributes
    $attributes = array('value' => $option);

    // // is checked ?
    if ($option == $value) {
        $attributes = array_merge($attributes, array('selected' => 'selected'));
    }

    // make option-text more human-readable (spaces instead of underscores and capital letters after each space)
    $text = ucwords(str_replace( '_', ' ', $option ));

    printf('<option %s>%s</option>', $this['field']->attributes($attributes), $text);
}

printf('</select>');