<meta charset = "<?php bloginfo('charset'); ?>"/>
<meta http-equiv = "X-UA-Compatible" content = "IE=edge,chrome=1">
<?php if ($this['config']->get('responsive', false)): ?>
<meta name = "viewport" content = "width=device-width, initial-scale=1">
<?php endif; ?>
<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
<link rel = "shortcut icon" href = "<?php echo $this['path']->url('template:favicon.ico');?>"/>
<link rel = "apple-touch-icon-precomposed" href = "<?php echo $this['path']->url('template:apple_touch_icon.png'); ?>"/>
<?php

//wp_enqueue_script('jquery');
wp_head();

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

	if ($styles)
	{
		// cache styles and check for remote styles
		$styles = array($this['asset']->cache('template.css', $styles, $filters, $options));
		foreach ($styles[0] as $style)
		{
			if ($style->getType() == 'File' && !$style->getPath())
			{
				$styles[] = $style;
			}
		}
	}

	if ($scripts)
	{
		// cache scripts and check for remote scripts
		$scripts = array($this['asset']->cache('template.js', $scripts, array('JSCompressor'), $options));
		foreach ($scripts[0] as $script)
		{
			if ($script->getType() == 'File' && !$script->getPath())
			{
				$scripts[] = $script;
			}
		}
	}

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

// add feed link
if (strlen($this['config']->get('rss_url', '')))
{
	printf("<link href=\"%s\" rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS 2.0\" />\n", $this['config']->get('rss_url'));
}

$this->output('head');