<?php
/**
 * @package   yoo_master
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

/*------------------------------------------------------------------------------------------------------------------------
    Author: Sean Goresht
    www: http://seangoresht.com/
    github: https://github.com/srsgores

    twitter: http://twitter.com/S.Goresht

     warp-kickstrap wordpress Template
     Licensed under the GNU Public License

	=============================================================================
	Filename:  template.config.php
	=============================================================================
	 This file is responsible for the main rendering of the template's LAYOUT parameters:
	 	--module default lengths
	 	--loading template-specific styles and javascript
	 	--generation of mobile dropdown for nav menus
	 	--loading template-specific IE conditional styles and Javascript
	 Note: all assets are pulled in with WARP's helper functions.  If you don't want to use WARP, check out wordpress's application-->addStylesheet(URL) function.

--------------------------------------------------------------------------------------------------------------------- */

// generate css for layout
//$css[] = sprintf('.wrapper { max-width: %dpx; }', $this['config']->get('template_width'));

// generate css for 3-column-layout

$sidebar_a = '';
$sidebar_b = '';
$maininner_width = 100;
$sidebar_a_width = intval($this['config']->get('sidebar-a_width'));
$sidebar_b_width = intval($this['config']->get('sidebar-b_width'));
$sidebar_classes = "";
$rtl = $this['config']->get('direction') == 'rtl';
$body_config = array();

// set widths
if ($this['modules']->count('sidebar-a'))
{
	$sidebar_a = $this['config']->get('sidebar-a');
	$maininner_width -= $sidebar_a_width;
	$css[] = sprintf('#sidebar-a { width: %d%%; }', $sidebar_a_width);
}

if ($this['modules']->count('sidebar-b'))
{
	$sidebar_b = $this['config']->get('sidebar-b');
	$maininner_width -= $sidebar_b_width;
	$css[] = sprintf('#sidebar-b { width: %d%%; }', $sidebar_b_width);
}

$css[] = sprintf('#maininner { width: %d%%; }', $maininner_width);

// all sidebars right
if (($sidebar_a == 'right' || !$sidebar_a) && ($sidebar_b == 'right' || !$sidebar_b))
{
	$sidebar_classes .= ($sidebar_a) ? 'sidebar-a-right ' : '';
	$sidebar_classes .= ($sidebar_b) ? 'sidebar-b-right ' : '';

// all sidebars left
}
elseif (($sidebar_a == 'left' || !$sidebar_a) && ($sidebar_b == 'left' || !$sidebar_b))
{
	$sidebar_classes .= ($sidebar_a) ? 'sidebar-a-left ' : '';
	$sidebar_classes .= ($sidebar_b) ? 'sidebar-b-left ' : '';
	$css[] = sprintf('#maininner { float: %s; }', $rtl ? 'left' : 'right');

// sidebar-a left and sidebar-b right
}
elseif ($sidebar_a == 'left')
{
	$sidebar_classes .= 'sidebar-a-left sidebar-b-right ';
	$css[] = '#maininner, #sidebar-a { position: relative; }';
	$css[] = sprintf('#maininner { %s: %d%%; }', $rtl ? 'right' : 'left', $sidebar_a_width);
	$css[] = sprintf('#sidebar-a { %s: -%d%%; }', $rtl ? 'right' : 'left', $maininner_width);

// sidebar-b left and sidebar-a right
}
elseif ($sidebar_b == 'left')
{
	$sidebar_classes .= 'sidebar-a-right sidebar-b-left ';
	$css[] = '#maininner, #sidebar-a, #sidebar-b { position: relative; }';
	$css[] = sprintf('#maininner, #sidebar-a { %s: %d%%; }', $rtl ? 'right' : 'left', $sidebar_b_width);
	$css[] = sprintf('#sidebar-b { %s: -%d%%; }', $rtl ? 'right' : 'left', $maininner_width + $sidebar_a_width);
}

// number of sidebars
if ($sidebar_a && $sidebar_b)
{
	$sidebar_classes .= 'sidebars-2 ';
}
elseif ($sidebar_a || $sidebar_b)
{
	$sidebar_classes .= 'sidebars-1 ';
}

