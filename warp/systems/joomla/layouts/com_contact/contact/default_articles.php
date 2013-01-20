<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

?>

<?php if ($this->params->get('show_articles')) : ?>
<ul class="blank">
<?php foreach ($this->item->articles as $article) :	?>
	<li>
		<?php echo JHtml::_('link', JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug)), htmlspecialchars($article->title, ENT_COMPAT, 'UTF-8')); ?>
	</li>
<?php endforeach; ?>
</ul>
<?php endif;