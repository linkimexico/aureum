<?php
echo $this->Html->script(array('jquery.fineuploader'));
echo $this->Html->css(array( 'fineuploader' ));
?> 
 
<script>
jQuery(document).ready(function(){
	jQuery("#albums").change(function(){
		jQuery('#uploadPhotos').spin('small');
		jQuery('#uploadArea').load('<?php echo $this->request->base?>/photos/ajax_upload/album/' + jQuery("#albums").val(), function(){
			jQuery('#uploadPhotos').spin(false);
		});
	});

	if (jQuery('#albums').val() != '')
	{
		jQuery('#uploadPhotos').spin('small');
		jQuery('#uploadArea').load('<?php echo $this->request->base?>/photos/ajax_upload/album/'+jQuery('#albums').val(), function(){
			jQuery('#uploadPhotos').spin(false);
		});
	}
});
</script>

<div class="box3">
	<a href="<?php echo $this->request->base?>/albums/ajax_create" class="overlay button button-action topButton" title="<?php echo __('Create New Album')?>"><?php echo __('Create New Album')?></a>
	<h1><?php echo __('Upload Photos')?></h1>	
	<div id="firstAlbum" style="display:<?php if (count($albums) > 0) echo 'none';?>">
		<a class="overlay" href="<?php echo $this->request->base?>/albums/ajax_create" title="<?php echo __('Create New Album')?>"><?php echo __('Click here')?></a> <?php echo __('to create your first album')?>
	</div>
	<div id="uploadPhotos" style="display:<?php if (count($albums) == 0) echo 'none';?>">
		<?php echo $this->Form->select('albums', $albums, array('value' => $aid)); ?>
		<div id="uploadArea"></div>
	</div>
</div>