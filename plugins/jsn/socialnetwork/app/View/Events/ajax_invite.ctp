<script>
jQuery(document).ready(function(){

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

	jQuery('#sendButton').click(function(){
		disableButton('sendButton');
		jQuery.post("<?php echo $this->request->base?>/events/ajax_sendInvite", jQuery("#sendInvite").serialize(), function(data){
			enableButton('sendButton');	
			if (data != '') {
				jQuery('#simple-modal-body').html(data);				
			}
		});		
		return false;
	});
});
</script>

<div class="message" style="display:none;"></div>
<form id="sendInvite">
<?php echo $this->Form->hidden('event_id', array('value' => $event_id)); ?>
<ul class="list6" style="position:relative">
	<li><?php echo $this->Form->text('friends'); ?></li>	
	<li><?php echo __('Not on your friends list? Enter their emails below (separated by commas)<br />Limit 10 email addresses per request')?><br />
		<?php echo $this->Form->textarea('emails'); ?>
	</li>
	<li><a href="#" class="button button-action" id="sendButton"><i class="icon-envelope"></i> <?php echo __('Send Invitations')?></a></li>
</ul>
</form>