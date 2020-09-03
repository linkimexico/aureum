<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div class="latestnews<?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $item) {

		$attrbs 		= json_decode($item->attribs);
		$images 		= json_decode($item->images);
		$intro_image 	= '';

		if(isset($attrbs->helix_ultimate_image) && $attrbs->helix_ultimate_image != '') {
			
			$intro_image = $attrbs->helix_ultimate_image;
			$basename = basename($intro_image);
			$list_image = JPATH_ROOT . '/' . dirname($intro_image) . '/' . JFile::stripExt($basename) . '_thumbnail.' . JFile::getExt($basename);
			if(file_exists($list_image)) {
				$thumb_image = JURI::root(true) . '/' . dirname($intro_image) . '/' . JFile::stripExt($basename) . '_thumbnail.' . JFile::getExt($basename);
			}

		} elseif(isset($images->image_intro) && !empty($images->image_intro)) {
			$thumb_image = $images->image_intro;
		}

		?>
		<div itemscope itemtype="http://schema.org/Article">
			<?php if (!empty($thumb_image)) {
				$itemAttribs = json_decode($item->attribs);
				$formateType = $itemAttribs->{'helix_ultimate_article_format'};
				$isVideo = '';
				if($formateType === 'video'){
					$isVideo = 'video-format';
				}
			?>
			<div class="img-responsive article-list-img <?php echo $isVideo;?>">
				<a href="<?php echo $item->link; ?>" class="indigo-news-title" itemprop="url">
					<img src="<?php echo $thumb_image; ?>">
				</a>
			</div>
			<?php } ?>
			<div class="latest-post-content-wrap">
				<div class="category-tag">
					<a href="<?php echo JURI::base() . "index.php/" . $item->category_title ?>"><?php echo $item->category_title; ?></a>
				</div>
				<div class="latest-post-title">
					<a href="<?php echo $item->link; ?>" class="indigo-news-title" itemprop="url">
						<span itemprop="name">
							<?php echo $item->title; ?>
						</span>
					</a>
				</div>
				<div class="latest-post-info">
					<span class="category-date"><?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC3')); ?></span>
				</div>
			</div>
		</div>
		
		<?php } ?>
</div>
