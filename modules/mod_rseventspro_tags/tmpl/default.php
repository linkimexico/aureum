<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$open = !$links ? 'target="_blank"' : ''; ?>

<?php if ($tags) { ?>
<ul class="rsepro-tag-module<?php echo $suffix; ?>">
	<?php foreach ($tags as $tag) { ?>
	<li>
		<a <?php echo $open; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&tag='.rseventsproHelper::sef($tag->id,$tag->name),false,$itemid); ?>"><?php echo $tag->name; ?></a>
	</li>
	<?php } ?>
</ul>
<?php } ?>