<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

$html = array();

if (($checksums = $this['path']->path('template:checksums')) && filesize($checksums)) {
	$this['checksum']->verify($this['path']->path('template:'), $log);

	if ($count = count($log)) {
	
		$html[] = '<a href="#" class="verify-link">Some template files have been modified.</a>';
		$html[] = '<ul class="verify">';
		foreach (array('modified', 'missing') as $type) {
			if (isset($log[$type])) {
				foreach ($log[$type] as $file) {
					$html[] = '<li class="'.$type.'">'.$file.($type == 'missing' ? ' (missing)' : null).'</li>';
				}
			}
		}
		$html[] = '</ul>';

	} else {
		$html[] = 'Verification successful, no file modifications detected.';
	}

} else {
	$html[] = 'Checksum file is missing! Your template is maybe compromised.';
}

echo implode("\n", $html);