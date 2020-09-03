<script>
function respondRequest(id, status)
{
	jQuery.post('<?php echo $this->request->base?>/groups/ajax_respond', {id: id, status: status}, function(data){
		jQuery('#request_'+id).html(data);
	});
}
</script>

<?php if (empty($requests)): echo '<div align="center">' . __('No join requests') . '</div>';
else: ?>
<ul class="list6 comment_wrapper" style="margin-top:0">
<?php foreach ($requests as $request): ?>
	<li id="request_<?php echo $request['GroupUser']['id']?>">
		<div style="float:right">
		    <a href="javascript:void(0)" onclick="respondRequest(<?php echo $request['GroupUser']['id']?>, 1)" class="button button-action"><?php echo __('Accept')?></a>
		    <a href="javascript:void(0)" onclick="respondRequest(<?php echo $request['GroupUser']['id']?>, 0)" class="button button-caution"><?php echo __('Delete')?></a>
		</div>
		<?php echo $this->Jsnsocial->getUserAvatar($request['User'])?>
		<div class="comment">
			<?php echo $this->Jsnsocial->getName($request['User'])?><br />
			<span class="date"><?php echo $this->Jsnsocial->getTime( $request['GroupUser']['created'], $jsnsocial_setting['date_format'], $utz )?></span>
		</div>
	</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>