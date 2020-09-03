<?php echo $this->element('hooks', array('position' => 'profile_top') ); ?>

<?php if (!empty($albums)): ?>
<h2><?php echo __('Photos')?></h2>
<ul class="list4 p_photos2">
<?php foreach ($albums as $album): ?>
	<li>
	   <a style="background-image:url(<?php echo $this->Jsnsocial->getAlbumCover($album['Album']['cover'])?>);" href="<?php echo $this->request->base?>/albums/view/<?php echo $album['Album']['id']?>"></a>	   
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<h2><?php echo __('Recent Activities')?></h2>

<?php if ( !empty( $uid ) ): ?>
<div id="status_box">
	<form id="wallForm">
	<?php
	if ( $uid != $user['User']['id'] ) 
		echo $this->Form->hidden('target_id', array('value' => $user['User']['id']));
		
	echo $this->Form->hidden('type', array('value' => APP_USER));
	echo $this->Form->hidden('action', array('value' => 'wall_post'));
    echo $this->Form->hidden('wall_photo_id');
	$text = ( $uid == $user['User']['id'] ) ? __("What's on your mind?") : __("Write something...");
	echo $this->Form->textarea('message', array('placeholder' => $text, 'onfocus' => 'showCommentButton(0)'));
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
	<?php echo $this->element('activities', array('more_url' => '/activities/ajax_browse/profile/' . $user['User']['id'] . '/page:2')); ?>
</ul>
