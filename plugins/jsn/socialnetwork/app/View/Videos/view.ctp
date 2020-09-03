<script>
jQuery(document).ready(function(){
    jQuery(".sharethis").hideshare({media: '<?php echo FULL_BASE_URL . $this->request->webroot . 'uploads/videos/' . $video['Video']['thumb']?>', linkedin: false});
});
</script>

<div id="leftnav">
    <?php echo $this->element('hooks', array('position' => 'video_detail_sidebar') ); ?> 
    
	<div class="box2 box_style1">
		<h3><?php echo __('Tags')?></h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/tags_item_block' ); ?>
		</div>
	</div>
	
	<?php if ( !empty( $similar_videos ) ): ?>
	<div class="box2 box_style2">
		<h3><?php echo __('Similar Videos')?></h3>
		<div class="box_content">
		    <?php echo $this->element('blocks/videos_block', array('videos' => $similar_videos)); ?>
		</div>
	</div>
	<?php endif; ?>

	<?php echo $this->element('likes', array('item' => $video['Video'], 'type' => APP_VIDEO)); ?>
	
	<div class="box4">
		<ul class="list6 list6sm">
			<?php if ($video['User']['id'] == $uid || ( !empty($cuser) && $cuser['Role']['is_admin'] ) || ( !empty($admins) && in_array($uid, $admins) )): ?>
			<li><a href="<?php echo $this->request->base?>/videos/ajax_create/<?php echo $video['Video']['id']?>" class="overlay" title="<?php echo __('Edit Video Details')?>"><?php echo __('Edit Video')?></a></li>
			<?php endif; ?>
			<li><a href="<?php echo $this->request->base?>/reports/ajax_create/video/<?php echo $video['Video']['id']?>" class="overlay" title="<?php echo __('Report Video')?>"><?php echo __('Report Video')?></a></li>
			<li><a href="#" class="sharethis"><?php echo __('Share This')?></a></li>
		</ul>
	</div>	
	
</div>

<div id="center">
    <div class="post_body">	
    	<h1><?php echo h($video['Video']['title'])?></h1>	
    		
    	<?php
    	$w = ( $this->request->is('mobile') ) ? 300 : 590;
        $h = ( $this->request->is('mobile') ) ? 225 : 465;
    	
    	switch ( $video['Video']['source'] )
    	{
    		case 'youtube':
    			echo '<iframe width="' . $w . '" height="' . $h . '" src="https://www.youtube.com/embed/' . $video['Video']['source_id'] . '?wmode=opaque" frameborder="0" allowfullscreen></iframe>';
    			break;
    			
    		case 'vimeo':
    			echo '<iframe src="https://player.vimeo.com/video/' . $video['Video']['source_id'] . '" width="' . $w . '" height="' . $h . '" frameborder="0"></iframe>';
    			break;
    	}
    	?>
    	
    	<div class="truncate" style="margin:10px 0" data-more-text="<?php echo __('Show More')?>" data-less-text="<?php echo __('Show Less')?>">
    		<?php echo $this->Jsnsocial->formatText( $video['Video']['description'], false, true )?>
    	</div>
    	<?php echo __('Posted by %s', $this->Jsnsocial->getName($video['User']))?> <?php echo __('in')?> <a href="<?php echo $this->request->base?>/videos/index/<?php echo $video['Video']['category_id']?>/<?php echo seoUrl($video['Category']['name'])?>"><?php echo $video['Category']['name']?></a> <span class="date"><?php echo $this->Jsnsocial->getTime($video['Video']['created'], $jsnsocial_setting['date_format'], $utz)?></span>
    	<br /><br />
    </div>

	<h2><?php echo __('Comments')?> (<span id="comment_count"><?php echo $comment_count?></span>)</h2>
	<ul class="list6 comment_wrapper" id="comments">
	<?php echo $this->element('comments');?>
	</ul>
	
	<?php echo $this->element( 'comment_form', array( 'target_id' => $video['Video']['id'], 'type' => APP_VIDEO ) ); ?>
</div>