<?php
if (count($videos) > 0)
{
	foreach ($videos as $video):
?>
	<li>
		<div class="album_container">
			<div class="album">
				<div class="album_info">
					<a class="album_title" href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_view/<?php echo $video['Video']['id']?>')"<?php else: ?>"<?php echo $this->request->base?>/videos/view/<?php echo $video['Video']['id']?>/<?php echo seoUrl($video['Video']['title'])?>"<?php endif; ?>>
						<h3><?php echo h($video['Video']['title'])?></h3>
					</a>
					<div class="date"><i class="icon icon-user"></i> <?php echo __('Posted by')?> <?php echo $this->Jsnsocial->getName($video['User'], false)?> &nbsp;&nbsp; <i class="icon icon-calendar"></i> <?php echo $this->Jsnsocial->getTime( $video['Video']['created'], $jsnsocial_setting['date_format'], $utz )?></div>
					<div class="album_desc"><?php echo $this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $video['Video']['description'])), 250);?></div>
					<!-- <a class="button button-highlight" href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_view/<?php echo $video['Video']['id']?>')"<?php else: ?>"<?php echo $this->request->base?>/videos/view/<?php echo $video['Video']['id']?>/<?php echo seoUrl($video['Video']['title'])?>"<?php endif; ?>>
						<?php echo __('Show More')?>  <i class="icon icon-arrow-right"></i>
					</a> -->
				</div>
			
				<div class="album_totals">
					<div class="album_total"><div class="album_totaltitle"><i class="icon icon-thumbs-down"></i> <?php echo __('Dislikes')?></div><div class="album_totalvalue"><?php echo ($video['Video']['dislike_count'])?></div></div>
					<div class="album_total"><div class="album_totaltitle"><i class="icon icon-thumbs-up"></i> <?php echo __('Likes')?></div><div class="album_totalvalue"><?php echo ($video['Video']['like_count'])?></div></div>
					<div class="clear"></div>
				</div>
			</div>
			<div class="album_fullcover" style="background-image:url(<?php echo $this->request->webroot?>uploads/videos/<?php echo $video['Video']['thumb']?>)">
				<i></i>
				<i></i>
				<i></i>
			</div>
		</div>
       <!-- <a href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_view/<?php echo $video['Video']['id']?>')"<?php else: ?>"<?php echo $this->request->base?>/videos/view/<?php echo $video['Video']['id']?>/<?php echo seoUrl($video['Video']['title'])?>"<?php endif; ?> class="album_cover" style="background-image:url(<?php echo $this->request->webroot?>uploads/videos/<?php echo $video['Video']['thumb']?>)"><div class="infoLayer"><p><?php echo h($video['Video']['title'])?></p></div></a>
       <div class="album_info">
           <a href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_view/<?php echo $video['Video']['id']?>')"<?php else: ?>"<?php echo $this->request->base?>/videos/view/<?php echo $video['Video']['id']?>/<?php echo seoUrl($video['Video']['title'])?>"<?php endif; ?>><?php echo h($this->Text->truncate( $video['Video']['title'], 25 ))?></a>
           <div class="date"><?php echo $video['Video']['like_count']?> <i class="icon-thumbs-up-alt"></i></div>
       </div> -->   
    </li>
<?php 
	endforeach;
}
else
	echo '<div align="center" style="width:100%;overflow:hidden">' . __('No more results found') . '</div>';
?>

<?php if (count($videos) >= RESULTS_LIMIT): ?>
    <div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url?>', 'list-content', this)"><?php echo __('Load More')?></a>
    </div>
<?php endif; ?>
