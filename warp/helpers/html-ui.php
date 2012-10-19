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
 Filename:  html-ui.php
 =============================================================================
 This file is responsible for providing a bulk of useful php functions to output consisten html for bootstrap.
Shoutout to ellislab and codeigniter for their ideas for functions: https://github.com/EllisLab/CodeIgniter/blob/develop/system/helpers/html_helper.php

 --------------------------------------------------------------------------------------------------------------------- */

class HtmlHelper extends WarpHelper
{
	function _stringify_attributes($attributes, $js = FALSE)
	{
		$atts = NULL;

		if (empty($attributes))
		{
			return $atts;
		}

		if (is_string($attributes))
		{
			return ' ' . $attributes;
		}

		$attributes = (array)$attributes;

		foreach ($attributes as $key => $val)
		{
			$atts .= ($js) ? $key . '=' . $val . ',' : ' ' . $key . '="' . $val . '"';
		}

		return rtrim($atts, ',');
	}
	function _list($type = 'ul', $list, $attributes = '', $depth = 0)
	{
		// If an array wasn't submitted there's nothing to do...
		if (!is_array($list))
		{
			return $list;
		}

		// Set the indentation based on the depth
		$out = str_repeat(' ', $depth);

		// Write the opening list tag
		$out .= '<' . $type . _stringify_attributes($attributes) . ">\n";

		// Cycle through the list elements.  If an array is
		// encountered we will recursively call _list()

		static $_last_list_item = '';
		foreach ($list as $key => $val)
		{
			$_last_list_item = $key;

			$out .= str_repeat(' ', $depth + 2) . '<li>';

			if (!is_array($val))
			{
				$out .= $val;
			}
			else
			{
				$out .= $_last_list_item . "\n" . _list($type, $val, '', $depth + 4) . str_repeat(' ', $depth + 2);
			}

			$out .= "</li>\n";
		}

		// Set the indentation for the closing tag and apply it
		return $out . str_repeat(' ', $depth) . '</' . $type . ">\n";
	}

	//actual methods here --
	public function printAlert($class = null, $title = null, $content = null)
	{
		switch ($class)
		{
			case "success":
				?>
				<div class="alert alert-success">
				<?php
				break;
			case "info":
				?>
				<div class="alert alert-info">
				<?php
				break;
			case "alert":
			default:
				?>
				<div class="alert">
				<?php
		}
		//title
		?>
		<p class="title">
			<?php
				echo $title;
			?>
		</p><p class="alert-description">
		<?php
		echo $content;
		?>
		</p></div>
		<?php
	}

	/**
	 * Heading
	 *
	 * Generates an HTML heading tag.
	 *
	 * @param	string	content
	 * @param	int	heading level
	 * @param	string
	 * @return	string
	 */
	public function heading($data = '', $h = '1', $attributes = '')
	{
		return '<h'.$h.$this->_stringify_attributes($attributes).'>'.$data.'</h'.$h.'>';
	}
	/**
	 * Unordered List
	 *
	 * Generates an HTML unordered list from an single or multi-dimensional array.
	 *
	 * @param    array
	 * @param    mixed
	 *
	 * @return    string
	 */
	public function ul($list, $attributes = '')
	{
		return $this->_list('ul', $list, $attributes);
	}
	/**
	 * Ordered List
	 *
	 * Generates an HTML ordered list from an single or multi-dimensional array.
	 *
	 * @param    array
	 * @param    mixed
	 *
	 * @return    string
	 */
	public function ol($list, $attributes = '')
	{
		return $this->_list('ol', $list, $attributes);
	}
}
?>