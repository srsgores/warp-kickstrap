<?php
/*------------------------------------------------------------------------------------------------------------------------
    Author: Sean Goresht
    www: http://seangoresht.com/
    github: https://github.com/srsgores

    twitter: http://twitter.com/S.Goresht

     warp-kickstrap Joomla Template
     Licensed under the GNU Public License

	=============================================================================
	Filename:  template.config.php
	=============================================================================
	 This file is responsible for the main rendering of the template's LAYOUT parameters:
	 	--module default lengths
	 	--loading template-specific styles and javascript
	 	--generation of mobile dropdown for nav menus
	 	--loading template-specific IE conditional styles and Javascript
	 Note: all assets are pulled in with WARP's helper functions.  If you don't want to use WARP, check out Joomla's application-->addStylesheet(URL) function.

--------------------------------------------------------------------------------------------------------------------- */

// generate css for layout
$css[] = sprintf('.wrapper { max-width: %dpx; }', $this['config']->get('template_width'));

// generate css for 3-column-layout
$sidebar_a       = '';
$sidebar_b       = '';
$maininner_width = 100;
$sidebar_a_width = intval($this['config']->get('sidebar-a_width'));
$sidebar_b_width = intval($this['config']->get('sidebar-b_width'));
$sidebar_classes = "";
$rtl             = $this['config']->get('direction') == 'rtl';
$body_config	 = array();

// set widths
if ($this['modules']->count('sidebar-a')) {
	$sidebar_a = $this['config']->get('sidebar-a');
	$maininner_width -= $sidebar_a_width;
	$css[] = sprintf('#sidebar-a { width: %d%%; }', $sidebar_a_width);
}

if ($this['modules']->count('sidebar-b')) {
	$sidebar_b = $this['config']->get('sidebar-b');
	$maininner_width -= $sidebar_b_width;
	$css[] = sprintf('#sidebar-b { width: %d%%; }', $sidebar_b_width);
}

$css[] = sprintf('#maininner { width: %d%%; }', $maininner_width);

// all sidebars right
if (($sidebar_a == 'right' || !$sidebar_a) && ($sidebar_b == 'right' || !$sidebar_b)) {
	$sidebar_classes .= ($sidebar_a) ? 'sidebar-a-right ' : '';
	$sidebar_classes .= ($sidebar_b) ? 'sidebar-b-right ' : '';

// all sidebars left
} elseif (($sidebar_a == 'left' || !$sidebar_a) && ($sidebar_b == 'left' || !$sidebar_b)) {
	$sidebar_classes .= ($sidebar_a) ? 'sidebar-a-left ' : '';
	$sidebar_classes .= ($sidebar_b) ? 'sidebar-b-left ' : '';
	$css[] = sprintf('#maininner { float: %s; }', $rtl ? 'left' : 'right');

// sidebar-a left and sidebar-b right
} elseif ($sidebar_a == 'left') {
	$sidebar_classes .= 'sidebar-a-left sidebar-b-right ';
	$css[] = '#maininner, #sidebar-a { position: relative; }';
	$css[] = sprintf('#maininner { %s: %d%%; }', $rtl ? 'right' : 'left', $sidebar_a_width);
	$css[] = sprintf('#sidebar-a { %s: -%d%%; }', $rtl ? 'right' : 'left', $maininner_width);

// sidebar-b left and sidebar-a right
} elseif ($sidebar_b == 'left') {
	$sidebar_classes .= 'sidebar-a-right sidebar-b-left ';
	$css[] = '#maininner, #sidebar-a, #sidebar-b { position: relative; }';
	$css[] = sprintf('#maininner, #sidebar-a { %s: %d%%; }', $rtl ? 'right' : 'left', $sidebar_b_width);
	$css[] = sprintf('#sidebar-b { %s: -%d%%; }', $rtl ? 'right' : 'left', $maininner_width + $sidebar_a_width);
}

// number of sidebars
if ($sidebar_a && $sidebar_b) {
	$sidebar_classes .= 'sidebars-2 ';
} elseif ($sidebar_a || $sidebar_b) {
	$sidebar_classes .= 'sidebars-1 ';
}

// generate css for dropdown menu
foreach (array(1 => '.dropdown', 2 => '.columns2', 3 => '.columns3', 4 => '.columns4') as $i => $class) {
	$css[] = sprintf('#menu %s { width: %dpx; }', $class, $i * intval($this['config']->get('menu_width')));
}

