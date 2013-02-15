<?php

printf('<input %s />', $control->attributes(array_merge($node->attr(), array('type' => 'number', 'name' => $name, 'value' => $value, 'step' => '0.1')), array('label', 'description', 'default')));