// generate css for dropdown menu
foreach (array(1 => '.dropdown', 2 => '.columns2', 3 => '.columns3', 4 => '.columns4') as $i => $class)
{
	$css[] = sprintf('#menu %s { width: %dpx; }', $class, $i * intval($this['config']->get('menu_width')));
}



//less variables to be set

$less_vars = array("num_cols", "col-margin-right", "textColour", "primaryColour", "secondaryColour",
	"tertiaryColour",
	"bodyBackgroundColour", "bodyWidth", "baseFontSize", "multiplier", "baseFontFamily", "baseFontWeight",
	"headingsFontFamily", "headingsFontWeight", "col-identifier", "col-identifier-names", "transitionTime", "pie",
	"borderColour", "borderStyle", "borderColour");

//TODO: Allow generation of variable array for those that exist
/*function getLessVars($paramArray) {
	$tempArray = array();
	if (is_array($paramArray)) {
		foreach ($paramArray as $stringValue)
		{
			if($this['config']->get($stringValue) > 0) {
				$tempArray[] += $this['config']->get($stringValue);
				//somehow combine the arrays?
			}
		}

	}
	return $tempArray;
}*/
//check for LESS configuration
if ($this['config']->get('less') == "1")
{
	$less_dir = $this['config']->get('lessdir');
	$add_path = "template:" . $less_dir;
	$current_path = $this['path']->path($add_path) . "/*.less";

	$destination_path = $this['path']->path("template:css");
	$less = new lessc;
	if ($this['config']->get('less_compress') == "1")
	{
		$less->setFormatter("compressed");
	}
	$less->addImportDir($this['path']->path($add_path) . "/imports/");

	//check for config and set variables accordingly
	if ($this['config']->get('num_cols') > 0) {
		$less->setVariables(array(
			"num-cols" => $this['config']->get('num_cols')
		));
	}
	if ($this['config']->get('col-margin-right') > 0) {
		$less->setVariables(array(
			"col-margin-right" => $this['config']->get('col-margin-right')
		));
	}
	if ($this['config']->get('textColour')) {
		$less->setVariables(array(
			"textColour" => $this['config']->get('textColour')
		));
	}
	if ($this['config']->get('primaryColour')) {
		$less->setVariables(array(
			"primaryColour" => $this['config']->get('primaryColour')
		));
	}
	if ($this['config']->get('secondaryColour')) {
		$less->setVariables(array(
			"secondaryColour" => $this['config']->get('secondaryColour')
		));
	}
	if ($this['config']->get('tertiaryColour')) {
		$less->setVariables(array(
			"tertiaryColour" => $this['config']->get('tertiaryColour')
		));
	}
	if ($this['config']->get('bodyBackgroundColour')) {
		$less->setVariables(array(
			"bodyBackgroundColour" => $this['config']->get('bodyBackgroundColour')
		));
	}
	if ($this['config']->get('bodyWidth')) {
		$less->setVariables(array(
			"bodyWidth" => $this['config']->get('bodyWidth')
		));
	}
	//loop through all files in folder, creating a CSS file
	foreach (glob($current_path) as $file)
	{
		if ($this['config']->get('less_cache') == "1")
		{
			autoCompileLess($file, $destination_path . "/" . basename($file, ".less") . ".css", $less);
		}
		else
		{
			$less->compileFile($file, $destination_path . "/" . basename($file, ".less") . ".css");
		}
	}
	//check to see if Bootstrap is enabled.  If so, compile those files too
	if ($this['config']->get('grid_system') == "bootstrap" || $this['config']->get('bootstrap-css') == "1")
	{
		$bootstrap_less_path = $this['path']->path("css:bootstrap/less");

		//set configured LESS variables based off of parameters
		$less->setVariables(array("bodyBackground" => $this['config']->get("bootstrap_bodyBackground"), "textColor" => $this['config']->get("bootstrap_textColor"), "sansFontFamily" => $this['config']->get("bootstrap_sansFontFamily"), "serifFontFamily" => $this['config']->get("bootstrap_serifFontFamily"), "baseFontSize" => $this['config']->get("bootstrap_baseFontSize"), "baseFontFamily" => $this['config']->get("bootstrap_baseFontFamily"), "altFontFamily" => $this['config']->get("bootstrap_altFontFamily"), "headingsColor" => $this['config']->get("bootstrap_headingsColor"), "heroUnitBackground" => $this['config']->get("bootstrap_heroUnitBackground"), "heroUnitHeadingColor" => $this['config']->get("bootstrap_heroUnitHeadingColor"), "heroUnitLeadColor" => $this['config']->get("bootstrap_heroUnitLeadColor"), "baseLineHeight" => $this['config']->get("bootstrap_baseLineHeight"), "gridColumns" => $this['config']->get("bootstrap_gridColumns"), "gridColumnWidth" => $this['config']->get("bootstrap_gridColumnWidth"), "gridGutterWidth" => $this['config']->get("bootstrap_gridGutterWidth"), "gridGutterWidth1200" => $this['config']->get("bootstrap_gridGutterWidth1200"), "gridGutterWidth768" => $this['config']->get("bootstrap_gridGutterWidth768"), "gridColumnWidth1200" => $this['config']->get("bootstrap_gridColumnWidth1200"), "gridColumnWidth768" => $this['config']->get("bootstrap_gridColumnWidth768")));


		if ($this['config']->get('less_cache') == "1")
		{
			autoCompileLess($bootstrap_less_path . "/bootstrap.less", $destination_path . "/bootstrap.css", $less);
		}
		else
		{
			$less->compileFile($bootstrap_less_path . "/bootstrap.less", $destination_path . "/bootstrap.css");
		}
	}
	if ($this['config']->get('toastr') == "1")
	{
		$toastr_less_path = $this['path']->path("lib:toastr");

		if ($this['config']->get('less_cache') == "1")
		{
			autoCompileLess($toastr_less_path . "/toastr.less", $destination_path . "/toastr.css", $less);
		}
		else
		{
			$less->compileFile($toastr_less_path . "/toastr.less", $destination_path . "/toastr.css");
		}
	}
	if ($this['config']->get('chosen') == "1")
	{
		$chosen_less_path = $this['path']->path("lib:chosen");

		if ($this['config']->get('less_cache') == "1")
		{
			autoCompileLess($chosen_less_path . "/chosen.less", $destination_path . "/chosen.css", $less);
		}
		else
		{
			$less->compileFile($chosen_less_path . "/chosen.less", $destination_path . "/chosen.css");
		}
	}
}

