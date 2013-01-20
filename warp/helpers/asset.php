<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AssetWarpHelper
		Asset helper class, to manage assets
*/
class AssetWarpHelper extends WarpHelper {

    protected $assets;
    protected $options;

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct() {
		parent::__construct();

		// init vars
		$this->assets  = array();
		$this->options = array('base_path' => $this['system']->path, 'base_url' => rtrim($this['path']->url('site:'), '/'));
	}

	/*
		Function: get
			Get a asset collection

		Parameters:
			$name - String

		Returns:
			Mixed
	*/
	public function get($name) {
		return isset($this->assets[$name]) ? $this->assets[$name] : null;
	}

	/*
		Function: createString
			Create a string asset

		Parameters:
			$input - String
			$options - Array

		Returns:
			Object
	*/
	public function createString($input, $options = array()) {
		return new WarpStringAsset($input, array_merge($options, $this->options));
	}

	/*
		Function: createFile
			Create a file asset

		Parameters:
			$input - String
			$options - Array

		Returns:
			Object
	*/
	public function createFile($input, $options = array()) {

		$url  = $input;
		$path = null;

	    if (!preg_match('/^(http|https)\:\/\//i', $input)) {

			// resource identifier ?
			if ($path = $this['path']->path($input)) {
				$url = $this['path']->url($input);
			}

			// absolute/relative path ?
			if (!$path) {
				$path = realpath($this->options['base_path'].'/'.ltrim(preg_replace('/'.preg_quote(str_replace(DIRECTORY_SEPARATOR, '/', $this->options['base_url']), '/').'/', '', $input, 1), '/'));
			}

	    }

		return new WarpFileAsset($url, $path, array_merge($options, $this->options));
	}

	/*
		Function: addString
			Add a string asset

		Parameters:
			$name - String
			$input - String
			$options - Array

		Returns:
			Void
	*/
	public function addString($name, $input, $options = array()) {
		return $this->addAsset($name, $this->createString($input, $options));
	}

	/*
		Function: addFile
			Add a file asset

		Parameters:
			$name - String
			$input - String
			$options - Array

		Returns:
			Mixed
	*/
	public function addFile($name, $input, $options = array()) {
		return $this->addAsset($name, $this->createFile($input, $options));
	}

	/*
		Function: addAsset
			Add asset object

		Parameters:
			$name - String
			$asset - Object

		Returns:
			Void
	*/
	protected function addAsset($name, $asset) {

		if (!isset($this->assets[$name])) {
			$this->assets[$name] = new WarpAssetCollection();
		}

		$this->assets[$name]->add($asset);
		
		return $asset;
	}

	/*
		Function: cache
			Apply filters and cache a asset

		Parameters:
			$file - String
			$asset - Object
			$filters - Array
			$options - Array

		Returns:
			Object
	*/
	public function cache($file, $asset, $filters = array(), $options = array()) {

		// init vars
		$hash = substr($asset->hash(serialize($filters)), 0, 8);
		$options = array_merge(array('Gzip' => false), $options);

		// copy gzip file, if not exists
		if ($options['Gzip'] && !$this['path']->path('cache:gzip.php')) {
			@copy($this['path']->path('warp:gzip/gzip.php'), rtrim($this['path']->path('cache:'), '/').'/gzip.php');
		}

		// append cache file suffix based on hash
		if ($extension = pathinfo($file, PATHINFO_EXTENSION)) {
			$file = preg_replace('/'.preg_quote('.'.$extension, '/').'$/', sprintf('-%s.%s', $hash, $extension), $file, 1);
		} else {
			$file .= '-'.$hash;
		}

		// create cache file, if not exists
		if (!$this['path']->path('cache:'.$file)) {
			@file_put_contents($this['path']->path('cache:').'/'.ltrim($file, '/'), $asset->getContent($this['assetfilter']->create($filters)));
		}

		$asset->setUrl($this['path']->url(($options['Gzip'] && $this['path']->path('cache:gzip.php') ? 'cache:gzip.php?' : 'cache:').$file));

		return $asset;
	}

}

/*
	Interface:  WarpAssetInterface
		Asset interface
*/
interface WarpAssetInterface {

	public function getUrl();

	public function setUrl($url);

	public function getContent($filter = null);

	public function setContent($content);

	public function load($filter = null);

	public function hash($salt = '');
	
}

/*
	Class:  WarpAssetOptions
		Asset options class, provides options implementation
*/
abstract class WarpAssetOptions implements ArrayAccess {

	protected $options;

	/*
		Function: __construct
			Class Constructor.
	*/
    public function __construct($options = array()) {
		$this->options = $options;
    }

	/* ArrayAccess interface implementation */

	public function offsetSet($name, $value) {
		$this->options[$name] = $value;
	}

	public function offsetGet($name)	{
		return isset($this->options[$name]) ? $this->options[$name] : null;
	}

	public function offsetExists($name) {
		return isset($this->options[$name]);
	}

	public function offsetUnset($name) {
		unset($this->options[$name]);
	}

}

/*
	Class:  WarpAssetBase
		Asset base class
*/
abstract class WarpAssetBase extends WarpAssetOptions implements WarpAssetInterface {

	protected $url;
	protected $content;
	protected $loaded = false;

	/*
		Function: getType
			Get asset type

		Returns:
			String
	*/
    public function getType() {
		return str_replace(array('Warp', 'Asset'), array('', ''), get_class($this));
	}

