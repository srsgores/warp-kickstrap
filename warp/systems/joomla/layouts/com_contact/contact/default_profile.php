<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

?>

<?php if (JPluginHelper::isEnabled('user', 'profile')) : ?>
<?php $fields = $this->item->profile->getFieldset('profile'); ?>
<ul class="blank">
<?php foreach ($fields as $profile) : ?>

	<li>
		<?php
			if ($profile->value) {
				
				echo $profile->label.' ';
				
				$profile->text = htmlspecialchars($profile->value, ENT_COMPAT, 'UTF-8');
				switch ($profile->id) {

					case "profile_website":
						$v_http = substr ($profile->profile_value, 0, 4);
						if ($v_http == "http") {
							echo '<a href="'.$profile->text.'">'.$profile->text.'</a>';
						} else {
							echo '<a href="http://'.$profile->text.'">'.$profile->text.'</a>';
						}
						break;
	
					default:
						echo $profile->text;
						
				}
			}
		?>
	</li>
	
<?php endforeach; ?>
</ul>
<?php endif;