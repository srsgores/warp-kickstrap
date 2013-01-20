<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

switch (count($modules)) {

	case 1:
		printf('<div class="grid-box width100 grid-h">%s</div>', $modules[0]);
		break;

	case 2:
		printf('<div class="grid-box width66 grid-h">%s</div>', $modules[0]);
		printf('<div class="grid-box width33 grid-h">%s</div>', $modules[1]);
		break;

	case 3:
		printf('<div class="grid-box width50 grid-h">%s</div>', $modules[0]);
		printf('<div class="grid-box width25 grid-h">%s</div>', $modules[1]);
		printf('<div class="grid-box width25 grid-h">%s</div>', $modules[2]);
		break;

	case 4:
		printf('<div class="grid-box width40 grid-h">%s</div>', $modules[0]);
		printf('<div class="grid-box width20 grid-h">%s</div>', $modules[1]);
		printf('<div class="grid-box width20 grid-h">%s</div>', $modules[2]);
		printf('<div class="grid-box width20 grid-h">%s</div>', $modules[3]);
		break;

	case 5:
		printf('<div class="grid-box width20 grid-h">%s</div>', $modules[0]);
		printf('<div class="grid-box width20 grid-h">%s</div>', $modules[1]);
		printf('<div class="grid-box width20 grid-h">%s</div>', $modules[2]);
		printf('<div class="grid-box width20 grid-h">%s</div>', $modules[3]);
		printf('<div class="grid-box width20 grid-h">%s</div>', $modules[4]);
		break;
		
	case 6:
		printf('<div class="grid-box width16 grid-h">%s</div>', $modules[0]);
		printf('<div class="grid-box width16 grid-h">%s</div>', $modules[1]);
		printf('<div class="grid-box width16 grid-h">%s</div>', $modules[2]);
		printf('<div class="grid-box width16 grid-h">%s</div>', $modules[3]);
		printf('<div class="grid-box width16 grid-h">%s</div>', $modules[4]);
		printf('<div class="grid-box width16 grid-h">%s</div>', $modules[5]);
		break;
		
	default:
		echo '<div class="grid-box width100 grid-h">Error: Only up to 6 modules are supported in this layout. If you need more add your own layout.</div>';

}