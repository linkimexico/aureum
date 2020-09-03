<?php
echo $this->Html->css(array('token-input', 'fineuploader', 'jquery.mp'), null, array('inline' => false));
echo $this->Html->script(array( 'jquery.tokeninput', 'tinymce/tinymce.min',	'jquery.fineuploader', 'jquery.mp.min'), array('inline' => false));
?>

<script>
function removeMember(id)
{
	jQuery.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',        
        callback: function(){
            jQuery.post('<?php echo $this->request->base?>/groups/ajax_remove_member', {id: id}, function() {
                jQuery('#member_'+id).fadeOut();
                
                if ( jQuery("#group_user_count").html() != '0' )
                    jQuery("#group_user_count").html( parseInt(jQuery("#group_user_count").html()) - 1 );
            });
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: "<?php echo addslashes(__('Are you sure you want to remove this member?'))?>",
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
	return false;
}

function changeAdmin(id, type)
{
	var msg = "<?php echo addslashes(__('Are you sure you want to make this member a group admin?'))?>";
	if ( type == 'remove' )
	   msg = "<?php echo addslashes(__('Are you sure you want to demote this group admin?'))?>";
	
	jQuery.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',        
        callback: function(){
            jQuery.post('<?php echo $this->request->base?>/groups/ajax_change_admin', {id: id, type: type}, function() {
                jQuery('#teams').trigger('click');
            });
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: msg,
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
	return false;
}

function inviteMore()
{
	jQuery('#simple-modal-body').html('');
    jQuery('#simple-modal-body').spin('small');  
    jQuery('#simple-modal-body').load('<?php echo $this->request->base?>/groups/ajax_invite/<?php echo $group['Group']['id']?>', function(){
        jQuery('#simple-modal-body').spin(false);
    });
}

function loadPage( link_id, url )
{
	jQuery('#' + link_id).children('.badge_counter').hide();
	jQuery('#' + link_id).spin('tiny');
	
	jQuery('#profile-content').load( url, {group_id: <?php echo $group['Group']['id']?>}, function(){
		jQuery('#' + link_id).children('.badge_counter').fadeIn();
		jQuery('#' + link_id).spin(false);
		
		// reattach events
		jQuery('textarea').elastic();
		jQuery(".tip").tipsy({ html: true, gravity: 's' });
		registerImageOverlay();
		jQuery('.tipsy').remove();
		//registerOverlay(); 
	});
}

function ajaxCreateItem( type )
{
	if ( type == 'topics' )
		jQuery('#editor').val(tinyMCE.activeEditor.getContent());
		
	disableButton('ajaxCreateButton');
	
	jQuery.post("<?php echo $this->request->base?>/" + type + "/ajax_save", jQuery("#createForm").serialize(), function(data){
		enableButton('ajaxCreateButton');
		var json = jQuery.parseJSON(data);
            
        if ( json.result == 1 )
        {
            loadPage(type, '<?php echo $this->request->base?>/' + type + '/ajax_view/' + json.id);
            jQuery("#" + type + "_count").html( parseInt(jQuery("#" + type + "_count").html()) + 1 );
        }
        else
        {
            jQuery(".error-message").show();
            jQuery(".error-message").html(json.message);
        }       
	});
} 

jQuery(document).ready(function(){	
	<?php if ( !empty( $this->request->named['topic_id'] ) ): ?>
	loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_view/<?php echo $this->request->named['topic_id']?>');
	<?php endif; ?>
	
	<?php if ( !empty( $this->request->named['video_id'] ) ): ?>
	loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_view/<?php echo $this->request->named['video_id']?>');
	<?php endif; ?>
    
    jQuery(".sharethis").hideshare({media: '<?php echo FULL_BASE_URL . $this->Jsnsocial->getItemPicture($group['Group'], 'groups')?>', linkedin: false});
});

<?php if ( !empty($my_status) && ( $my_status['GroupUser']['status'] == GROUP_USER_MEMBER || $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) ): ?>
jQuery(document).ajaxStop(function(){uploaderWall()});
jQuery(document).ready(function(){uploaderWall()});
function uploaderWall(){
	if(jQuery('#select-2').html()==''){
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
	}
}
<?php endif; ?>

</script>

<div id="leftnav">
	<img src="<?php echo $this->Jsnsocial->getItemPicture($group['Group'], 'groups')?>" class="page-avatar">
	<div class="menu">
    	<ul class="list2" id="browse" style="margin-bottom: 10px">
    		<li class="current">
    			<a data-url="<?php echo $this->request->base?>/groups/ajax_details/<?php echo $group['Group']['id']?>" rel="profile-content" href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>"><i class="icon-file-text-alt"></i> <?php echo __('Details')?></a>
    		</li>		
    		<li><a data-url="<?php echo $this->request->base?>/groups/ajax_members/<?php echo $group['Group']['id']?>" rel="profile-content" id="teams" href="#"><i class="icon-group"></i> 
    			<?php echo __('Members')?> <span id="group_user_count" class="badge_counter"><?php echo $group['Group']['group_user_count']?></span></a>
    		</li>
    		<li><a data-url="<?php echo $this->request->base?>/photos/ajax_browse/group/<?php echo $group['Group']['id']?>" rel="profile-content" id="photos" href="#"><i class="icon-picture"></i> 
    			<?php echo __('Photos')?> <span id="group_user_count" class="badge_counter"><?php echo $group['Group']['photo_count']?></span></a>
    		</li>
    		<li><a data-url="<?php echo $this->request->base?>/videos/ajax_browse/group/<?php echo $group['Group']['id']?>" rel="profile-content" id="videos" href="#"><i class="icon-facetime-video"></i> 
    			<?php echo __('Videos')?> <span id="videos_count" class="badge_counter"><?php echo $group['Group']['video_count']?></span></a>
    		</li>
    		<li><a data-url="<?php echo $this->request->base?>/topics/ajax_browse/group/<?php echo $group['Group']['id']?>" rel="profile-content" id="topics" href="#"><i class="icon-comments"></i> 
    			<?php echo __('Topics')?> <span id="topics_count" class="badge_counter"><?php echo $group['Group']['topic_count']?></span></a>
    		</li>
    	</ul>
    </div>
	
	<?php if ($group['Group']['type'] != PRIVACY_PRIVATE || (!empty($my_status) && ($my_status['GroupUser']['status'] == GROUP_USER_MEMBER || $my_status['GroupUser']['status'] == GROUP_USER_ADMIN))): ?>
	
	<div class="box2 box_style1">
		<h3><?php echo __('Admins')?> (<?php echo $admin_count?>)</h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/users_block', array( 'users' => $group_admins ) ); ?>
		</div>
	</div>
	
	<?php echo $this->element('hooks', array('position' => 'group_detail_sidebar') ); ?> 
	
	<?php if (!empty($members)): ?>
	<div class="box2">
		<h3><?php echo __('Members')?> (<?php echo $member_count?>)</h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/users_block', array( 'users' => $members ) ); ?>
		</div>
	</div>
	<?php endif; ?>
		
	<?php endif; ?>
	
	<div class="box4">
		<ul class="list6 list6sm">
			<?php if ( ( !empty($my_status) && $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) || $group['Group']['type'] != PRIVACY_PRIVATE ): ?>
			<li>
				<a href="<?php echo $this->request->base?>/groups/ajax_invite/<?php echo $group['Group']['id']?>" class="overlay" title="<?php echo __('Invite Friends to Join')?>"><?php echo __('Invite Friends')?></a>
			</li>
			<?php endif; ?>	
			<?php if ( ( !empty($my_status) && $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) || !empty($cuser['Role']['is_admin'] ) ): ?>
			<li>
				<a href="<?php echo $this->request->base?>/groups/create/<?php echo $group['Group']['id']?>" class="no-ajax"><?php echo __('Edit Group')?></a>
			</li>
			<?php endif; ?>
			<?php if ( !empty($my_status) && ( $my_status['GroupUser']['status'] == GROUP_USER_MEMBER || $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) && ( $uid != $group['Group']['user_id'] ) ): ?>
			<li><a href="javascript:void(0)" onclick="jsnsocialConfirm('<?php echo addslashes(__('Are you sure you want to leave this group?'))?>', '<?php echo $this->request->base?>/groups/do_leave/<?php echo $group['Group']['id']?>')"><?php echo __('Leave Group')?></a></li>
			<?php endif; ?>
			<li><a href="<?php echo $this->request->base?>/reports/ajax_create/group/<?php echo $group['Group']['id']?>" class="overlay" title="<?php echo __('Report Group')?>"><?php echo __('Report Group')?></a></li>
			<?php if ( ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
                <?php if ( !$group['Group']['featured'] ): ?>
                <li><a href="<?php echo $this->request->base?>/groups/do_feature/<?php echo $group['Group']['id']?>"><?php echo __('Feature Group')?></a></li>
                <?php else: ?>
                <li><a href="<?php echo $this->request->base?>/groups/do_unfeature/<?php echo $group['Group']['id']?>"><?php echo __('Unfeature Group')?></a></li>
                <?php endif; ?>
            <?php endif; ?>
            <li><a href="#" class="sharethis"><?php echo __('Share This')?></a></li>
		</ul>	
	</div>	
</div>

<div id="center">	
	<?php if (!empty($uid) && (($group['Group']['type'] != PRIVACY_PRIVATE && empty($my_status['GroupUser']['status'])) || ($group['Group']['type'] == PRIVACY_PRIVATE && empty($my_status['GroupUser']['status'])))): ?>
	<a href="<?php echo $this->request->base?>/groups/do_request/<?php echo $group['Group']['id']?>" class="button button-action topButton"><?php echo __('Join')?></a>
	<?php endif; ?>	
	<h1><?php echo h($group['Group']['name'])?></h1>	
	<div id="profile-content">
		<?php 
		if ( !empty( $this->request->named['topic_id'] ) || !empty( $this->request->named['video_id'] ) )
			echo __('Loading...');
		else
			echo $this->element('ajax/group_detail');		
		?>
	</div>
</div>