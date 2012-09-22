<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

switch (count($modules)) {

	case 1:
		printf('<div class="twelvecol last">%s</div>', $modules[0]);
		break;

	case 2:
		printf('<div class="sixcol">%s</div>', $modules[0]);
		printf('<div class="sixcol last">%s</div>', $modules[1]);
		break;

	case 3:
		printf('<div class="fourcol">%s</div>', $modules[0]);
		printf('<div class="fourcol">%s</div>', $modules[1]);
		printf('<div class="fourcol last">%s</div>', $modules[2]);
		break;

	case 4:
		printf('<div class="threecol">%s</div>', $modules[0]);
		printf('<div class="threecol">%s</div>', $modules[1]);
		printf('<div class="threecol">%s</div>', $modules[2]);
		printf('<div class="threecol last">%s</div>', $modules[3]);
		break;

	case 5:
		printf('<div class="twocol">%s</div>', $modules[0]);
		printf('<div class="threecol">%s</div>', $modules[1]);
		printf('<div class="twocol">%s</div>', $modules[2]);
		printf('<div class="threecol">%s</div>', $modules[3]);
		printf('<div class="twocol last">%s</div>', $modules[4]);
		break;

	case 6:
		printf('<div class="twocol">%s</div>', $modules[0]);
		printf('<div class="twocol">%s</div>', $modules[1]);
		printf('<div class="twocol">%s</div>', $modules[2]);
		printf('<div class="twocol">%s</div>', $modules[3]);
		printf('<div class="twocol">%s</div>', $modules[4]);
		printf('<div class="twocol last">%s</div>', $modules[5]);
		break;

	default:
		echo "<div class=\"twelvecol last\"><div class = \"alert alert-error\"><h1 class = \"title\">Error</h1><p>Only up to 6 modules are supported in this layout. If you need more add your own layout.</p></div></div>";

}