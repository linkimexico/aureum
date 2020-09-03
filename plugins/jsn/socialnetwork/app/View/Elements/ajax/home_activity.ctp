<?php if ( !empty( $uid ) ): ?>
<script>
jQuery(document).ready(function(){
	jQuery("#feed-type a").click(function(){
		jQuery('#whats_new').spin('tiny');
		jQuery("#feed-type a").removeClass('current');
		jQuery(this).addClass('current');
		jQuery("#list-content").load(jQuery(this).attr('href'), {noCache: 1}, function(){
			jQuery('#whats_new').spin(false);
		});
		return false;
	});
	
	jQuery('#select-2').fineUploader({
        request: {
            endpoint: "<?php echo $this->request->base?>/upload/wall"
        },
        text: {
            uploadButton: '<?php echo __('Upload a Photo')?>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            sizeLimit: 10 * 1024 * 1024
        },
        multiple: false
    }).on('complete', function(event, id, fileName, response) {
        jQuery('.qq-upload-list li').remove();
        jQuery('#wall_photo_preview').html('<img src="<?php echo $this->request->webroot?>' + response.photo + '">');
        jQuery('#wall_photo_id').val(response.photo_id);
    });
});
</script>
<?php endif; ?>

<?php if ( !empty( $uid ) && $jsnsocial_setting['member_message'] ): ?>
<div class="box1"><?php echo nl2br($jsnsocial_setting['member_message'])?></div>
<?php endif; ?>

<?php if ( !empty( $uid ) && $jsnsocial_setting['feed_selection'] ): ?>	
<ul class="list7" id="feed-type" style="float:right;">
	<li><a href="<?php echo $this->request->base?>/activities/ajax_browse/everyone" <?php if ( $activity_feed == 'everyone' ) echo 'class="current"'; ?>><i class="icon icon-globe"></i> <?php echo __('Everyone')?></a></li>
	<li><a href="<?php echo $this->request->base?>/activities/ajax_browse/friends" <?php if ( $activity_feed == 'friends' ) echo 'class="current"'; ?>><i class="icon icon-group"></i> <?php echo __('Friends & Me')?></a></li>
</ul>
<?php endif; ?>

<h1><?php echo __("What's New")?></h1>					
<?php if ($uid): ?>
<div id="status_box" class="statusHome">
    <form id="wallForm">
    <?php
    echo $this->Form->hidden('type', array('value' => APP_USER));
    echo $this->Form->hidden('action', array('value' => 'wall_post'));
    echo $this->Form->hidden('wall_photo_id');
    echo $this->Form->textarea('message', array( 'placeholder' => __("Share what's new"), 'onfocus' => 'showCommentButton(0)'));
    ?>
    
    <div style="display:none;margin-top: 10px;" id="commentButton_0">
        <div style="float:right">
        	<?php echo $this->Form->select('privacy', array( PRIVACY_EVERYONE => __('Everyone'), PRIVACY_FRIENDS => __('Friends Only') ), array('empty' => false)); ?>
        	<a href="javascript:void(0)" onclick="postWall()" class="button button-action" style="margin-bottom:3px" id="status_btn"><i class="icon-share"></i> <?php echo __('Share')?></a>
    	</div>
    	<div id="select-2"></div>
    	<div id="wall_photo_preview"></div>
    </div>    
    
    </form>
</div>
<?php endif; ?>

<ul class="list6 comment_wrapper" id="list-content">
	<?php echo $this->element( 'activities', array('more_url' => '/activities/ajax_browse/everyone/page:2') ); ?>
</ul>