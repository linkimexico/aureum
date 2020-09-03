<script>
jQuery(document).ready(function(){
	
	<?php if ( empty( $to ) ): ?>
	jQuery("#friends").tokenInput("<?php echo $this->request->base?>/friends/do_get_json", 
		{ preventDuplicates: true, 
		  hintText: "<?php echo __('Enter a friend\'s name')?>",
		  noResultsText: "<?php echo __('No results')?>",
		  tokenLimit: 20,
		  resultsFormatter: function(item)
		  { 
		  	var avatar = 'uploads/avatars/' + item.avatar;
            
            if ( !item.avatar )
                avatar = 'img/no_avatar.jpg';
            
            if(!avatar.startsWith("http")) avatar = '<?php echo $this->request->root?>/' + avatar;
            
            return '<li><img src="' + avatar + '" style="width: 40px" align="absmiddle"> ' + item.name + '</li>'; 
		  } 
		}
	);
	<?php endif; ?>

	jQuery('#sendButton').click(function(){
		disableButton('sendButton');
		jQuery.post("<?php echo $this->request->base?>/conversations/ajax_doSend", jQuery("#sendMessage").serialize(), function(data){
			enableButton('sendButton');
			var json = jQuery.parseJSON(data);
            
            if ( json.result == 1 )
            {
                jQuery("#subject").val('');
                jQuery("#message").val('');
                jQuery(".error-message").hide();
                sModal.hideModal();
                jsnsocialAlert('<?php echo addslashes(__('Your message has been sent'))?>');
            }
            else
            {
                jQuery(".error-message").show();
                jQuery(".error-message").html(json.message);
            }       
		});
		
		return false;
	});
});
</script>

<form id="sendMessage">
<ul class="list6 list6sm2" style="position:relative">
	<?php if (!empty($to)): ?>
	<input type="hidden" name="data[friends]" value="<?php echo $to['User']['id']?>">
	<li><label><?php echo __('Send to')?></label><?php echo h($to['User']['name'])?></li>
	<?php else: ?>
	<li><label><?php echo __('Send to')?></label><?php echo $this->Form->text('friends'); ?></li>
	<?php endif; ?>
	<li><label><?php echo __('Subject')?></label><?php echo $this->Form->text('subject'); ?></li>
	<li><label><?php echo __('Message')?></label><?php echo $this->Form->textarea('message', array('style' => 'height:120px')); ?></li>
	<li><label>&nbsp;</label><a href="#" class="button button-action" id="sendButton"><i class="icon-envelope"></i> <?php echo __('Send Message')?></a></li>
</ul>
</form>
<div class="error-message" style="display:none;"></div>