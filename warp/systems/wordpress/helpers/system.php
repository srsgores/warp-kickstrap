<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: SystemWarpHelper
		Wordpress system helper class, provides Wordpress integration (http://wordpress.org)
*/
class SystemWarpHelper extends WarpHelper {

	/* system path */
	public $path;

	/* system url */
	public $url;

	/* cache path */
	public $cache_path;

	/* cache time */
	public $cache_time;

	/* configuration */
	public $config;

	/* theme xml */
	public $xml;

	/* query */
	public $query;
	
	/* all menu items options */
	public $menu_item_options;
	
	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct() {
		parent::__construct();

		// init vars
		$this->path              = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', ABSPATH), '/');
		$this->url               = rtrim(site_url(), '/');
		$this->cache_path        = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', get_template_directory()), '/').'/cache';
		$this->cache_time        = 86400;
		$this->menu_item_options = $this['option']->get('menu-items', array());

		// get config
		if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE) {
			$settings = $this['option']->get('warp_theme_options', array());
		} else {
			$settings = ($file = $this['path']->path('template:config')) ? file_get_contents($file) : array();
		}

		// set config or load defaults
		if (count($settings)) {
			$this->config = $this['data']->create($settings);
		} else {
			$this->config = $this['data']->create(file_get_contents($this['path']->path('template:config.default')));
		}

		// set cache directory
		if (!file_exists($this->cache_path)) {
			mkdir($this->cache_path, 0755);
		}
	}

	/*
		Function: init
			Initialize system configuration

		Returns:
			Void
	*/
	public function init() {

		// set paths
		$this['path']->register($this->path.'/wp-admin', 'admin');
		$this['path']->register($this->path, 'site');
		$this['path']->register($this->cache_path, 'cache');
		$this['path']->register($this['path']->path('warp:systems/wordpress/menus'), 'menu');
		$this['path']->register($this['path']->path('warp:systems/wordpress/widgets'), 'widgets');
		$this['path']->register($this['path']->path('template:').'/widgets', 'widgets');

		// Enable thumbnail support for posts
		add_theme_support( 'post-thumbnails' );

		// set translations
		load_theme_textdomain('warp', $this['path']->path('template:languages'));

		// get theme xml
		$this->xml = $this['dom']->create($this['path']->path('template:template.xml'), 'xml');

		// get module positions
		foreach ($this->xml->find('positions > position') as $position) {
            $this['modules']->register($position->text());
		}

		// load widgets
		foreach ($this['path']->dirs('widgets:') as $name) {
			if ($file = $this['path']->path("widgets:{$name}/{$name}.php")) {
				require_once($file);
			}
		}

		// add actions
		add_action('wp_ajax_warp_search', array($this, 'ajaxSearch'));
		add_action('wp_ajax_nopriv_warp_search', array($this, 'ajaxSearch'));

		// register main menu	
		register_nav_menus(array('main_menu' => 'Main Navigation Menu'));

		// is admin or site
		if (is_admin()) {

			// set paths
	        $this['path']->register($this['path']->path('warp:config'), 'config');
	        $this['path']->register($this['path']->path('warp:systems/wordpress/config'), 'config');
			
			// add actions
			add_action('admin_init', array($this, '_adminInit'));
		    add_action('admin_menu', array($this, '_adminMenu'));

			// add notices
			if (isset($_GET['page']) && in_array($_GET['page'], array('warp', 'warp_widget'))) {
				add_action('admin_notices', array($this, '_adminNotices'));
			}

		} else {

			// add action
			add_action('wp', array($this, '_wp'));
			add_action("get_sidebar", array($this, '_get_sidebar'));

            // remove auto-linebreaks ?
            if (!$this->config->get('wpautop', 1)) {
            	remove_filter('the_content', 'wpautop');
			}

			// set custom menu walker
            add_filter('wp_nav_menu_args', create_function('$args','if (empty($args["walker"])) $args["walker"] = new WarpMenuWalker();return $args;'));

            // filter widgets that should not be displayed
			add_filter('widget_display_callback', create_function('$instance,$widget,$args','$warp = Warp::getInstance(); return $warp["widgets"]->get($widget->id)->display ? $instance : false;'), 10, 3);
			
			// disable the admin bar for mobiles
			if ($this->config->get('mobile') && $this['browser']->isMobile()) {
				add_theme_support('admin-bar', array('callback' => '__return_false'));
			}
		}
	}

	/*
		Function: getQuery
			Get current query information

		Returns:
			Object
	*/
	public function getQuery() {
        global $wp_query;

		// create, if not set
		if (empty($this->query)) {
			
			// init vars
	        $obj   = $wp_query->get_queried_object();
			$query = array();

			// find current page type
			foreach (array('home', 'front_page', 'archive', 'search', 'single', 'page', 'category') as $type) {
				if (call_user_func('is_'.$type)) {
					$query[] = $type;

					if ($type == 'page') {
						$query[] = 'page-'.$obj->ID;
					}

					if ($type == 'single') {
						$query[] = $obj->post_type;
						$query[] = $obj->post_type.'-'.$obj->ID;
					}

					if ($type == 'category') {
						$query[] = 'cat-'.$obj->cat_ID;
					}

					if ($type == 'archive') {
						$query[] = 'cat-'.$obj->term_id;
					}
				}
			}
			
			$this->query = $query;
		}

		return $this->query;
	}

	/*
		Function: getPostCount
			Retrieve current post count

		Returns:
			Int
	*/
	public function getPostCount() {
		global $wp_query;
		return $wp_query->post_count;
	}

	/*
		Function: isBlog

		Returns:
			Boolean
	*/
	public function isBlog() {
		return true;
	}

	/*
		Function: isPreview
			Checks for default widgets in theme preview 

		Returns:
			Boolean
	*/
	public function isPreview($position) {
		
		// preview postions
		$positions = array('logo', 'right');

		return is_preview() && in_array($position, $positions);
	}
	
	/*
		Function: ajaxSearch
			Ajax search callback

		Returns:
			String
	*/
	public function ajaxSearch(){
		global $wp_query;

		$result = array('results' => array());
		$query  = isset($_REQUEST['s']) ? $_REQUEST['s']:"";

		if (strlen($query) >= 3) {
			
			$wp_query->query_vars['posts_per_page'] = $this->config->get('search_results', 5);
			$wp_query->query_vars['post_status'] = 'publish';
			$wp_query->query_vars['s'] = $query;
			$wp_query->is_search = true;

			foreach ($wp_query->get_posts() as $post) {
			    
			    $content = !empty($post->post_excerpt) ? strip_tags(do_shortcode($post->post_excerpt)) : strip_tags(do_shortcode($post->post_content));
			    
			    if (strlen($content) > 180) {
			        $content = substr($content, 0, 179).'...';
			    }
			    
			    $result['results'][] = array(
					'title' => $post->post_title,
					'text'  => $content,
					'url'   => get_permalink($post->ID)
				);
			}
		}

		die(json_encode($result));
	}

	/*
		Function: _wp
			WP action callback

		Returns:
			Void
	*/
	public function _wp() {

		// set config
		$this->config->set('language', get_bloginfo("language"));
		$this->config->set('direction', $GLOBALS['wp_locale']->is_rtl() ? 'rtl' : 'ltr'); 
		$this->config->set('site_url', rtrim(get_option('siteurl'), '/')); 
		$this->config->set('site_name', get_option('blogname'));
		$this->config->set('datetime', date('Y-m-d'));
		$this->config->set('actual_date', date_i18n($this->config->get('date_format', 'l, j F Y')));
		$this->config->set('page_class', implode(' ', array_map(create_function('$element','return "wp-".$element;'), $this->getQuery()))); 

		// outdated browser page ?
		if (($this['config']->get('ie6page') && $this['browser']->isIE6()) || ($this['config']->get('outdated_browser') && $this['browser']->outdatedBrowser())) {
			$this['event']->bind('render.layouts:template', create_function('&$layout,&$args', '$args["title"] = __("Please update to a modern browser", "warp"); $args["error"] = "browser"; $args["message"] = __("outdatedBrowser_page_message", "warp"); $layout = "layouts:error";'));
		}
		
		// mobile theme ?
		if ($this['config']->get('mobile') && $this['browser']->isMobile()) {
			$this['config']->set('style', 'mobile');
		}

		// branding ?
		if ($this['config']->get('warp_branding')) {
			$this['template']->set('warp_branding', $this->warp->getBranding());
		}

		// set theme style paths
		if ($style = $this['config']->get('style')) {
			foreach (array('css' => 'template:styles/%s/css', 'js' => 'template:styles/%s/js', 'layouts' => 'template:styles/%s/layouts') as $name => $resource) {
				if ($p = $this['path']->path(sprintf($resource, $style))) {
					$this['path']->register($p, $name);
				}
			}
		}
	}

	/*
		Function: _adminInit
			Admin init action callback

		Returns:
			Void
	*/
	public function _adminInit() {
		
	    if (defined('DOING_AJAX') && DOING_AJAX && isset($_POST['task'], $_POST['warp-ajax-save'])) {
			
			$message = 'failed';
			$post 	 = function_exists('wp_magic_quotes') ? array_map('stripslashes_deep', $_POST) : $_POST;

			switch ($post['task']) {

				case 'theme-options':
					
					// update theme config
					$config  = isset($post['config']) ? $post['config'] : array();
					$config  = array_merge($config, array('profile_data' => isset($post['profile_data']) ? $post['profile_data'] : array()));
					$config  = array_merge($config, array('profile_map' => isset($post['profile_map']) ? $post['profile_map'] : array()));
					
					if (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE) {

						if ($this['option']->set('warp_theme_options', (string) $this['data']->create($config))) {  
					        $message = 'success';  
					    }

					} else {

						if (file_put_contents($this['path']->path('template:').'/config', (string) $this['data']->create($config))) {
							$message = 'success';
						}
					}

					break;

				case 'widget-options':

					// update widget options
					if (update_option('warp_widget_options', $post['warp_widget_options'])) {
						$message = 'success';
					}

					break;
			}

		    die(json_encode(compact('message')));
		}

		// add css/js
		$siteurl = sprintf('/%s/i', preg_quote(parse_url(site_url(), PHP_URL_PATH), '/'));

		if (isset($_GET['page']) && in_array($_GET['page'], array('warp', 'warp_widget'))) {
			wp_enqueue_style('warp-css-config', preg_replace($siteurl, '', $this['path']->url('config:css/config.css'), 1));
			wp_enqueue_script('warp-js-config', preg_replace($siteurl, '', $this['path']->url('config:js/config.js'), 1));
			wp_enqueue_script('warp-js-admin', preg_replace($siteurl, '', $this['path']->url('config:js/admin.js'), 1));
		}

		wp_enqueue_style('warp-css-admin', preg_replace($siteurl, '', $this['path']->url('config:css/admin.css'), 1));
		wp_enqueue_script('warp-js-wp-admin', preg_replace($siteurl, '', $this['path']->url('config:js/wp-admin.js'), 1));

		// add actions
		add_action('wp_ajax_save_nav_settings', array($this,'_save_nav_settings'));
		add_action('wp_ajax_get_nav_settings', array($this,'_get_nav_settings'));
	}

	/*
		Function: _adminNotices
			Admin notices action callback

		Returns:
			Void
	*/
	public function _adminNotices() {

		// get warp xml
		$xml = $this['dom']->create($this['path']->path('warp:warp.xml'), 'xml');

		// cache writable ?
		if (!file_exists($this->cache_path) || !is_writable($this->cache_path)) {
			$update['cache'] = "Cache not writable, please check directory permissions ({$this->cache_path})";
		}

		// update check
		if ($url = $xml->first('updateUrl')->text()) {

			// create check urls
			$urls['tmpl'] = sprintf('%s?application=%s&version=%s&format=raw', $url, get_template(), $this->xml->first('version')->text());
			$urls['warp'] = sprintf('%s?application=%s&version=%s&format=raw', $url, 'warp', $xml->first('version')->text());

			foreach ($urls as $type => $url) {

				// only check once a day 
				$hash = md5($url.date('Y-m-d'));
				if ($this['option']->get("{$type}_check") != $hash) {
					if ($request = $this['http']->get($url)) {
						$this['option']->set("{$type}_check", $hash);
						$this['option']->set("{$type}_data", $request['body']);
					}
				}

				// decode response and set message
				if (($data = json_decode($this['option']->get("{$type}_data"))) && $data->status == 'update-available') {
					$update[$type] = $data->message;
				}

			}
		}

		// show notice
		if (!empty($update)) {
			echo '<div class="update-nag">'.implode('<br>', $update).'</div>';
		}
		
		return false;
	}
	
	/*
		Function: _adminMenu
			Admin menu action callback

		Returns:
			Void
	*/
	public function _adminMenu() {

		// init vars
		$name = $this->xml->first('name')->text();
		$icon = $this['path']->url('config:images/yoo_icon_16.png');

	    add_menu_page('', $name, apply_filters('warp_edit_theme_options', 'edit_theme_options'), 'warp', false, $icon); 

		add_submenu_page('warp', 'Theme Options', 'Theme Options', apply_filters('warp_edit_theme_options', 'edit_theme_options'), 'warp', array($this, '_adminThemeOptions'));
		add_submenu_page('warp', 'Widget Options', 'Widget Options', apply_filters('warp_edit_theme_options', 'edit_theme_options'), 'warp_widget', array($this, '_adminWidgetOptions'));
	}

	/*
		Function: _adminThemeOptions
			Render admin theme options layout

		Returns:
			Void
	*/	
	public function _adminThemeOptions() {
	    echo $this['template']->render('config:layouts/theme_options', array('xml' => $this->xml));
    }

	/*
		Function: _adminWidgetOptions
			Render admin widget options layout

		Returns:
			Void
	*/	
	public function _adminWidgetOptions() {
	    
		// get module settings
		$module_settings = $this->xml->find('modulesettings > setting');

		// get position settings
		$position_settings = array();
	
		foreach ($this->xml->find('positions > position') as $position) {
			$position_settings[$position->text()] = $position;
		}
		
	    echo $this['template']->render('config:layouts/widget_options', compact('position_settings', 'module_settings'));
    }

	/*
		Function: getMenuItemOptions
			Retrieve menu by id

		Parameters:
			$id - Menu Item ID

		Returns:
			Array
	*/
	public function getMenuItemOptions($id) {
		
		$menu_settings = array(
			'columns'     => 1,
			'columnwidth' => -1,
			'image'       => ''
		);
		
		return isset($this->menu_item_options[$id]) ? $this->menu_item_options[$id] : array();
	}
	
	/*
		Function: _save_nav_settings
			Saves menu item settings

		Returns:
			Void
	*/	
	public function _save_nav_settings() {
	    
		if (isset($_POST['menu-item'])) {
			
			$menu_item_settings = $this->menu_item_options;
			
			foreach ($_POST['menu-item'] as $itemId=>$settings) {
				$menu_item_settings[$itemId] = $settings;
			}
			
			$this['option']->set('menu-items', $menu_item_settings);
			$this->menu_item_options = $menu_item_settings;
		}
		
		die();
    }
	
	/*
		Function: _get_nav_settings
			Returns menu item settings as json

		Returns:
			Boolean
	*/
	public function _get_nav_settings() {
		die(json_encode($this->menu_item_options));
    }

	/*
		Function: _get_sidebar
			Catches default sidebar content and makes it available for the sidebar widget

		Returns:
			Void
	*/
	public function _get_sidebar($name=null) {

		$templates = isset($name) ? array("sidebar-{$name}.php", "sidebar.php") : array("sidebar.php");

		ob_start();

		if ("" == locate_template($templates, true, true)) {
			load_template(ABSPATH.WPINC."/theme-compat/sidebar.php", true);
			$clear = true; 
		}

		$output = ob_get_clean();

		if (isset($clear)) {
			$output = "";
		}

		$this["template"]->set("sidebar.output", $output);
    }

}

