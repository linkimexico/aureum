<?php
echo $this->Html->css(array('token-input', 'fineuploader'), null, array('inline' => false));
echo $this->Html->script(array('jquery.tokeninput', 'jquery.fineuploader'), array('inline' => false));
?>

<script>
jQuery(document).ready(function(){
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
    
    jQuery(".sharethis").hideshare({media: '<?php echo FULL_BASE_URL . $this->Jsnsocial->getItemPicture($event['Event'], 'events')?>', linkedin: false});
});

function inviteMore()
{
	jQuery('#simple-modal-body').html('');
	jQuery('#simple-modal-body').spin('small');	
	jQuery('#simple-modal-body').load('<?php echo $this->request->base?>/events/ajax_invite/<?php echo $event['Event']['id']?>', function(){
	    jQuery('#simple-modal-body').spin(false);
	});
}
</script>

<div id="leftnav">
	<img src="<?php echo $this->Jsnsocial->getItemPicture($event['Event'], 'events')?>" class="page-avatar">
	<div class="menu">
    	<ul class="list2" style="margin-bottom: 10px">
    		<?php 
    		// invite only available for public event and owner
    		if ( ( !empty($uid) && $event['Event']['type'] == PRIVACY_PUBLIC ) || ( $uid == $event['User']['id'] ) ): 
    		?>
    		<li>
    			<a href="<?php echo $this->request->base?>/events/ajax_invite/<?php echo $event['Event']['id']?>" class="overlay" title="<?php echo __('Invite Friends to Attend')?>"><i class="icon-envelope"></i> <?php echo __('Invite Friends')?></a>
    		</li>
    		<?php 
    		endif;		
    		if ( $event['Event']['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) ): 
    		?>
    		<li>
    			<a href="<?php echo $this->request->base?>/events/create/<?php echo $event['Event']['id']?>"><i class="icon-pencil"></i> <?php echo __('Edit Event')?></a>
    		</li>
    		<?php endif; ?>		
    	</ul>
    </div>
	
	<div class="box1" style="margin:10px 0">
		<form action="<?php echo $this->request->base?>/events/do_rsvp" method="post">
		<input type="hidden" name="event_id" value="<?php echo $event['Event']['id']?>">
		<b><?php echo __('Are you attending?')?></b>
		<div style="margin:8px 0 10px 0">
			<input type="radio" name="rsvp" value="1" <?php if (!empty($my_rsvp) && $my_rsvp['EventRsvp']['rsvp'] == 1) echo 'checked'; ?>> <?php echo __('Yes')?> 
			&nbsp;<input type="radio" name="rsvp" value="2" <?php if (!empty($my_rsvp) && $my_rsvp['EventRsvp']['rsvp'] == 2) echo 'checked'; ?>> <?php echo __('No')?> 
			&nbsp;<input type="radio" name="rsvp" value="3" <?php if (!empty($my_rsvp) && $my_rsvp['EventRsvp']['rsvp'] == 3) echo 'checked'; ?>> <?php echo __('Maybe')?>
		</div>
		<input type="submit" class="button button-action" value="<?php echo __('Confirm RSVP')?>">
		</form>
	</div>
	
	<?php if (!empty($attending)): ?>
	<div class="box2 box_style1">
		<h3><a href="<?php echo $this->request->base?>/events/ajax_showRsvp/<?php echo $event['Event']['id']?>/<?php echo RSVP_ATTENDING?>" class="overlay" title="<?php echo __('%s Attending', $event['Event']['event_rsvp_count'])?>"><?php echo __('%s Attending', $event['Event']['event_rsvp_count'])?></a></h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/users_block', array( 'users' => $attending ) ); ?>
		</div>		
	</div>
	<?php endif; ?>
	
	<?php echo $this->element('hooks', array('position' => 'event_detail_sidebar') ); ?> 
	
	<?php if (!empty($maybe)): ?>
	<div class="box2">
		<h3><a href="<?php echo $this->request->base?>/events/ajax_showRsvp/<?php echo $event['Event']['id']?>/<?php echo RSVP_MAYBE?>" class="overlay" title="<?php echo __('%s Maybe Attending', $maybe_count)?>"><?php echo __('%s Maybe Attending', $maybe_count)?></a></h3>
		<div class="box_content">
		    <?php echo $this->element('blocks/users_sm_block', array('users' => $maybe)); ?>
		</div>		
	</div>
	<?php endif; ?>

	<?php if (!empty($awaiting)): ?>
	<div class="box2">
		<h3><a href="<?php echo $this->request->base?>/events/ajax_showRsvp/<?php echo $event['Event']['id']?>/<?php echo RSVP_AWAITING?>" class="overlay" title="<?php echo __('%s Awaiting Response', $awaiting_count)?>"><?php echo __('%s Awaiting Response', $awaiting_count)?></a></h3>
		<div class="box_content">
		    <?php echo $this->element('blocks/users_sm_block', array('users' => $awaiting)); ?>
		</div>		
	</div>
	<?php endif; ?>

	<?php if (!empty($not_attending)): ?>
	<div class="box2">
		<h3><a href="<?php echo $this->request->base?>/events/ajax_showRsvp/<?php echo $event['Event']['id']?>/<?php echo RSVP_NOT_ATTENDING?>" class="overlay" title="<?php echo __('%s Not Attending', $not_attending_count)?>"><?php echo __('%s Not Attending', $not_attending_count)?></a></h3>
		<div class="box_content">
		    <?php echo $this->element('blocks/users_sm_block', array('users' => $not_attending)); ?>
		</div>		
	</div>
	<?php endif; ?>
	
	<div class="box4">
		<ul class="list6 list6sm">
			<li><a href="<?php echo $this->request->base?>/reports/ajax_create/event/<?php echo $event['Event']['id']?>" class="overlay" title="<?php echo __('Report Event')?>"><?php echo __('Report Event')?></a></li>
			<li><a href="#" class="sharethis"><?php echo __('Share This')?></a></li>
		</ul>	
	</div>	
</div>

<div id="center">
	<h1><?php echo h($event['Event']['title'])?></h1>	

	<h2><?php echo __('Information')?></h2>
	<ul class="list6 info">
		<li><label><?php echo __('Time')?>:</label>
			<?php echo $this->Time->format($event['Event']['from'],"%A, %e %B %Y")?>, <?php echo $event['Event']['from_time']?> - 
			<?php echo $this->Time->format($event['Event']['to'],"%A, %e %B %Y")?>, <?php echo $event['Event']['to_time']?>
			(<?php if (!empty($event['User']['timezone'])) echo $event['User']['timezone']; else echo $jsnsocial_setting['timezone'];?>)
		</li>
		<li><label><?php echo __('Location')?>:</label><?php echo h($event['Event']['location'])?></li>
		<?php if ( !empty( $event['Event']['address'] ) ): ?>
		<li><label><?php echo __('Address')?>:</label><?php echo h($event['Event']['address'])?> (<a href="javascript:void(0)" class="overlay" rel="google_map" title="<?php echo __('View Map')?>"><?php echo __('View Map')?></a>)</li>
		<?php endif; ?>
		<?php if ( !empty( $event['Event']['category_id'] ) ): ?>
		<li><label><?php echo __('Category')?>:</label><a href="<?php echo $this->request->base?>/events/index/<?php echo $event['Event']['category_id']?>/<?php echo seoUrl($event['Category']['name'])?>"><?php echo $event['Category']['name']?></a></li>
		<?php endif; ?>
		<li><label><?php echo __('Created by')?>:</label><?php echo $this->Jsnsocial->getName($event['User'], false)?></li>
		<li><label><?php echo __('Info')?>:</label><div><?php echo $this->Jsnsocial->formatText( $event['Event']['description'] )?></div></li>
	</ul>
	<h2><?php echo __('Messages')?></h2>
	<?php
	if ( !empty( $my_rsvp ) || ( !empty( $uid ) && $event['Event']['type'] == PRIVACY_PUBLIC ) ):
	?>
	<div id="status_box">			
		<form id="wallForm">
		<?php
		echo $this->Form->hidden('target_id', array('value' => $event['Event']['id']));
		echo $this->Form->hidden('type', array('value' => APP_EVENT));
		echo $this->Form->hidden('action', array('value' => 'wall_post'));
        echo $this->Form->hidden('wall_photo_id');
		echo $this->Form->textarea('message', array('placeholder' => __('Write something...'), 'onfocus' => 'showCommentButton(0)'));
		?>
		<div style="display:none;margin-top: 10px;" id="commentButton_0">
		    <div style="float:right">
		        <a href="javascript:void(0)" onclick="postWall()" class="button button-action" style="margin-bottom:3px" id="status_btn"><i class="icon-share"></i> <?php echo __('Share')?></a>      
            </div>  
            <div id="select-2"></div>
            <div id="wall_photo_preview"></div>	
		</div>	
		</form>			
	</div>
	<?php endif; ?>
	<ul class="list6 comment_wrapper" id="list-content">
		<?php 
		if ( !empty( $activities) )
			echo $this->element( 'activities', array( 'more_url' => '/activities/ajax_browse/event/' . $event['Event']['id'] . '/page:2', 'admins' => array( $event['User']['id'] ) ) ); 
		else
			echo __('No messages found');	
		?>
	</ul>

</div>

<?php if ( !empty( $event['Event']['address'] ) ): ?>
<div id="google_map" style="display:none">
	<iframe width="450" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?q=<?php echo $event['Event']['address']?>&amp;output=embed"></iframe>
</div>
<?php endif; ?>  