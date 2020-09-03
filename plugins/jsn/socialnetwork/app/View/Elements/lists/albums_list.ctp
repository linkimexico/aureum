<?php
if (count($albums) > 0)
{
	foreach ($albums as $album):
?>
	<li>
		<div class="album_container">
			<div class="album">
				<div class="album_info">
					<a class="album_title" href="<?php echo $this->request->base?>/albums/view/<?php echo $album['Album']['id']?>/<?php echo seoUrl($album['Album']['title'])?>">
						<h3><?php echo h($album['Album']['title'])?></h3>
					</a>
					<div class="date"><i class="icon icon-user"></i> <?php echo __('Posted by')?> <?php echo $this->Jsnsocial->getName($album['User'], false)?> &nbsp;&nbsp; <i class="icon icon-calendar"></i> <?php echo $this->Jsnsocial->getTime( $album['Album']['created'], $jsnsocial_setting['date_format'], $utz )?></div>
					<div class="album_desc"><?php echo $this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $album['Album']['description'])), 250);?></div>
					<!-- <a class="button button-highlight" href="<?php echo $this->request->base?>/albums/view/<?php echo $album['Album']['id']?>/<?php echo seoUrl($album['Album']['title'])?>">
						<?php echo __('View Album')?> <i class="icon icon-arrow-right"></i>
					</a> -->
				</div>
			
				<div class="album_totals">
					<div class="album_total"><div class="album_totaltitle"><i class="icon icon-picture"></i> <?php echo __('Photos')?></div><div class="album_totalvalue"><?php echo ($album['Album']['photo_count'])?></div></div>
					<div class="album_total"><div class="album_totaltitle"><i class="icon icon-thumbs-down"></i> <?php echo __('Dislikes')?></div><div class="album_totalvalue"><?php echo ($album['Album']['dislike_count'])?></div></div>
					<div class="album_total"><div class="album_totaltitle"><i class="icon icon-thumbs-up"></i> <?php echo __('Likes')?></div><div class="album_totalvalue"><?php echo ($album['Album']['like_count'])?></div></div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="album_fullcover" style="background-image:url(<?php echo str_replace('/xxxt_','/',$this->Jsnsocial->getAlbumCover($album['Album']['cover']))?>)">
				<i></i>
				<i></i>
				<i></i>
			</div>
		</div>
	</li>
<?php 
	endforeach;
}
else
	echo '<div align="center" style="width:100%;overflow:hidden">' . __('No more results found') . '</div>';
?>

<?php if (count($albums) >= RESULTS_LIMIT): ?>
    <div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url?>', 'list-content', this)"><?php echo __('Load More')?></a>
    </div>
<?php endif; ?>