/*
	Class: WarpMenuWalker
		Custom Menu Walker
*/
class WarpMenuWalker extends Walker_Nav_Menu {

	public function start_lvl(&$output, $depth) {
		$output .= '<ul>';
	}

	public function end_lvl(&$output, $depth) {
		$output .= '</ul>';
	}

	public function start_el(&$output, $item, $depth, $args) {

		// get warp
		$warp = Warp::getInstance();

		// init vars
		$data = array();
		$classes = empty($item->classes) ? array() : (array) $item->classes;
		$options = $warp['system']->getMenuItemOptions($item->ID);

		// set id
		$data['data-id'] = $item->ID;

		// is current item ?
		if (in_array('current-menu-item', $classes) || in_array('current_page_item', $classes)) {
			$data['data-menu-active'] = 2;

		// home/fronpage item
		} elseif ($item->url == 'index.php' && (is_home() || is_front_page())) {
			$data['data-menu-active'] = 2;
		}

		// has columns ?
		if (!empty($options['columns'])) {
			$data['data-menu-columns'] = (int) $options['columns'];			
		}

		// has columnwidth ?
		if (!empty($options['columnwidth'])) {
			$data['data-menu-columnwidth'] = (int) $options['columnwidth'];			
		}

		// has image ?
		if (!empty($options['image'])) {
			$upload = wp_upload_dir();
			$data['data-menu-image'] = trailingslashit($upload['baseurl']).$options['image'];			
		}

		// set item attributes
		$attributes = '';
		foreach ($data as $name => $value) {
			$attributes .= sprintf(' %s="%s"', $name, esc_attr($value));
		}

		// create item output
		$id = apply_filters('nav_menu_item_id', '', $item, $args);
		$output .= '<li'.(strlen($id) ? sprintf(' id="%s"', esc_attr($id)) : '').$attributes.'>';

		// set link attributes
		$attributes = '';
		foreach (array('attr_title' => 'title', 'target' => 'target', 'xfn' => 'rel', 'url' => 'href') as $var => $attr) {
			if (!empty($item->$var)) {
				$attributes .= sprintf(' %s="%s"', $attr, esc_attr($item->$var));
			}
		}

		// escape link title
		$item->title = htmlspecialchars($item->title, ENT_COMPAT, "UTF-8");

		// is separator ?
		if ($item->url == '#') {
			$format = '%s<span%s><span>%s</span></span>%s';
			$attributes = ' class="separator"';
		} else {
			$format = '%s<a%s><span>%s</span></a>%s';
		}

		// create link output
		$item_output = sprintf($format, $args->before, $attributes, $args->link_before.apply_filters('the_title', $item->title, $item->ID).$args->link_after, $args->after);

		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}
	
	public function end_el(&$output, $item, $depth) {
		$output .= '</li>';
	}	

}


/*
	Function: mb_strpos
		mb_strpos function for servers not using the multibyte string extension
*/
if (!function_exists('mb_strpos')) {
	function mb_strpos($haystack, $needle, $offset = 0) {
		return strpos($haystack, $needle, $offset);
	}
}