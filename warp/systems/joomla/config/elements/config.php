<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldConfig extends JFormField {

	protected $type = 'Config';

	protected function getInput() {

		// copy callback
		$this->copyAjaxCallback();

		// load config
		require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/config.php');

		// get warp
		$warp = Warp::getInstance();
		$warp['system']->document->addScript($warp['path']->url('lib:jquery/jquery.js'));
		$warp['system']->document->addScript($warp['path']->url('config:js/config.js'));
		$warp['system']->document->addScript($warp['path']->url('config:js/admin.js'));
		$warp['system']->document->addStyleSheet($warp['path']->url('config:css/config.css'));
		$warp['system']->document->addStyleSheet($warp['path']->url('config:css/admin.css'));

		// render config
		return $warp['template']->render('config:layouts/config');
	}

	protected function copyAjaxCallback() {

		$source = dirname(__FILE__).'/warp-ajax.php';
		$target = JPATH_ROOT.'/administrator/templates/system/warp-ajax.php';

		if (!file_exists($target) || md5_file($source) != md5_file($target)) {
			JFile::copy($source, $target);
		}

	}

}