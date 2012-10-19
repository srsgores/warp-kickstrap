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

     warp-kickstrap Joomla Template
     Licensed under the GNU Public License

	=============================================================================
	Filename:  config.php
	=============================================================================
	 This file displays the template's admin settings when you click on the template name.  From here, you can configure profiles, styles, layouts, and all template settings.
	 All template settings are stored in config.xml, and all the fields in that file are outputted here.

--------------------------------------------------------------------------------------------------------------------- */


// get config
$config = $this['system']->config;

// get config xml
$xml = $this['dom']->create($this['path']->path('template:config.xml'), 'xml');
$warpxml = $this['dom']->create($this['path']->path('warp:warp.xml'), 'xml');

echo '<ul id="config" data-warpversion="' . ($warpxml->first('version')->text()) . '">';

// render fields
foreach ($xml->find('fields') as $fields)
{

	// init vars
	$name = $fields->attr('name');
	$content = '';

	if ($name == 'Profiles')
	{

		// get profile data
		$profiles = $config->get('profile_data', array('default' => array()));

		// render profiles
		foreach ($profiles as $profile => $values)
		{
			$content .= $this->render('config:layouts/fields', array('config' => $config, 'fields' => $fields, 'values' => $this['data']->create($values), 'prefix' => "profile_data[$profile]", 'attr' => array('data-profile' => $profile)));
		}

	}
	else
	{
		$content = $this->render('config:layouts/fields', array('config' => $config, 'fields' => $fields, 'values' => $config, 'prefix' => 'config', 'attr' => array()));
	}

	printf('<li class="%s" data-name="%s">%s</li>', $name, $name, $content);
}

echo '</ul>';