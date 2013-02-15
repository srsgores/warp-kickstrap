<?php
printf('<input %s />', $control->attributes(array_merge($node->attr(), array('type' => 'color', 'name' => $name, 'value' => $value)), array('label', 'description', 'default')));