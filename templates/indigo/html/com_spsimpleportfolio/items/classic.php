<?php
/**
* @package     SP Simple Portfolio
*
* @copyright   Copyright (C) 2010 - 2018 JoomShaper. All rights reserved.
* @license     GNU General Public License version 2 or later.
*/

defined('_JEXEC') or die;
jimport( 'joomla.filesystem.file' );
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

//Load the method jquery script.
JHtml::_('jquery.framework');
$doc = JFactory::getDocument();
$doc->addStylesheet( JURI::root(true) . '/components/com_spsimpleportfolio/assets/css/featherlight.min.css' );
$doc->addStylesheet( JURI::root(true) . '/components/com_spsimpleportfolio/assets/css/spsimpleportfolio.css' );
$doc->addScript( JURI::root(true) . '/components/com_spsimpleportfolio/assets/js/jquery.shuffle.modernizr.min.js' );
$doc->addScript( JURI::root(true) . '/components/com_spsimpleportfolio/assets/js/featherlight.min.js' );
$doc->addScript( JURI::root(true) . '/components/com_spsimpleportfolio/assets/js/spsimpleportfolio.js' );

if( $this->params->get('show_page_heading') && $this->params->get( 'page_heading' ) ) {
	echo "<h1 class='page-header'>" . $this->params->get( 'page_heading' ) . "</h1>";
}
?>

<div id="sp-simpleportfolio" class="sp-simpleportfolio sp-simpleportfolio-view-items layout-<?php echo $this->layout_type; ?>">
	<?php if($this->params->get('show_filter', 1)) { ?>
	<div class="sp-simpleportfolio-filter">
		<ul>
			<li class="active" data-group="all"><a href="#"><?php echo JText::_('COM_SPSIMPLEPORTFOLIO_SHOW_ALL'); ?></a></li>
			<?php foreach ($this->tagList as $filter) { ?>
			<li data-group="<?php echo $filter->alias; ?>"><a href="#"><?php echo $filter->title; ?></a></li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>

	<?php
	//Videos
	foreach ($this->items as $key => $this->item) {
		if($this->item->video) {
			$video = parse_url($this->item->video);

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

			echo '<iframe class="sp-simpleportfolio-lightbox" src="'. $video_src .'" width="500" height="281" id="sp-simpleportfolio-video'.$this->item->id.'" style="border:none;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
		}
	}
	?>

	<div class="sp-simpleportfolio-items classic-layout row sp-simpleportfolio-columns-<?php echo $this->params->get('columns', 3); ?>">
		<?php $i = 0;
		foreach ($this->items as $this->item) {
			if($i == 5){
				$i = 1;
			}
			if($i == 1 || $i == 2) {
				$column_class = 'col-sm-8';
			} else {
				$column_class = 'col-sm-4';
			}
			?>
		<div class="sp-simpleportfolio-item <?php echo $column_class; ?>" data-groups='[<?php echo $this->item->groups; ?>]'>
			<div class="sp-simpleportfolio-overlay-wrapper clearfix" style="background-image: url('<?php echo $this->item->thumb;?>');">

				<?php if($this->item->video) { ?>
				<span class="sp-simpleportfolio-icon-video"></span>
				<?php } ?>

				<!-- <img class="sp-simpleportfolio-img" src="<?php //echo $this->item->thumb; ?>" alt="<?php //echo $this->item->title; ?>"> -->

				<div class="sp-simpleportfolio-overlay">
					<div class="portfolio-tag-date-wrapper">
						<div class="sp-simpleportfolio-tags">
							<span>
								<?php echo implode(', ', $this->item->tags); ?>
							</span>
						</div>
						<div class="portfolio-date">
							<span><?php echo JHtml::_('date', $this->item->created, 'd M');?></span>
							<span class="portfolio-date-year"><?php echo JHtml::_('date', $this->item->created, 'Y');?></span>
						</div>
					</div>
					<?php if($this->layout_type!='default') { ?>
					<h3 class="sp-simpleportfolio-title">
						<a href="<?php echo $this->item->url; ?>">
							<?php echo $this->item->title; ?>
						</a>
					</h3>
					<div class="sp-simpleportfolio-tags">
						<?php echo implode(', ', $this->item->tags); ?>
					</div>
					<?php } ?>
				</div>
			</div>

			<?php if($this->layout_type=='default') { ?>
			<div class="sp-simpleportfolio-info-wrapper">
				<?php $cat = categoryTitle($this->item->catid); 
					if (!empty($cat)) {?>
				<p><?php echo $cat->title; ?></p>
				<?php }?>
				<h3 class="sp-simpleportfolio-title">
					<a href="<?php echo $this->item->url; ?>">
						<?php echo $this->item->title; ?>
					</a>
				</h3>
			</div>
			<?php } ?>
		</div>
		<?php $i++;} ?>
	</div>

	<?php if ($this->pagination->get('pages.total') >1) { ?>
	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>
</div>
