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
		jQuery.post("<?php echo $this->request->base?>/conversations/ajax_doAdd", jQuery("#sendMessage").serialize(), function(data){
			enableButton('sendButton');
            var json = jQuery.parseJSON(data);
                
            if ( json.result == 1 )
                window.location.reload();
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
<?php echo $this->Form->hidden('msg_id', array('value' => $msg_id)) ?>
<ul class="list6 list6sm2" style="position:relative;">	
	<li><?php echo $this->Form->text('friends'); ?></li>
	<li><?php echo __('People you add will see all previous messages in this conversation')?></li>		
	<li><a href="#" class="button button-action" id="sendButton"><i class="icon-plus-sign"></i> <?php echo __('Add People')?></a>
	</li>
</ul>
</form>
<div class="error-message" style="display:none;margin-top:10px;"></div>