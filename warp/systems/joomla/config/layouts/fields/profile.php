<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

require_once($this['path']->path('admin:/components/com_menus/helpers/menus.php'));

// profile options
$select   = array();
$selected = array();
$profiles = $config->get('profile_data', array('default' => array()));

foreach ($profiles as $profile => $values) {
	$attr = array('value' => $profile);

	// is checked ?
	if ((string) $config->get('profile_default') == $profile) {
		$attr = array_merge($attr, array('selected' => 'selected'));
	}

	$select[]   = sprintf('<option %s >%s</option>', $control->attributes($attr, array('selected')), $profile);
	$selected[] = sprintf('<option %s >%s</option>', $control->attributes($attr), $profile);
}

?>

<select class="profile" name="<?php echo $name; ?>"><?php echo implode("\n", $selected); ?></select>			

<div id="profile">

	<select><?php echo implode("\n", $select); ?></select>			

	<a class="add" href="#">Add</a>
	<a class="rename" href="#">Rename</a>
	<a class="remove" href="#">Remove</a>
	<a class="assign" href="#">Assign Pages</a>

	<div class="items">
		<select name="items" size="15" multiple="multiple">
			<?php foreach (MenusHelper::getMenuLinks() as $menu) : ?>
			<?php if (count($menu->links)) : ?>
			<optgroup label="<?php echo $menu->title; ?>">
				<?php foreach ($menu->links as $link) :	?>
				<option value="<?php echo (int) $link->value; ?>"><?php echo $link->text; ?></option>
				<?php endforeach; ?>
			</optgroup>
			<?php endif; ?>
			<?php endforeach; ?>
		</select>
	</div>

	<?php foreach ($config->get('profile_map', array()) as $page => $profile): ?>
		<?php printf('<input %s />', $control->attributes(array('type' => 'hidden', 'name' => "profile_map[$page]", 'value' => $profile))); ?>
	<?php endforeach; ?>

</div>