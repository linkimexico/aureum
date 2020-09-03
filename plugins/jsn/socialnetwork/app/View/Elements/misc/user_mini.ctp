<div class="user_mini">
	<?php echo $this->Jsnsocial->getUserAvatar($user)?>
	<div class="comment">
		<?php echo $this->Jsnsocial->getName($user)?><br />			
		<div class="comment_message">
			<span class="date">
				<?php echo __n( '%s friend', '%s friends', $user['friend_count'], $user['friend_count'] )?> . 
				<?php echo __n( '%s photo', '%s photos', $user['photo_count'], $user['photo_count'] )?>
			</span><br />
			<?php if ( !empty($uid) && $uid != $user['id'] && !$areFriends ): ?>
			<a href="<?php echo $this->request->base?>/friends/ajax_add/<?php echo $user['id']?>" id="addFriend_<?php echo $user['id']?>" class="overlay" title="<?php printf( __('Send %s a friend request'), h($user['name']) )?>"><?php echo __('Add as Friend')?></a><br />
			<?php endif; ?>
		</div>
	</div>
</div>