// load css
$this['asset']->addFile('css', 'css:reset.css');
$this['asset']->addFile('css', 'css:base.css');
$this['asset']->addFile('css', 'css:1140/1140.css');
$this['asset']->addFile('css', 'css:style.css');

if ($this['config']->get('yootheme-css') == "1") {
	$this['asset']->addFile('css', 'css:yootheme/layout.css');
	$this['asset']->addFile('css', 'css:yootheme/menus.css');
	$this['asset']->addString('css', implode("\n", $css));
	$this['asset']->addFile('css', 'css:yootheme/modules.css');
	$this['asset']->addFile('css', 'css:yootheme/tools.css');
	$this['asset']->addFile('css', 'css:yootheme/system.css');
	$this['asset']->addFile('css', 'css:extensions.css');
	$this['asset']->addFile('css', 'css:custom.css');

	if (($color = $this['config']->get('color1')) && $this['path']->path("css:/yootheme/color1/$color.css")) { $this['asset']->addFile('css', "css:/yootheme/color1/$color.css"); }
	if (($color = $this['config']->get('color2')) && $this['path']->path("css:/yootheme/color2/$color.css")) { $this['asset']->addFile('css', "css:/yootheme/color2/$color.css"); }
	if (($font = $this['config']->get('font1')) && $this['path']->path("css:/yootheme/font1/$font.css")) { $this['asset']->addFile('css', "css:/yootheme/font1/$font.css"); }
	if (($font = $this['config']->get('font2')) && $this['path']->path("css:/yootheme/font2/$font.css")) { $this['asset']->addFile('css', "css:/yootheme/font2/$font.css"); }
	if (($font = $this['config']->get('font3')) && $this['path']->path("css:/yootheme/font3/$font.css")) { $this['asset']->addFile('css', "css:/yootheme/font3/$font.css"); }
	if ($this['config']->get('direction') == 'rtl') $this['asset']->addFile('css', 'css:/yoothemertl.css');
	$this['asset']->addFile('css', 'css:yootheme/responsive.css');
	$this['asset']->addFile('css', 'css:yootheme/print.css');
}
elseif ($this['config']->get("bootstrap-css") == "1") {

}
elseif ($this['config']->get("1140-css") == "1") {

}

// load fonts
$http  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$fonts = array(
	'droidsans' => 'template:fonts/droidsans.css',
	'opensans' => 'template:fonts/opensans.css',
	'yanonekaffeesatz' => 'template:fonts/yanonekaffeesatz.css',
	'mavenpro' => 'template:fonts/mavenpro.css',
	'kreon' => 'template:fonts/kreon.css');

foreach (array_unique(array($this['config']->get('font1'), $this['config']->get('font2'), $this['config']->get('font3'))) as $font) {
	if (isset($fonts[$font])) {
		$this['asset']->addFile('css', $fonts[$font]);
	}
}

// set body css classes
$body_classes  = $sidebar_classes.' ';
$body_classes .= $this['system']->isBlog() ? 'isblog ' : 'noblog ';
$body_classes .= $this['config']->get('page_class');

$this['config']->set('body_classes', $body_classes);

// add social buttons
$body_config['twitter'] = (int) $this['config']->get('twitter', 0);
$body_config['plusone'] = (int) $this['config']->get('plusone', 0);
$body_config['facebook'] = (int) $this['config']->get('facebook', 0);

$this['config']->set('body_config', json_encode($body_config));

// add javascripts
$this['asset']->addFile('js', 'js:warp.js');
$this['asset']->addFile('js', 'js:responsive.js');
$this['asset']->addFile('js', 'js:accordionmenu.js');
$this['asset']->addFile('js', 'js:dropdownmenu.js');
$this['asset']->addFile('js', 'js:template.js');

if ($this['config']->get('loader') == "1")
{
	$this['asset']->addFile('js', 'js:loader.js');
}

// internet explorer
if ($this['useragent']->browser() == 'msie') {

	// add conditional comments
	$head[] = sprintf('<!--[if lte IE 8]><script src="%s"></script><![endif]-->', $this['path']->url('js:html5.js'));
	$head[] = sprintf('<!--[if IE 8]><link rel="stylesheet" href="%s" /><![endif]-->', $this['path']->url('css:ie8.css'));

}

// add $head
if (isset($head)) {
	$this['template']->set('head', implode("\n", $head));
}