//check for SCSS configuration
if ($this['config']->get('sass') == "1")
{
	$sass_dir = $this['config']->get('sassdir');
	$warp_path = "template:" . $sass_dir;
	$add_path = $this['path']->path($warp_path) . "/";
	//check to see if foundation is enabled
	if ($this['config']->get("grid_system") == "foundation")
	{
		$add_path = $this['path']->path("css:foundation");
	}
	$scss = new scssc();
	if ($this['config']->get('sass_compress') == "1")
	{
		$scss->setFormatter("scss_formatter_compressed");
	}
	$scss->addImportPath($add_path . "/partials/");
	//loop through all files in folder, serving css to client
	var_dump($add_path); //for debugging
	$server = new scss_server($add_path, null, $scss);
	$server->serve();
}
function autoCompileLess($inputFile, $outputFile, $compiler)
{
	// load the cache
	$cacheFile = $inputFile . ".cache";

	if (file_exists($cacheFile))
	{
		$cache = unserialize(file_get_contents($cacheFile));
	}
	else
	{
		$cache = $inputFile;
	}

	$less = $compiler;

	$newCache = $less->cachedCompile($cache);

	if (!is_array($cache) || $newCache["updated"] > $cache["updated"])
	{
		file_put_contents($cacheFile, serialize($newCache));
		file_put_contents($outputFile, $newCache['compiled']);
	}
}

// load css
/*$this['asset']->addFile('css', 'css:reset.css');
$this['asset']->addFile('css', 'css:base.css');*/