	/*
		Function: getUrl
			Get asset url

		Returns:
			String
	*/
    public function getUrl() {
		return $this->url;
	}

	/*
		Function: setUrl
			Set asset url

		Parameters:
			$url - String

		Returns:
			Void
	*/
    public function setUrl($url) {
		$this->url = $url;
	}

	/*
		Function: getContent
			Get asset content and apply filters

		Returns:
			String
	*/
    public function getContent($filter = null) {
		
		if (!$this->loaded) {
            $this->load($filter);
        }

		if ($filter) {
	        $asset = clone $this;
			$filter->filterContent($asset);
	        return $asset->getContent();
		}
		
		return $this->content;
	}

	/*
		Function: setContent
			Set asset content

		Parameters:
			$content - String

		Returns:
			Void
	*/
    public function setContent($content) {
		$this->content = $content;
	}

	/*
		Function: doLoad
			Load asset and apply filters

		Parameters:
			$content - String
			$filter - Object

		Returns:
			Void
	*/
    protected function doLoad($content, $filter = null) {
		$this->content = $content;
		
		if ($filter) {
			$filter->filterLoad($this);
		}
		
		$this->loaded = true;
	}

}

class WarpStringAsset extends WarpAssetBase {

	protected $string;

	/*
		Function: __construct
			Class Constructor.
	*/
    public function __construct($string, $options = array()) {
		parent::__construct($options);
		
		$this->string = $string;
    }

	/*
		Function: load
			Load asset callback

		Parameters:
			$filter - Object

		Returns:
			Void
	*/
    public function load($filter = null) {
		$this->doLoad($this->string, $filter);
	}

	/*
		Function: doLoad
			Load asset and apply filters

		Parameters:
			$content - String
			$filter - Object

		Returns:
			Void
	*/
    public function hash($salt = '') {
        return md5($this->string.$salt);
    }

}

class WarpFileAsset extends WarpAssetBase {

	protected $path;

	/*
		Function: __construct
			Class Constructor.
	*/
    public function __construct($url, $path, $options = array()) {
		parent::__construct($options);

		$this->url = $url;
		$this->path = $path;
    }

	/*
		Function: getPath
			Get asset file path

		Returns:
			String
	*/
    public function getPath() {
		return $this->path;
    }

	/*
		Function: load
			Load asset callback

		Parameters:
			$filter - Object

		Returns:
			Void
	*/
    public function load($filter = null) {
		if (file_exists($this->path)) {
			$this->doLoad(preg_replace('{^\xEF\xBB\xBF|\x1A}', '', file_get_contents($this->path)), $filter); // load with UTF-8 BOM removal
		}
    }

	/*
		Function: hash
			Get unique asset hash

		Parameters:
			$salt - String

		Returns:
			String
	*/
    public function hash($salt = '') {
        return md5($this->path.filemtime($this->path).$salt);
    }

}

class WarpAssetCollection extends WarpAssetOptions implements WarpAssetInterface, Iterator {

	protected $url;
	protected $content;
	protected $assets;

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($assets = array(), $options = array()) {
		parent::__construct($options);

		$this->assets = new SplObjectStorage();

		if (!is_array($assets)) {
			$assets = array($assets);
		}

		foreach ($assets as $asset) {
			$this->add($asset);
		}
	}

	/*
		Function: getUrl
			Get asset url

		Returns:
			String
	*/
	public function getUrl() {
		return $this->url;
	}

	/*
		Function: setUrl
			Set asset url

		Parameters:
			$url - String

		Returns:
			Void
	*/
	public function setUrl($url) {
		$this->url = $url;
	}

	/*
		Function: getContent
			Get asset content and apply filters

		Returns:
			String
	*/
	public function getContent($filter = null) {
		$content = array();

		foreach ($this as $asset) {
			$content[] = $asset->getContent($filter);
		}

		return implode("\n", $content);
	}

	/*
		Function: setContent
			Set asset content

		Parameters:
			$content - String

		Returns:
			Void
	*/
	public function setContent($content) {
		$this->content = $content;
	}

	/*
		Function: load
			Load asset callback

		Parameters:
			$filter - Object

		Returns:
			Void
	*/
	public function load($filter = null) {
		$content = array();

		foreach ($this as $asset) {
			$content[] = $asset->getContent($filter);
		}

		$this->content = implode("\n", $content);
	}

	/*
		Function: hash
			Get unique asset hash

		Parameters:
			$salt - String

		Returns:
			String
	*/
	public function hash($salt = '') {
		$hashes = array();

		foreach ($this as $asset) {
			$hashes[] = $asset->hash($salt);
		}

		return md5(implode(' ', $hashes));
	}

	/*
		Function: add
			Add asset to collection

		Parameters:
			$filter - Object

		Returns:
			Void
	*/
	public function add($asset) {
		$this->assets->attach($asset);
	}

	/*
		Function: remove
			Add asset from collection

		Parameters:
			$filter - Object

		Returns:
			Void
	*/
	public function remove($asset) {
		$this->assets->detach($asset);
	}

	/* Iterator interface implementation */
	
	public function current() {
		return $this->assets->current();
	}

	public function key() {
		return $this->assets->key();
	}

	public function valid() {
		return $this->assets->valid();
	}

	public function next() {
		$this->assets->next();
	}

	public function rewind() {
		$this->assets->rewind();
	}

}