<script>
function addFriend( uid )
{
	jQuery.post("<?php echo $this->request->base?>/friends/ajax_sendRequest", {user_id: uid, message: ''}, function() {
		jQuery('#friend_'+uid).fadeOut();
	});
}
</script>

<ul class="list1" id="list-content">
<?php foreach ($suggestions as $friend): ?>
	<li id="friend_<?php echo $friend['User']['id']?>"><?php echo $this->Jsnsocial->getUserAvatar($friend['User'], 40)?>
		<div style="margin-left:50px">
			<?php echo $this->Jsnsocial->getName($friend['User'])?><br />
			<span class="date"><?php echo __n( '%s mutual friend', '%s mutual friends', $friend[0]['count'], $friend[0]['count'] )?></span><br />				
			<a href="javascript:void(0)" id="addFriend_<?php echo $friend['User']['id']?>" onclick="addFriend(<?php echo $friend['User']['id']?>)"><?php echo __('Add as friend')?></a>
		</div>
	</li>
<?php endforeach; ?>
</ul> 