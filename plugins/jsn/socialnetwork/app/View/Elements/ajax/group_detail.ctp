<?php if ( !empty( $request_count ) ): ?>
<div class="message" style="margin-bottom: 10px;"><?php echo $request_count?> <?php echo __n( 'join request', 'join requests', $request_count )?>. <a href="<?php echo $this->request->base?>/groups/ajax_requests/<?php echo $group['Group']['id']?>" class="overlay" title="<?php echo __('Join Requests')?>"><?php echo __('Click here')?></a> <?php echo __('to respond')?></div>
<?php endif; ?> 

<h2 style="margin-top: 0px"><?php echo __('Information')?></h2>
<ul class="list6 info">
	<li><label><?php echo __('Category')?>:</label> <a href="<?php echo $this->request->base?>/groups/index/<?php echo $group['Group']['category_id']?>/<?php echo seoUrl($group['Category']['name'])?>"><?php echo $group['Category']['name']?></a></li>
	<li><label><?php echo __('Type')?>:</label>
		<?php 
		switch ( $group['Group']['type'] )
		{
			case PRIVACY_PUBLIC:
				echo __('Public (anyone can view and join)');
				break;
				
			case PRIVACY_PRIVATE:
				echo __('Private (only group members can view details)');
				break;
				
			case PRIVACY_RESTRICTED:
				echo __('Restricted (anyone can join upon approval)');
				break;
		}
		?>
	</li>
	<?php 
	if ( $group['Group']['type'] != PRIVACY_PRIVATE || 
		 ( !empty($my_status) && ( $my_status['GroupUser']['status'] == GROUP_USER_MEMBER || $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) )
	   ):
	?>
	<li><label><?php echo __('Description')?>:</label><div><?php echo $this->Jsnsocial->formatText( $group['Group']['description'] )?></div></li>
	<?php endif; ?>
</ul>

<?php 
if ( $group['Group']['type'] != PRIVACY_PRIVATE || 
	 ( !empty($my_status) && ($my_status['GroupUser']['status'] == GROUP_USER_MEMBER || $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) )
   ):
?>

<?php if (!empty($photos)): ?>
<h2><?php echo __('Photos')?></h2>
<ul class="list4 p_photos2">
<?php foreach ($photos as $photo): ?>	
	<li style="background-image:url(<?php echo $this->request->webroot?><?php echo $photo['Photo']['thumb']?>);">
       <a href="<?php echo $this->request->base?>/photos/view/<?php echo $photo['Photo']['id']?>#content"></a>     
    </li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<h2><?php echo __('Recent Activities')?></h2>		
	
<?php
if ( !empty($my_status) && ( $my_status['GroupUser']['status'] == GROUP_USER_MEMBER || $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) ):
?>
<div id="status_box">
	<form id="wallForm">
	<?php
	echo $this->Form->hidden('target_id', array('value' => $group['Group']['id']));
	echo $this->Form->hidden('type', array('value' => APP_GROUP));
	echo $this->Form->hidden('action', array('value' => 'wall_post'));
    echo $this->Form->hidden('wall_photo_id');
	echo $this->Form->textarea('message', array( 'placeholder' => __('Write something...'), 'onfocus' => 'showCommentButton(0)'));
	?>
	<div style="display:none;margin-top: 10px;" id="commentButton_0">
	    <div style="float:right">
	       <a href="javascript:void(0)" onclick="postWall()" class="button button-action" id="status_btn"><i class="icon-share"></i> <?php echo __('Share')?></a>
	    </div>
	    <div id="select-2"></div>
        <div id="wall_photo_preview"></div>
	</div>
	</form>
</div>
<?php endif; ?>

<ul class="list6 comment_wrapper" id="list-content">
	<?php echo $this->element('activities', array( 'more_url' => '/activities/ajax_browse/group/' . $group['Group']['id'] . '/page:2' ) ); ?>
</ul>
<?php endif; ?>