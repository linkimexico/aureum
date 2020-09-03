<script>
jQuery(document).ready(function(){
	jQuery('#addFriendButton').click(function(){		
		disableButton('addFriendButton');
		jQuery.post("<?php echo $this->request->base?>/friends/ajax_sendRequest", jQuery("#addFriendForm").serialize(), function(data){
			enableButton('addFriendButton');
			jQuery('#simple-modal-body').html(data);
			//jQuery('#addFriend_<?php echo $user['User']['id']?>').replaceWith('<span class="button disabled topButton"><?php echo addslashes(__('Request Sent'))?></span>');
			jQuery('#addFriend_<?php echo $user['User']['id']?>').remove();
		});
		return false;		
	});
});
</script> 

<div style="margin:0 0 5px 0"><?php printf( __('You can send <b>%s</b> an optional message below'), h($user['User']['name']) ); ?></div>
<form id="addFriendForm">
<input type="hidden" name="user_id" value="<?php echo $user['User']['id']?>">
<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td width="85" valign="top"><img src="<?php echo $this->Jsnsocial->getUserPicture($user['User']['photo'])?>" class="img_wrapper"></td>
		<td><textarea name="message"></textarea></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><br /><a href="#" id="addFriendButton" class="button button-action"><i class="icon-envelope"></i> <?php echo __('Send Request')?></a></td>
	</tr>
</table>
</form>