<script>
jQuery(document).ready(function(){
	jQuery('#createButton').click(function(){
        disableButton('createButton');
        jQuery.post("<?php echo $this->request->base?>/admin/spam_challenges/ajax_save", jQuery("#createForm").serialize(), function(data){
            enableButton('createButton');
            var json = jQuery.parseJSON(data);
            
            if ( json.result == 1 )
            	location.reload();
            else
            {
            	jQuery(".error-message").fadeIn();
				jQuery(".error-message").html(json.message);
            }
        });
    });
});
</script>

<form id="createForm">
<?php echo $this->Form->hidden('id', array('value' => $challenge['SpamChallenge']['id'])); ?>
<ul class="list6 list6sm2">
	<li><label>Question</label>
		<?php echo $this->Form->text('question', array('value' => $challenge['SpamChallenge']['question'])); ?>
	</li>
	<li><label>Answers (<a href="javascript:void(0)" class="tip" title="One answer per line. Case is not sensitive">?</a>)</label>
		<?php echo $this->Form->textarea('answers', array('value' => $challenge['SpamChallenge']['answers'])); ?>
	</li>
	<li><label>Active</label>
		<?php echo $this->Form->checkbox('active', array( 'checked' => $challenge['SpamChallenge']['active'] ) );	?>
	</li>
	<li><label>&nbsp;</label>
		<a href="#" id="createButton" class="button button-action"><i class="icon-save"></i> Save Challenge</a>
	</li>
</ul>
</form>
<div class="error-message" style="display:none;margin-top:10px;"></div>