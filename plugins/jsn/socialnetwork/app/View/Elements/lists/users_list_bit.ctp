<?php
$profileMenu=JFactory::getApplication()->getMenu()->getItems('link','index.php?option=com_jsn&view=profile',true);
if(isset($profileMenu->id)) $Itemid=$profileMenu->id;
else $Itemid='';

if (count($users) > 0)
{	
	foreach ($users as $user):
?>
	<li <?php if ( isset($type) && $type == 'home' ): ?>id="friend_<?php echo $user['Friend']['friend_id']?>"<?php endif; ?>
		<?php if ( isset($group) ): ?>id="member_<?php echo $user['GroupUser']['id']?>"<?php endif; ?>
	>
		<a href="<?php echo JRoute::_('index.php?option=com_jsn&view=profile&Itemid='.$Itemid.'&id='.$user['User']['id']);?>"><i class="<?php if($user['User']['online']) echo 'ep_online'; else echo 'ep_offline';?>"></i><img <?php if(empty($user['User']['avatar_clean'])) echo 'avatar="'.$user['User']['name'].'"'; ?> src="<?php echo $this->Jsnsocial->getUserPicture($user['User']['photo'], false)?>" class="img_wrapper2"></a>
		
		<?php if ( isset($type) && $type == 'home' ): ?>
		<a href="javascript:void(0)" onclick="return removeFriend(<?php echo $user['Friend']['friend_id']?>)"><i class="icon-remove icon-large delete-icon"></i></a>
		<?php endif; ?>
		
		<div class="comment">
			<?php echo $this->Jsnsocial->getName($user['User'])?>
			<div class="comment_message">
				<span class="date">
					
					<i class="icon icon-group"></i> <?php echo __n( '%s friend', '%s friends', $user['User']['friend_count'], $user['User']['friend_count'] )?> . 
					<i class="icon icon-picture"></i> <?php echo __n( '%s photo', '%s photos', $user['User']['photo_count'], $user['User']['photo_count'] )?><br />
					
					<?php if ( isset($group) && isset($admins) && $user['User']['id'] != $uid && $group['User']['id'] != $user['User']['id'] && 
							   ( !empty($cuser['Role']['is_admin']) || in_array($uid, $admins) ) ): 
					?>
					<a href="javascript:void(0)" onclick="removeMember(<?php echo $user['GroupUser']['id']?>)"><?php echo __('Remove Member')?></a> .
					<?php endif; ?>
					
					<?php if ( isset($group) && isset($admins) && !in_array($user['User']['id'], $admins) && 
							   ( !empty($cuser['Role']['is_admin']) || $uid == $group['User']['id'] ) ): 
					?>
					<a href="javascript:void(0)" onclick="changeAdmin(<?php echo $user['GroupUser']['id']?>, 'make')"><?php echo __('Make Admin')?></a>
					<?php endif; ?>
					
					<?php if ( isset($group) && isset($admins) && in_array($user['User']['id'], $admins) && $user['User']['id'] != $group['User']['id'] && 
							   ( !empty($cuser['Role']['is_admin']) || $uid == $group['User']['id'] ) ): 
					?>
					<a href="javascript:void(0)" onclick="changeAdmin(<?php echo $user['GroupUser']['id']?>, 'remove')"><?php echo __('Remove Admin')?></a>
					<?php endif; ?>
					
					<?php if ( isset($friends_requests) && !in_array($user['User']['id'], $friends_requests) && $user['User']['id'] != $uid): ?>
					<a href="<?php echo $this->request->base?>/friends/ajax_add/<?php echo $user['User']['id']?>" id="addFriend_<?php echo $user['User']['id']?>" style="margin-top:4px;" class="overlay" title="<?php printf( __('Send %s a friend request'), h($user['User']['name']) )?>"><?php echo __('Add as friend')?></a>
					<?php endif; ?>
					
					<?php if ( isset($friends) && in_array($user['User']['id'], $friends) && $user['User']['id'] != $uid): ?>
					<span style="margin-top:4px;" class="button button-small button-highlight disabled"><i class="icon icon-group"></i> <?php echo __('Friend')?></span>
					<?php endif; ?>
					
					<?php if ( $user['User']['id'] == $uid): ?>
					<span style="margin-top:4px;" class="button button-small button-action disabled"><i class="icon icon-user"></i> <?php echo __('Me')?></span>
					<?php endif; ?>
					
				</span>
			</div>
		</div>	
	</li>
<?php 
	endforeach;
}
else
	echo '<div align="center">' . __('No more results found') . '</div>';
?>