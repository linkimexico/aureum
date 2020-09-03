<script>
jQuery(document).ready(function(){
	jQuery('#reportButton').click(function(){
		disableButton('reportButton');
		jQuery.post("<?php echo $this->request->base?>/reports/ajax_save", jQuery("#reportForm").serialize(), function(data){
			enableButton('createButton');
			
			var json = jQuery.parseJSON(data);
            
            if ( json.result == 1 )
            {
                jQuery(".error-message").hide();
                jQuery('.simple-modal-body').html(json.message);
            }
            else
            {
                jQuery(".error-message").show();
                jQuery(".error-message").html(json.message);
            }   
		});
	});
	return false;
});
</script>

<div class="error-message" style="display:none;"></div>
<form id="reportForm">
<?php echo $this->Form->hidden('type', array( 'value' => $type ) ); ?>
<?php echo $this->Form->hidden('target_id', array( 'value' => $target_id ) ); ?>
<ul class="list6 list6sm2" style="position:relative">
	<li><label><?php echo __('Reason')?></label>
		<?php echo $this->Form->textarea('reason'); ?>
	</li>
	<li><label>&nbsp;</label>
	    <a href="#" class="button button-caution" id="reportButton"><i class="icon-warning-sign"></i> <?php echo __('Report')?></a>
	</li>
</ul>
</form>