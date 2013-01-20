<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
    Class: CheckWarpHelper
        System check helper class
*/
class CheckWarpHelper extends WarpHelper {
    
    protected $issues;

    /*
        Function: getIssues
            Retrieve issues by type (critical, notice)

        Returns:
			Array
    */
    public function getIssues($type) {
		return isset($this->issues[$type]) ? $this->issues[$type] : array();
    }

    /*
        Function: checkWritable
            Check if directory is writable

        Returns:
			Boolean
    */
    public function checkWritable($directory) {

		$writable = is_writable($directory);

		if (!$writable) {
            $this->issues['critical'][] = sprintf("Directory not writable: %s.", $this->_relativePath($directory));
		}

		return $writable;
    }

    /*
        Function: checkCommon
            Do all common checks

        Returns:
			Void
    */
    public function checkCommon() {
 
        // check php version
		$current  = phpversion();
		$required = '5.2.7';

        if (version_compare($required, $current, '>=')) {
           $this->issues['critical'][] = "<a href=\"http://php.net\">PHP</a> version {$current} is too old. Make sure to install {$required} or newer.";
        }

        // check json support
        if (!function_exists('json_decode')) {
           $this->issues['critical'][] = 'No <a href="http://php.net/manual/en/book.json.php">JSON</a> support available.';
        }

		// check dom xml support
        if (!class_exists('DOMDocument')) {
           $this->issues['critical'][] = 'No <a href="http://www.php.net/manual/en/book.dom.php">DOM XML</a> support available.';
        }

		// check multibyte string support
        if (!extension_loaded('mbstring')) {
           $this->issues['notice'][] = 'No <a href="http://php.net/manual/en/book.mbstring.php">Multibyte string (mbstring)</a> support available.';
        }

    }

    /*
        Function: checkjQuery
            Check for multiple jQuery versions

        Returns:
			Void
    */
    public function checkjQuery($directory) {

		$matches = array();

		foreach ((array) $directory as $dir) {
			foreach ($this->_readDirectory($dir, $this->_relativePath($dir), '/jquery([a-zA-Z0-9_.-])*\.js$/') as $file) {

				// whitelist ?
				if (preg_match('/zoo|widgetkit/', $file)) {
					continue;
				}
				
				$matches[] = $file;
			}
		}

		if (count($matches)) {
           $this->issues['notice'][] = "Multiple jQuery Libraries found. Please make sure these don't conflict each other. <br>".implode("<br>", $matches);
		}

    }

	/*
		Function: _relativePath
			Create relative path to system directory

		Parameters:
			$path - Path

		Returns:
			String
	*/
	protected function _relativePath($path) {
		return preg_replace('/'.preg_quote(str_replace(DIRECTORY_SEPARATOR, '/', $this['system']->path), '/').'/i', '', str_replace(DIRECTORY_SEPARATOR, '/', $path), 1).'/';
	}

	/*
		Function: _readDirectory
			Read files form a directory

		Parameters:
			$path - Path to files
			$prefix - Prefix
			$filter - Filter
			$recursive - Recursive

		Returns:
			Array
	*/
	protected function _readDirectory($path, $prefix = '', $filter = false, $recursive = true) {

		$files  = array();
	    $ignore = array('.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin');

		foreach (scandir($path) as $file) {
			
			// ignore file ?
	        if (in_array($file, $ignore)) {
				continue;
			}

			// get files
            if (is_dir($path.'/'.$file) && $recursive) {
            	$files = array_merge($files, $this->_readDirectory($path.'/'.$file, $prefix.$file.'/', $filter, $recursive));
			} else {

				// filter file ?
				if ($filter && !preg_match($filter, $file)) {
					continue;
				}
				
				$files[] = $prefix.$file;
            }
		}

		return $files;
	}

}