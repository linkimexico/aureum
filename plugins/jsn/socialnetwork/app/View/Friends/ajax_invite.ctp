<script>
jQuery(document).ready(function(){
	jQuery('#inviteButton').click(function(){		
		disableButton('inviteButton');
		jQuery.post("<?php echo $this->request->base?>/friends/ajax_invite", jQuery("#inviteForm").serialize(), function(data){
			enableButton('inviteButton');
			if ( data == '' )
			{
				jsnsocialAlert('<?php echo __('Your invitation has been sent')?>');
				jQuery('#to').val('');
				jQuery('#message').val('');
			}
		});	
		return false;	
	});
});
</script>

<div class="post_body">
    <h1><?php echo __('Invite Your Friends')?></h1>
    <?php echo __("Enter your friends' emails below (separated by commas). Limit 10 email addresses per request")?><br /><br />
    <form id="inviteForm">
    <ul class="list6 list6sm2">
    	<li><label><?php echo __('Send to')?></label><?php echo $this->Form->textarea('to'); ?></li>
    	<li><label><?php echo __('Message')?></label><?php echo $this->Form->textarea('message'); ?></li>
    	<li><label>&nbsp;</label>
    	    <a href="#" class="button button-action" id="inviteButton"><i class="icon-envelope"></i> <?php echo __('Send Invitation')?></a>
    	</li>
    </ul>
    </form>
</div>