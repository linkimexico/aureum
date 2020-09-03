<style>
.delete-icon {
	top: 16px;
	right: 15px;
}
</style>
<script>
jQuery(document).ready(function(){

	jQuery("#notifications_list li").hover(
		function () {
		jQuery(this).contents().find('.delete-icon').show();
	  }, 
	  function () {
		jQuery(this).contents().find('.delete-icon').hide();
	  }
	);
});

function removeNotification(id)
{
	jQuery.get('<?php echo $this->request->base?>/notifications/ajax_remove/'+id);
	jQuery("#noti_"+id).slideUp();
	
	if ( jQuery('#noti_' + id).hasClass('unread') && jQuery("#notification_count").html() != '0' )
	{
		var noti_count = parseInt(jQuery("#notification_count").html()) - 1;
		
		jQuery("#notification_count").html( noti_count );
		jQuery("#new_notifications").html( noti_count );
		
		Tinycon.setBubble( noti_count );
	}
}

function clearNotifications()
{
	jQuery.get('<?php echo $this->request->base?>/notifications/ajax_clear');
	jQuery("#notifications_list").slideUp();
	jQuery("#new_notifications").fadeOut();	
	jQuery("#notification_count").html('0');
	Tinycon.setBubble( 0 );
}
</script>

<?php if ( $type == 'home' ): ?>
	<?php if ( !empty($notifications) ): ?>
	<a href="javascript:void(0)" onclick="return clearNotifications()" class="button button-caution topButton"><?php echo __('Clear All Notifications')?></a>
	<?php endif; ?>
	<h1 style="margin-bottom: 15px"><?php echo __('Notifications')?></h1>
<?php endif; ?>

<?php 
if (count($notifications) == 0):
	echo __('No new notifications');
else:
?>
<ul class="list2" id="notifications_list">
<?php 
	foreach ($notifications as $noti):
?>
	<li id="noti_<?php echo $noti['Notification']['id']?>">
		<a href="<?php echo $this->request->base?>/notifications/ajax_view/<?php echo $noti['Notification']['id']?>" <?php if (!$noti['Notification']['read']) echo 'class="unread"';?>>
			<span class="<?php if($noti['Sender']['online']) echo 'ep_online'; else echo 'ep_offline';?>"></span><img src="<?php echo $this->Jsnsocial->getUserPicture($noti['Sender']['avatar'])?>" class="img_wrapper2"> 
			<b><?php echo h($noti['Sender']['name'])?></b> 
			<?php echo $this->element('misc/notification_texts', array( 'noti' => $noti ));	?>
			<br />
			<span class="date"><?php echo $this->Jsnsocial->getTime( $noti['Notification']['created'], $jsnsocial_setting['date_format'], $utz )?></span>
		</a>

		<a href="javascript:void(0)" onclick="return removeNotification(<?php echo $noti['Notification']['id']?>)" style="padding:0"><i class="icon-remove delete-icon"></i></a>
	</li>
<?php
	endforeach;
?>
</ul>
<?php
endif;
?>