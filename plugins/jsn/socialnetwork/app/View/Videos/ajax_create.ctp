<script>
jQuery(document).ready(function(){
	jQuery('#fetchButton').click(function(){
		disableButton('fetchButton');
		jQuery.post("<?php echo $this->request->base?>/videos/ajax_fetch", jQuery("#createForm").serialize(), function(data){
			enableButton('fetchButton');
			jQuery("#videoForm").html(data);
			jQuery("#fetchVideo").slideUp();
		});
		return false;
	});
});
</script>

<form id="createForm">
	
<div id="fetchVideo">
	<?php echo __('Copy and paste the video url in the text field below')?><br /><br />
	
	<?php 
	if ( !empty( $this->request->data['group_id'] ) )
		echo $this->Form->hidden('group_id', array('value' => $this->request->data['group_id']));
	
	echo $this->Form->hidden('tags');
	?>
	<ul class="list6 list6sm2">
		<li><label><?php echo __('Source')?></label>
			<?php echo $this->Form->select( 'source', 
											array( 'youtube' => 'YouTube', 'vimeo'   => 'Vimeo' ),
											array( 'empty' => false )
										  );
			?>
		</li>
		<li><label><?php echo __('URL')?></label><?php echo $this->Form->text('url'); ?></li>
		<li><label>&nbsp;</label>
		    <a href="#" class="button button-action" id="fetchButton"><i class="icon-ok"></i> <?php echo __('Fetch Video')?></a>
		</li>
	</ul>
</div>

<div id="videoForm"></div>
</form>