if ($this['config']->get('yootheme-css') == "1" || $this['config']->get('grid_system') == "yoo")
{
	$this['asset']->addFile('css', 'css:yootheme/layout.css');
	$this['asset']->addFile('css', 'css:yootheme/menus.css');
	$this['asset']->addString('css', implode("\n", $css));
	$this['asset']->addFile('css', 'css:yootheme/modules.css');
	$this['asset']->addFile('css', 'css:yootheme/tools.css');
	$this['asset']->addFile('css', 'css:yootheme/system.css');
	$this['asset']->addFile('css', 'css:extensions.css');
	$this['asset']->addFile('css', 'css:custom.css');

	if (($color = $this['config']->get('color1')) && $this['path']->path("css:/yootheme/color1/$color.css"))
	{
		$this['asset']->addFile('css', "css:/yootheme/color1/$color.css");
	}
	if (($color = $this['config']->get('color2')) && $this['path']->path("css:/yootheme/color2/$color.css"))
	{
		$this['asset']->addFile('css', "css:/yootheme/color2/$color.css");
	}
	if (($font = $this['config']->get('font1')) && $this['path']->path("css:/yootheme/font1/$font.css"))
	{
		$this['asset']->addFile('css', "css:/yootheme/font1/$font.css");
	}
	if (($font = $this['config']->get('font2')) && $this['path']->path("css:/yootheme/font2/$font.css"))
	{
		$this['asset']->addFile('css', "css:/yootheme/font2/$font.css");
	}
	if (($font = $this['config']->get('font3')) && $this['path']->path("css:/yootheme/font3/$font.css"))
	{
		$this['asset']->addFile('css', "css:/yootheme/font3/$font.css");
	}
	if ($this['config']->get('direction') == 'rtl')
	{
		$this['asset']->addFile('css', 'css:/yoothemertl.css');
	}
	$this['asset']->addFile('css', 'css:yootheme/responsive.css');
	$this['asset']->addFile('css', 'css:yootheme/print.css');
}
elseif ($this['config']->get("bootstrap-css") == "1" || $this['config']->get("grid_system") == "bootstrap")
{
	$this['asset']->addFile('css', 'css:bootstrap.css'); //NOTE: Bootstrap's grid is merged together
}
elseif ($this['config']->get("grid_system") == "1140")
{
	$this['asset']->addFile('css', 'css:1140/1140.css');
}
elseif ($this['config']->get("grid_system") == "ggs")
{
	$this['asset']->addFile('css', 'css:ggs/ggs.css');
}

if ($this['config']->get("icomoon") == "1")
{
	$this['asset']->addFile('css', 'css:icomoon/style.css');
	if ($this['useragent']->browser() == 'msie')
	{

		// add conditional comments
		$head[] = sprintf('<!--[if lte IE 7]><script src="%s"></script><![endif]-->', $this['path']->url('js:lte-ie7.js'));
	}
}


//now that grid systems are loaded, time to load main styles
//check for LESS styler and configuration
if ($this['config']->get("lessStyler") == "1") {
	$this['asset']->addFile("js", "js:less-1.3.3.min.js");
	//less file added in head
	$head[] = sprintf('<link rel = "stylesheet/less" type = "text/css" href ="%s"></script>', $this['path']->url('less:style.less'));
}
else {
	$this['asset']->addFile('css', 'css:style.css');
}


// load fonts
$http = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$fonts = array('droidsans' => 'template:fonts/droidsans.css', 'opensans' => 'template:fonts/opensans.css', 'yanonekaffeesatz' => 'template:fonts/yanonekaffeesatz.css', 'mavenpro' => 'template:fonts/mavenpro.css', 'kreon' => 'template:fonts/kreon.css');

foreach (array_unique(array($this['config']->get('font1'), $this['config']->get('font2'), $this['config']->get('font3'))) as $font)
{
	if (isset($fonts[$font]))
	{
		$this['asset']->addFile('css', $fonts[$font]);
	}
}

// set body css classes
$body_classes = $sidebar_classes . ' ';
$body_classes .= $this['system']->isBlog() ? 'isblog ' : 'noblog ';
$body_classes .= $this['config']->get('page_class');

$this['config']->set('body_classes', $body_classes);

// add social buttons
$body_config['twitter'] = (int)$this['config']->get('twitter', 0);
$body_config['plusone'] = (int)$this['config']->get('plusone', 0);
$body_config['facebook'] = (int)$this['config']->get('facebook', 0);

$this['config']->set('body_config', json_encode($body_config));

// add javascripts

/**
 * Load scripts from CDN if the user has specified so; otherwise, load scripts from local drive.
 * Host must be in HTTP mode, or else we will have problems with Cross-site permissions
 */
