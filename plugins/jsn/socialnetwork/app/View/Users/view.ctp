<?php
echo $this->Html->script(array('jquery.fineuploader', 'jquery.Jcrop.min', 'jquery.mp.min'));
echo $this->Html->css(array( 'fineuploader', 'jquery.Jcrop', 'jquery.mp' ));
?> 

<script>
<?php if ( $uid == $user['User']['id'] ): ?>
var jcrop_api;
var x = 0
    y = 0
    w = 0
    h = 0;

jQuery(document).ready(function(){
    jQuery('#avatar_upload a').click(function() {    
        jQuery.fn.SimpleModal({
            width: 600,
            model: 'modal-ajax',
            title: '<?php echo __('Profile Picture')?>',
            hideFooter: false,
            offsetTop: 100,
            param: {
                url: '<?php echo $this->request->base?>/users/ajax_avatar',
                onRequestComplete: function() { },
                onRequestFailure: function() { }
            }
        }).addButton('<?php echo __('Save Thumbnail')?>', 'button button-action', function() {
            jQuery('#avatar_wrapper').spin('large');
            var modal = this;
            
            jQuery.post('<?php echo $this->request->base?>/upload/thumb', {x: x, y: y, w: w, h: h}, function(data) {
                var json = jQuery.parseJSON(data);
                modal.hideModal(); 
                
                if ( data != '' )
                    jQuery('#member-avatar').attr('src', json.thumb);                
            });                       
        }).showModal();
		return false;
    });
    
    jQuery('#cover_upload a').click(function() {    
        jQuery.fn.SimpleModal({
            width: 600,
            model: 'modal-ajax',
            title: '<?php echo __('Cover Picture')?>',
            hideFooter: false,
            offsetTop: 100,
            param: {
                url: '<?php echo $this->request->base?>/users/ajax_cover',
                onRequestComplete: function() { },
                onRequestFailure: function() { }
            }
        }).addButton('<?php echo __('Save Cover Picture')?>', 'button button-action', function() {
            jQuery('#cover_wrapper').spin('large');
            var modal = this;
            
            jQuery.post('<?php echo $this->request->base?>/upload/thumb_cover', {x: x, y: y, w: w, h: h}, function(data) {
                var json = jQuery.parseJSON(data);
                modal.hideModal(); 
                
                if ( data != '' )
                    jQuery('#cover').css('background-image', 'url(' + json.thumb + ')');         
            });  
        }).showModal();
		return false;
    });
});

