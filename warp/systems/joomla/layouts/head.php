<?php
/*------------------------------------------------------------------------------------------------------------------------
     Author: Sean Goresht
     www: http://seangoresht.com/
     github: https://github.com/srsgores

     twitter: http://twitter.com/S.Goresht

      warp-kickstrap Joomla Template
      Licensed under the GNU Public License

 	=============================================================================
 	Filename:  head.php
 	=============================================================================
 	 This file serves as the building block for all of warp kickstrap.  It will APPEND to the existing head created by Joomla.
 	 This file is the main location where CSS and Javascript files are loaded; other files are loaded WITHIN the template (under the root directory -- not WARP) directory in template.config.php.  They are imported using WARP's helpers.
 
 --------------------------------------------------------------------------------------------------------------------- */
?>

<meta charset = "<?php echo $this['system']->document->getCharset(); ?>"/>
<meta http-equiv = "X-UA-Compatible" content = "IE=edge,chrome=1">
<?php if ($this['config']->get('responsive', false)): ?>
<meta name = "viewport" content = "width=device-width, initial-scale=1">
<?php endif; ?>
<jdoc:include type = "head"/>
<link rel = "apple-touch-icon-precomposed" href = "<?php echo $this['path']->url('template:apple_touch_icon.png'); ?>"/>
<!-- Note: we don't even have to make a link rel for touch icons, as the apple webkit will look for the file in the root directory. -->

<?php

// get html head data
$head = $this['system']->document->getHeadData();
$http = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

// remove deprecated meta-data (html5)
unset($head['metaTags']['http-equiv']);
unset($head['metaTags']['standard']['title']);
unset($head['metaTags']['standard']['rights']);
unset($head['metaTags']['standard']['language']);

$this['system']->document->setHeadData($head);

/**
 * Load scripts from CDN if the user has specified so; otherwise, load scripts from local drive.
 * Host must be in HTTP mode, or else we will have problems with Cross-site permissions
 */
if ($this['config']->get('jquery_cdn') == "1")
{
	$this['system']->document->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js");
}
else
{
	// load jQuery, if not loaded before
	if (!$this['system']->application->get('jquery'))
	{ //if jQuery is found using warp's helpers...
		$this['system']->application->set('jquery', true); //set jquery to true
		$this['system']->document->addScript($this['path']->url('lib:jquery/jquery.js'));
	}
}

//now load remaining scripts, depending on parameters
if ($this['config']->get('cdn') == "1" && $http != "https")
{ //if the user has specified that CDN is to be used
	if ($this['config']->get('modernizr') == "1")
	{
		$this['system']->document->addScript("http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.1/modernizr.min.js");
	}
	if ($this['config']->get('bootstrap') == "1")
	{
		$this['system']->document->addScript("http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.1.0/bootstrap.min.js");
	}
	if ($this['config']->get('scrollto') == "1")
	{
		$this['system']->document->addScript("http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js");
		$this['system']->document->addScript("http://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/1.4.3/jquery.scrollTo.min.js");
	}
	if ($this['config']->get('masonry') == "1")
	{
		$this['system']->document->addScript("http://cdnjs.cloudflare.com/ajax/libs/masonry/2.1.04/jquery.masonry.min.js");
	}
}
else
{
	//load modernizr
	if ($this['config']->get('modernizr') == "1")
	{
		$this['system']->document->addScript($this['path']->url('lib:modernizr/modernizr.custom.js'));
	}
	//load bootstrap
	if ($this['config']->get('bootstrap') == "1")
	{
		$this['system']->document->addScript($this['path']->url('lib:bootstrap/bootstrap.min.js'));
	}
	if ($this['config']->get('scrollto') == "1")
	{
		$this['system']->document->addScript($this['path']->url('lib:jquery-easing/jquery.easing.min.js'));
		$this['system']->document->addScript($this['path']->url('lib:jquery-scrollto/jquery.scrollTo.min.js'));
	}
	if ($this['config']->get('masonry') == "1")
	{
		$this['system']->document->addScript($this['path']->url('lib:masonry/jquery.masonry.min.js'));
	}
}

// get styles and scripts
$styles = $this['asset']->get('css');
$scripts = $this['asset']->get('js');

// compress styles and scripts
if ($compression = $this['config']->get('compression'))
{

	$options = array();
	$filters = array('CSSImportResolver', 'CSSRewriteURL', 'CSSCompressor');

	// set options
	if ($compression == 3)
	{
		$options['Gzip'] = true;
	}

	// set filter
	if ($compression >= 2 && ($this['useragent']->browser() != 'msie' || version_compare($this['useragent']->version(), '8.0', '>=')))
	{
		$filters[] = 'CSSImageBase64';
	}

	// cache styles and check for remote styles
	if ($styles)
	{
		$styles = array($this['asset']->cache('template.css', $styles, $filters, $options));
		foreach ($styles[0] as $style)
		{
			if ($style->getType() == 'File' && !$style->getPath())
			{
				$styles[] = $style;
			}
		}
	}

	// cache scripts and check for remote scripts
	if ($scripts)
	{
		$scripts = array($this['asset']->cache('template.js', $scripts, array('JSCompressor'), $options));
		foreach ($scripts[0] as $script)
		{
			if ($script->getType() == 'File' && !$script->getPath())
			{
				$scripts[] = $script;
			}
		}
	}

	// compress joomla styles and scripts
	$head = $this['system']->document->getHeadData();
	$data = array('styleSheets' => array(), 'scripts' => array());

	foreach ($head['styleSheets'] as $style => $meta)
	{

		if (preg_match('/\.css$/i', $style))
		{
			$asset = $this['asset']->createFile($style);
			if ($asset->getPath())
			{
				$style = $this['asset']->cache(basename($style), $asset, array('CSSImportResolver', 'CSSRewriteURL', 'CSSCompressor'), $options)->getUrl();
			}
		}

		$data['styleSheets'][$style] = $meta;
	}

	foreach ($head['scripts'] as $script => $meta)
	{

		if (preg_match('/\.js$/i', $script))
		{
			$asset = $this['asset']->createFile($script);
			if ($asset->getPath())
			{
				$script = $this['asset']->cache(basename($script), $asset, array('JSCompressor'), $options)->getUrl();
			}
		}

		$data['scripts'][$script] = $meta;
	}

	$this['system']->document->setHeadData(array_merge($head, $data));
}

// add styles
if ($styles)
{
	foreach ($styles as $style)
	{
		if ($url = $style->getUrl())
		{
			printf("<link rel=\"stylesheet\" href=\"%s\" />\n", $url);
		}
		else
		{
			printf("<style>%s</style>\n", $style->getContent());
		}
	}
}

// add scripts
if ($scripts)
{
	foreach ($scripts as $script)
	{
		if ($url = $script->getUrl())
		{
			printf("<script src=\"%s\"></script>\n", $url);
		}
		else
		{
			printf("<script>%s</script>\n", $script->getContent());
		}
	}
}

$this->output('head');