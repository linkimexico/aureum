<script>
function respondRequest(id, status)
{
	jQuery.post('<?php echo $this->request->base?>/friends/ajax_respond', {id: id, status: status}, function(data){
		jQuery('#request_'+id).html(data);
	});
}
</script>

<h1><?php echo __('Friend Requests')?></h1>	

<?php if (empty($requests)): echo '<div align="center">' . __('You have no friend requests') . '</div>';
else: ?>
<ul class="list6 comment_wrapper" style="margin-top:0">
<?php foreach ($requests as $request): ?>
	<li id="request_<?php echo $request['FriendRequest']['id']?>">
		<div style="float:right">
		    <a href="javascript:void(0)" onclick="respondRequest(<?php echo $request['FriendRequest']['id']?>, 1)" class="button button-action"><?php echo __('Accept')?></a>
		    <a href="javascript:void(0)" onclick="respondRequest(<?php echo $request['FriendRequest']['id']?>, 0)" class="button button-caution"><?php echo __('Delete')?></a>
		</div>
		<?php echo $this->Jsnsocial->getUserAvatar($request['Sender'])?>
		<div class="comment">
			<?php echo $this->Jsnsocial->getName($request['Sender'])?><br /><?php echo nl2br(h($request['FriendRequest']['message']))?><br />
			<span class="date"><?php echo $this->Jsnsocial->getTime( $request['FriendRequest']['created'], $jsnsocial_setting['date_format'], $utz )?></span>
		</div>
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>