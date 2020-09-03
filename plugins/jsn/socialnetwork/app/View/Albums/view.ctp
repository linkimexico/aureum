<?php

echo $this->Html->script(array('jquery.fineuploader'));
echo $this->Html->css(array( 'fineuploader' ));
?> 

<script>
jQuery(document).ready(function(){
    jQuery(".sharethis").hideshare({media: '<?php echo FULL_BASE_URL . $this->Jsnsocial->getAlbumCover($album['Album']['cover'])?>', linkedin: false});
});
</script>

<div class="page-padding">
	<?php if ( $uid == $album['User']['id'] || ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
	    <?php if ( empty( $album['Album']['type'] ) ): ?> 
	    <span class="button-dropdown topButton" data-buttons="dropdown">
		    <a href="#" class="button"><?php echo __('Actions')?> <i class="icon-caret-down"></i></a>
		    <ul>
		    	<li><a href="<?php echo $this->request->base?>/albums/ajax_create/<?php echo $album['Album']['id']?>" class="overlay" title="<?php echo __('Edit Album')?>"><?php echo __('Edit Album')?></a></li>
		    	<li><a href="<?php echo $this->request->base?>/albums/edit/<?php echo $album['Album']['id']?>"><?php echo __('Edit Photos')?></a></li>
		    </ul>   
		</span>		
	    <a href="<?php echo $this->request->base?>/photos/ajax_upload/album/<?php echo $album['Album']['id']?>" title="<?php echo h($album['Album']['title'])?>" class="overlay button button-action topButton"><?php echo __('Upload Photos')?></a>
		<?php endif; ?>
	<?php endif; ?>
    		
    <h1><?php echo h($album['Album']['title'])?></h1>
    
    <div style="margin:-5px 0 15px 0">
    	<?php echo __('Posted by %s', $this->Jsnsocial->getName($album['User']))?> <?php echo __('in')?> <a href="<?php echo $this->request->base?>/photos/index/<?php echo $album['Album']['category_id']?>/<?php echo seoUrl($album['Category']['name'])?>"><?php echo $album['Category']['name']?></a> <?php echo $this->Jsnsocial->getTime( $album['Album']['created'], $jsnsocial_setting['date_format'], $utz )?>
    </div>
    
    <?php echo $this->element( 'lists/photos_list', array( 'type' => APP_ALBUM ) ); ?>
    
    <div class="comment_message"><?php echo $this->Jsnsocial->formatText( $album['Album']['description'] )?></div> 
    
    <?php if (!empty($tags)): ?>
    <div style="margin-top:5px"><b><?php echo __('Tags')?></b>:
        <?php echo $this->element( 'blocks/tags_item_block' ); ?>
    </div>
    <?php endif; ?>
    
    <div class="album_right">
        <?php echo $this->element( 'likes', array('item' => $album['Album'], 'type' => APP_ALBUM ) ); ?>
    	<ul class="list6 list6sm">
    		<li><a href="<?php echo $this->request->base?>/reports/ajax_create/album/<?php echo $album['Album']['id']?>" class="overlay" title="<?php echo __('Report Album')?>"><?php echo __('Report Album')?></a></li>
    		<li><a href="#" class="sharethis"><?php echo __('Share This')?></a></li>
    	</ul>
    </div>
    <div class="album_left">
        <h2><?php echo __('Comments')?> (<span id="comment_count"><?php echo $comment_count?></span>)</h2>
        <ul class="list6 comment_wrapper" id="comments">
    	<?php echo $this->element('comments');?>
    	</ul>
    	
    	<?php echo $this->element( 'comment_form', array( 'target_id' => $album['Album']['id'], 'type' => APP_ALBUM, 'class' => 'commentForm2' ) ); ?>
    </div>
</div>