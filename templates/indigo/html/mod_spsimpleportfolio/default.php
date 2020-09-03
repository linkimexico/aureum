<?php
/**
 * @package     SP Simple Portfolio
 * @subpackage  mod_spsimpleportfolio
 *
 * @copyright   Copyright (C) 2010 - 2018 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;
jimport( 'joomla.filesystem.file' );
$layout_type = $params->get('layout_type', 'default');
function categoryTitle ($catid) {
	$db = JFactory::getDbo();
	$query= $db->getQuery(true);
	$query->select('title')->from($db->qn('#__categories'))
	->where(
		array(
			$db->qn('id') . ' = ' . $catid,
			$db->qn('published') . ' = 1'
		)
	);
	$db->setQuery($query);
	$result = $db->loadObject();
	return !empty($result) ? $result : null;
}
?>
<div id="mod-sp-simpleportfolio" class="sp-simpleportfolio sp-simpleportfolio-view-items layout-<?php echo str_replace('_', '-', $layout_type); ?> <?php echo $moduleclass_sfx; ?>">
	<?php if($params->get('show_filter', 1)) { ?>
		<div class="sp-simpleportfolio-filter">
			<ul>
				<li class="active" data-group="all"><a href="#"><?php echo JText::_('MOD_SPSIMPLEPORTFOLIO_SHOW_ALL'); ?></a></li>
				<?php foreach ($tagList as $filter) { ?>
						<li data-group="<?php echo $filter->alias; ?>"><a href="#"><?php echo $filter->title; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>

	<?php
		//Videos
		foreach ($items as $item) {
			if($item->video) {
				$video = parse_url($item->video);

				switch($video['host']) {
					case 'youtu.be':
					$video_id 	= trim($video['path'],'/');
					$video_src 	= '//www.youtube.com/embed/' . $video_id;
					break;

					case 'www.youtube.com':
					case 'youtube.com':
					parse_str($video['query'], $query);
					$video_id 	= $query['v'];
					$video_src 	= '//www.youtube.com/embed/' . $video_id;
					break;

					case 'vimeo.com':
					case 'www.vimeo.com':
					$video_id 	= trim($video['path'],'/');
					$video_src 	= "//player.vimeo.com/video/" . $video_id;
				}
				echo '<iframe class="sp-simpleportfolio-lightbox" src="'. $video_src .'" width="500" height="281" id="sp-simpleportfolio-video'.$item->spsimpleportfolio_item_id.'" style="border:none;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
			}
		}
	?>

	<div class="sp-simpleportfolio-items sp-simpleportfolio-columns-<?php echo $params->get('columns', 3); ?>">
		<?php foreach ($items as $item) { ?>
			<div class="sp-simpleportfolio-item" data-groups='[<?php echo $item->groups; ?>]'>
				<div class="sp-simpleportfolio-overlay-wrapper clearfix">
					<a href="<?php echo $item->url; ?>" class="full-link"></a>
					<?php if($item->video) { ?>
						<span class="sp-simpleportfolio-icon-video"></span>
					<?php } ?>

					<img class="sp-simpleportfolio-img" src="<?php echo $item->thumb; ?>" alt="<?php echo $item->title; ?>">
					
					<div class="sp-simpleportfolio-overlay">
						<div class="portfolio-tag-date-wrapper">
							<div class="sp-simpleportfolio-tags">
								<span>
									<?php echo implode(', ', $item->tags); ?>
								</span>
							</div>
							<div class="portfolio-date">
								<span><?php echo JHtml::_('date', $item->created, 'd M');?></span>
								<span class="portfolio-date-year"><?php echo JHtml::_('date', $item->created, 'Y');?></span>
							</div>
						</div>
						<?php if($layout_type!='default') { ?>
						<h3 class="sp-simpleportfolio-title">
							<a href="<?php echo $item->url; ?>">
								<?php echo $item->title; ?>
							</a>
						</h3>
						<div class="sp-simpleportfolio-tags">
							<?php echo implode(', ', $item->tags); ?>
						</div>
						<?php } ?>
					</div>
				</div>

				<?php if($layout_type=='default') { ?>
					<div class="sp-simpleportfolio-info-wrapper">
						<?php $cat = categoryTitle($item->catid); 
							if (!empty($cat)) {?>
						<p><?php echo $cat->title; ?></p>
						<?php }?>
						<h3 class="sp-simpleportfolio-title">
							<a href="<?php echo $item->url; ?>">
								<?php echo $item->title; ?>
							</a>
						</h3>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>

</div>
