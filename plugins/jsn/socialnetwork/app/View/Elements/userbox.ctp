<div id="userbox">    
    <input type="text" id="global-search" placeholder="<?php echo __('Search')?>">
	<?php if (!empty($uid)): ?>		
		<span class="button-dropdown" data-buttons="dropdown">
            <a href="#" class="button button-flat-primary button-flat"> <span id="jsn_cuser_name"><?php echo h($cuser['name'])?></span><i id="jsn_cuser_name_icon" class="icon-cog"></i> <i class="icon-caret-down"></i></a>
            <ul>
                <li><a href="<?php echo $this->Jsnsocial->getProfileUrl( $cuser )?>"><?php echo h($cuser['name'])?></a></li>
                <?php if ( $cuser['Role']['is_admin'] && empty( $jsnsocial_settings['hide_admin_link'] ) ): ?>
                <li><a href="<?php echo $this->request->base?>/admin/home"><?php echo __('Admin Panel')?></a></li>
                <?php endif; ?>
                <li><a href="<?php
					$app = JFactory::getApplication();
					$menu = $app->getMenu();
					$editMenu=$menu->getItems('link','index.php?option=com_users&view=profile&layout=edit',true);
					if(isset($editMenu->id)) echo JRoute::_('index.php?option=com_users&view=profile&layout=edit&Itemid='.$editMenu->id,false);
					else echo JRoute::_('index.php?option=com_users&view=profile&layout=edit',false);
					?>"><?php echo __('Profile Information')?></a></li>
                <li><a class="overlay" title="<?php echo __('User Settings')?>" href="<?php echo $this->request->base?>/users/profile/edit"><?php echo __('User Settings')?></a></li>
				<?php if ( $cuser['conversation_user_count'] > 0 ): ?>
                <li><a href="<?php echo $this->request->base?>/home/index/tab:messages"><?php echo __('New Messages (%s)', $cuser['conversation_user_count'])?></a></li>
                <?php endif; ?>
                <?php if ( $cuser['friend_request_count'] > 0 ): ?>
                <li><a href="<?php echo $this->request->base?>/home/index/tab:friend-requests"><?php echo __('Friend Requests (%s)', $cuser['friend_request_count'])?></a></li>
                <?php endif; ?>
                <li><a href="<?php echo $this->request->base?>/home/index/tab:invite-friends"><?php echo __('Invite Friends')?></a>
                <li><a href="<?php echo $this->request->base?>/users/do_logout"><?php echo __('Log Out')?></a></li>
            </ul>
        </span>
		
		<a id="member-link" href="<?php echo $this->Jsnsocial->getProfileUrl( $cuser )?>"><img width="45" <?php if(empty($cuser['avatar_clean'])) echo 'avatar="'.$cuser['name'].'"'; ?> alt="<?php echo $cuser['name']?>" src="<?php echo $this->Jsnsocial->getUserPicture($cuser['avatar'])?>" id="member-avatar"></a>
		
		<?php if (!empty($cuser['notification_count'])): ?>		
			<script>jQuery(document).ready(function(){Tinycon.setBubble(<?php echo $cuser['notification_count']?>);});</script>    
			<a href="<?php echo $this->request->base?>/notifications/ajax_show" id="new_notifications" class="overlay" title="<?php echo __n( '%s New Notification', '%s New Notifications', $cuser['notification_count'], $cuser['notification_count'] )?>"><?php echo $cuser['notification_count']?></a>
		<?php else : ?>
			<a style="display:none;" href="<?php echo $this->request->base?>/notifications/ajax_show" id="new_notifications" class="overlay" title="">0</a>
		<?php endif; ?>
		<script>
		jQuery(document).ready(function(){
			setInterval(function(){
				jQuery.ajax(
					{
						url : "<?php echo $this->request->base?>/notifications/ajax_count",
						success : function (data,stato) {
							if(jQuery("#new_notifications").html()!=data && jQuery.isNumeric(data))
							{ 
								if(data=='0'){
									jQuery("#new_notifications").hide();
									Tinycon.setBubble( 0 );
								}
								else {
									if(data=='1') jQuery("#new_notifications").html( data ).attr('title',data+' <?php echo __('New Notification')?>').show();
									else jQuery("#new_notifications").html( data ).attr('title',data+' <?php echo __('New Notification')?>').show();
									Tinycon.setBubble( data );
								}
								jQuery("#notification_count").html( data );
							}
							
						}
				    }
				);
				//alert('time');
			},10000);
		});
		</script>
	<?php else: ?>
	    <a href="#" class="button button-flat-primary button-flat" id="loginButton"> <?php echo __('Login')?> <i class="icon-caret-down"></i></a>
        <div id="loginForm" class="jsnsocial-dropdown">
        	<div class="dropdown-caret right">
              <span class="caret-outer"></span>
              <span class="caret-inner"></span>
            </div>
			<?php
				$document	= JFactory::getDocument();
				$renderer	= $document->loadRenderer('module');
				$mod		= JModuleHelper::getModule('mod_login', '');
				$params = array('style' => '');
				//print_r($mod);
				$from=array('btn','add-on','input-prepend','input-append','control-group');
				$to=array('button','hide','','','');
				$loginContent=str_replace($from,$to,$renderer->render($mod)); 
				echo $loginContent;
			?>
    	</div>
	<?php endif; ?>
</div>