function storeCoords(c)
{
    x = c.x;
    y = c.y;
    w = c.w;
    h = c.h;
}
<?php else: ?>
function removeFriend(id)
{
    jQuery.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',        
        callback: function(){
            jQuery.post('<?php echo $this->request->base?>/friends/ajax_remove', {id: id}, function() {
	            location.reload();
	        });
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: "<?php echo addslashes(__('Are you sure you want to remove this friend?'))?>",
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
}

<?php endif; ?>

<?php if ( !empty($uid) && $canView ): ?>
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
	<?php if ( $canView ): ?>
	<div id="browse" style="margin-bottom:10px" class="menu">
		<ul class="list2">
			<li class="current">
				<a data-url="<?php echo $this->request->base?>/users/ajax_profile/<?php echo $user['User']['id']?>" rel="profile-content" href="<?php echo $this->Jsnsocial->getProfileUrl( $user['User'] )?>"><i class="icon-user"></i> <?php echo __('Profile')?></a>
			</li>
			<li>
				<a data-url="<?php echo $this->request->base?>/users/ajax_info/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="icon-file-text-alt"></i> <?php echo __('Info')?></a>
			</li>
			<li>
				<a data-url="<?php echo $this->request->base?>/users/ajax_friends/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="icon-group"></i> <?php echo __('Friends')?>
				<span class="badge_counter"><?php echo $user['User']['friend_count']?></span></a>
			</li>	
			<?php if (!empty($plugin['photo'])): ?>
			<li>
				<a data-url="<?php echo $this->request->base?>/users/ajax_photos/<?php echo $user['User']['id']?>" rel="profile-content" id="user_photos" href="#"><i class="<?php echo $plugin['photo']['icon_class']?>"></i> <?php echo __('Photos')?>
				<span class="badge_counter"><?php echo $user['User']['photo_count']?></span></a>
			</li>		
			<?php endif; ?>
			<?php if (!empty($plugin['blog'])): ?>
			<li>
			    <a data-url="<?php echo $this->request->base?>/users/ajax_blogs/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="<?php echo $plugin['blog']['icon_class']?>"></i> <?php echo __('Blog')?>
				<span class="badge_counter"><?php echo $user['User']['blog_count']?></span></a>
			</li>
			<?php endif; ?>
            <?php if (!empty($plugin['topic'])): ?>
			<li>
			    <a data-url="<?php echo $this->request->base?>/users/ajax_topics/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="<?php echo $plugin['topic']['icon_class']?>"></i> <?php echo __('Topics')?>
				<span class="badge_counter"><?php echo $user['User']['topic_count']?></span></a>
			</li>		
			<?php endif; ?>
            <?php if (!empty($plugin['video'])): ?>
			<li><a data-url="<?php echo $this->request->base?>/users/ajax_videos/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="<?php echo $plugin['video']['icon_class']?>"></i> <?php echo __('Videos')?>  
				<span class="badge_counter"><?php echo $user['User']['video_count']?></span></a>
			</li>	
			<?php endif; ?>
			
			<?php
            if ( $this->elementExists('menu/user') )
                echo $this->element('menu/user');
            ?>
		</ul>
	</div>
	<?php endif; ?>

	<?php if ($user['User']['friend_count']): ?>
	<div class="box2 box_style1" style="margin-top:10px">
		<h3><?php echo __('Friends')?> (<?php echo $user['User']['friend_count']?>)</h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/users_block', array( 'users' => $friends ) ); ?>
		</div>
	</div>
	<?php endif; ?>
	
	<?php if ( !empty( $mutual_friends ) ): ?>
	<div class="box2">
		<h3><a href="<?php echo $this->request->base?>/friends/ajax_show_mutual/<?php echo $user['User']['id']?>" class="overlay" title="<?php echo __('Mutual Friends')?>"><?php echo __('Mutual Friends')?></a></h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/users_block', array( 'users' => $mutual_friends ) ); ?>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( $canView ): ?>
	    
	    <?php echo $this->element('hooks', array('position' => 'profile_sidebar') ); ?>
	
		<?php if (count($videos) > 0): ?>
		<div class="box2">
			<h3><?php echo __('Videos')?></h3>
			<div class="box_content">
			    <?php echo $this->element('blocks/videos_block'); ?>
		    </div>
		</div>
		<?php endif; ?>
	
		<?php if (!empty($blogs)): ?>
		<div class="box2">
			<h3><?php echo __('Blogs')?></h3>	
			<div class="box_content">
			    <?php echo $this->element('blocks/blogs_block'); ?>
			</div>
		</div>
		<?php endif; ?>
	
		<?php if (!empty($groups)): ?>
		<div class="box2">
			<h3><?php echo __('Groups')?></h3>
			<div class="box_content">
			<?php
			$i = 1;
			foreach ($groups as $group)
			{
				echo '<a href="' . $this->request->base . '/groups/view/' . $group['Group']['id'] . '">' . h($group['Group']['name']) . '</a>';
		
				if ($i < count($groups)) 
					echo ', ';
				$i++;
			}
			?>
			</div>
		</div>
		<?php endif; ?>
		
	<?php endif; ?>
		
	<div class="box4">
		<ul class="list6 list6sm">
			<?php if ( !empty($cuser['role_id']) && $cuser['Role']['is_admin'] && !$user['User']['featured'] ): ?>
			<li><a href="<?php echo $this->request->base?>/admin/users/feature/<?php echo $user['User']['id']?>"><?php echo __('Feature User')?></a></li>
			<?php endif; ?>
			<?php if ( !empty($cuser['role_id']) && $cuser['Role']['is_admin'] && $user['User']['featured'] ): ?>
			<li><a href="<?php echo $this->request->base?>/admin/users/unfeature/<?php echo $user['User']['id']?>"><?php echo __('Unfeature User')?></a></li>
			<?php endif; ?>
			<?php if ( !empty($cuser['role_id']) && $cuser['Role']['is_admin'] && !$user['Role']['is_admin'] ): ?>
			<li><a class="overlay"  title="<?php echo __('Edit User')?>" href="<?php echo $this->request->base?>/admin/users/edit/<?php echo $user['User']['id']?>"><?php echo __('Edit User')?></a></li>
			<?php endif; ?>
			<li><a href="<?php echo $this->request->base?>/reports/ajax_create/user/<?php echo $user['User']['id']?>" class="overlay" title="<?php echo __('Report User')?>"><?php echo __('Report User')?></a></li>
			<?php if ( !empty($uid) && $areFriends ): ?>
            <li><a href="javascript:void(0)" onclick="return removeFriend(<?php echo $user['User']['id']?>)"><?php echo __('Unfriend')?></a></li>
            <?php endif; ?>			
		</ul>	
	</div>	
</div>

<div id="center" class="profilePage">
	<div id="profile-content" style="">
		<?php 
		if ( !empty( $activity ) )
		{
			echo '<ul class="list6 comment_wrapper" id="list-content">';
			echo $this->element( 'activities', array( 'activities' => array( $activity ) ) );
			echo '</ul>';
		}
		else
		{		
			if ( $canView )
				echo $this->element('ajax/profile_detail');
			else
				printf( __('%s only shares some information with everyone'), $user['User']['name'] );
		}		
		?>
	</div>
</div> 