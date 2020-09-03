<?php //echo $this->element('profilenav', array("cmenu" => "profile"));?>

<form action="<?php echo $this->request->base?>/users/profile" method="post">
<div class="post_body">
    
	
	<?php echo $this->element('ajax/profile_edit');?>
	
	<?php if ( !$cuser['Role']['is_super'] ): ?> 
	<ul class="list6 list6sm" style="margin:10px 0">
		<li><a href="javascript:void(0)" onclick="jQuery('#simple-modal-overlay').remove();jQuery('#simple-modal').remove();jsnsocialConfirm('<?php echo addslashes(__('Are you sure you want to deactivate your account? Your profile will not be accessible to anyone and you will not be able to login again!'))?>', '<?php echo $this->request->base?>/users/deactivate')"><?php echo __('Deactivate my account')?></a></li> 
		<li><a href="javascript:void(0)" onclick="jQuery('#simple-modal-overlay').remove();jQuery('#simple-modal').remove();jsnsocialConfirm('<?php echo addslashes(__('Are you sure you want to permanently delete your account? All your contents (including groups, topics, events...) will also be permanently deleted!'))?>', '<?php echo $this->request->base?>/users/request_deletion')"><?php echo __('Request to permanently delete my account')?></a></li>
	</ul>
	<?php endif; ?>
</div>
</form>