<div id="jsn_userphoto">
	<i class="status_big <?php if($user['User']['online']) echo 'ep_online'; else echo 'ep_offline';?>"></i>
	<img <?php if(empty($user['User']['avatar_clean'])) echo 'avatar="'.$user['User']['name'].'"'; ?> class="avatar" src="<?php echo $user['User']['photo']?>" alt="avatar"/>
</div>
<h1 id="jsn_username"><?php echo $user['User']['name']?></h1>
<div id="cover_container">
<script>
jQuery(document).ready(function($){
	$('#cover').height($('#cover').width()/3.5);
	$('.jsn_profile .zocial').each(function(){
		var social_link=$(this).parent();
		$('.jsn_sociallink').append(social_link);
	});
});
jQuery(window).resize(function(){
	jQuery('#cover').height(jQuery('#cover').width()/3.5);
});
</script>
<div id="cover" <?php if ( !empty( $user['User']['cover'] ) ): ?>style="background-image: url(<?php echo $this->request->webroot?>uploads/covers/<?php echo $user['User']['cover']?>)"<?php endif; ?>>
	<?php if ( !empty( $cover_album_id ) ): ?>
	<a href="<?php echo $this->request->base?>/albums/view/<?php echo $cover_album_id?>"></a>
	<?php endif; ?>

	<div id="actions">
		<?php if ($user['User']['id'] == $uid || JFactory::getUser()->authorise('core.edit', 'com_users')): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=profile&layout=edit&user_id='.$user['User']['id'],false);?>" class="topButton button button-action" ><i class="icon icon-cog"></i> <?php echo __('Edit Profile')?></a>
		<?php endif; ?>
		<?php if ($user['User']['id'] != $uid && !empty($uid)): ?>

	        <a class="topButton button button-action overlay" href="<?php echo $this->request->base?>/conversations/ajax_send/<?php echo $user['User']['id']?>" title="<?php echo __('Send New Message')?>"><i class="icon icon-envelope"></i> <?php echo __('Send Message')?></a>

			<?php if ( !empty($request_sent) ): ?>
			<span class="topButton disabled  button"><?php echo __('Request Sent')?></span>
			<?php endif; ?>

			<?php if ( !empty($uid) && !$areFriends && empty($request_sent) ): ?>
			<a href="<?php echo $this->request->base?>/friends/ajax_add/<?php echo $user['User']['id']?>" id="addFriend_<?php echo $user['User']['id']?>" class="overlay topButton button button-action" title="<?php printf( __('Send %s a friend request'), h($user['User']['name']) )?>"><i class="icon icon-group"></i> <?php echo __('Add as Friend')?></a>
			<?php endif; ?>

		<?php endif;?>
		<?php if ( $uid == $user['User']['id'] ): ?>
		<div id="cover_upload">
			<a href="#" class="topButton button button-action"><i class="icon icon-picture"></i> <?php echo __('Edit Cover Picture')?></a>
		</div>
		<?php endif; ?>
	</div>
	
</div>
<div id="jsn_featured_info">
	<?php
	$db=JFactory::getDbo();
	$query=$db->getQuery(true);
	$query->select('f.alias')->from('#__jsn_fields as f')->where("alias IN ('registerdate','lastvisitdate')")->where('profile=1');
	$db->setQuery($query);
	$dataField=$db->loadColumn();
	?>
	<?php if(in_array('registerdate',$dataField)) : ?><div class="jsn_featured_infoval jsn_hiddenphone"><i class="icon-user icon"></i> <?php echo $this->Jsnsocial->getTime($user['User']['created'], $jsnsocial_setting['date_format'], $utz)?></div><?php endif; ?>
	<?php if(in_array('lastvisitdate',$dataField) && $user['User']['last_login']!='0000-00-00 00:00:00') : ?><div class="jsn_featured_infoval jsn_hiddenphone"><i class="icon-eye-open icon"></i> <?php echo $this->Jsnsocial->getTime($user['User']['last_login'], $jsnsocial_setting['date_format'], $utz)?></div><?php endif; ?>
	<div class="jsn_featured_infoval"><i class="icon-group icon"></i> <?php echo $user['User']['friend_count']?> <?php echo __('Friends')?></div>
	<div class="jsn_featured_infoval"><i class="icon-picture icon"></i> <?php echo $user['User']['photo_count']?> <?php echo __('Photos')?></div>
	<div class="jsn_sociallink"></div>
	<div class="clear"></div>
</div>
</div>