function setJquery($this)
{
	if ($this['config']->get('jquery_cdn') == "1")
	{
		if ($this['config']->get('ajaxify') == '1')
		{
			// load older jQuery
			wp_enqueue_script("jquerycdnnew", "https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js");
		}
		else
		{
			// load older jQuery
			wp_enqueue_script("jquerycdnold", "https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js");
		}
	}
	else
	{
		if ($this['config']->get('ajaxify') == '1')
		{
			// load older jQuery
			wp_enqueue_script("jquery");
		}
		else
		{
			// load older jQuery
			$this['asset']->addFile('js', 'lib:jquery/jquery.js');
		}
	}
}

setJquery($this);
//now load remaining scripts, depending on parameters
if ($this['config']->get('cdn') == "1" && $http != "https")
{ //if the user has specified that CDN is to be used
	if ($this['config']->get('modernizr') == "1")
	{
		wp_enqueue_script("modernizr", "http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js");
	}
	if ($this['config']->get('bootstrap') == "1")
	{
		wp_enqueue_script("twitterbootstrap", "//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.0/js/bootstrap.min.js");
	}
	if ($this['config']->get('scrollto') == "1")
	{
		wp_enqueue_script("jquery-easing", "http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js");
		wp_enqueue_script("jquery-scrollto", "http://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/1.4.3/jquery.scrollTo.min.js");
	}
	if ($this['config']->get('masonry') == "1")
	{
		wp_enqueue_script("masonry", "http://cdnjs.cloudflare.com/ajax/libs/masonry/2.1.04/jquery.masonry.min.js");
	}
}
else
{
	//load modernizr
	if ($this['config']->get('modernizr') == "1")
	{
		$this['asset']->addFile('js', 'lib:modernizr/modernizr.custom.min.js');
	}
	//load bootstrap
	if ($this['config']->get('bootstrap') == "1")
	{
		$this['asset']->addFile('js', 'lib:bootstrap/bootstrap.min.js');
	}
	if ($this['config']->get('scrollto') == "1")
	{
		$this['asset']->addFile('js', 'lib:jquery-easing/jquery.easing.min.js');
		$this['asset']->addFile('js', 'lib:jquery-scrollto/jquery.scrollTo.min.js');
	}
	if ($this['config']->get('masonry') == "1")
	{
		$this['asset']->addFile('js', 'lib:masonry/jquery.masonry.min.js');
	}
}
$this['asset']->addFile('js', 'js:warp.js');
$this['asset']->addFile('js', 'js:responsive.js');
$this['asset']->addFile('js', 'js:accordionmenu.js');
$this['asset']->addFile('js', 'js:dropdownmenu.js');


$this['asset']->addFile('js', 'js:dropdownmenu.js');

if ($this['config']->get('jmpress') == "1")
{
	//loading ajaxify and jmpress will conflict because they both use ajax
}

if ($this['config']->get('dynslide') == "1")
{
	$this['asset']->addFile('js', 'lib:jmpress/jmpress.js');
}
$this['asset']->addFile('js', 'js:template.js');

if ($this['config']->get('ajaxify') == "1")
{
	$this['asset']->addFile('js', 'lib:history/jquery.history.js');
	if ($this['config']->get('scrollto') == "0")
	{
		$this['asset']->addFile('js', 'lib:scrollto/jquery.scrollTo.js');
	}
	$this['asset']->addFile('js', 'js:ajaxify-html5.js');
}

if ($this['config']->get('loader') == "1")
{
	$this['asset']->addFile('js', 'js:loader.js');
}

if ($this['config']->get('toastr') == "1")
{
	$this['asset']->addFile('js', 'lib:toastr/toastr.js');
}

//chosen
if ($this['config']->get('chosen') == "1")
{
	$this['asset']->addFile('js', 'lib:chosen/chosen.js');
}
// internet explorer
if ($this['useragent']->browser() == 'msie')
{

	// add conditional comments
	$head[] = sprintf('<!--[if lte IE 7]><script src="%s"></script><![endif]-->',
		$this['path']->url('js:lte-ie7.min.js'));
	$head[] = sprintf('<!--[if lte IE 9]><script src="%s"></script><![endif]-->', $this['path']->url('js:IE9.js'));

}

// add $head
if (isset($head))
{
	$this['template']->set('head', implode("\n", $head));
}