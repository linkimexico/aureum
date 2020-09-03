<?php
echo $this->Html->script(array('tinymce/tinymce.min', 'jquery.fineuploader'), array('inline' => false));
echo $this->Html->css(array( 'fineuploader' ));

$tags_value = '';
if (!empty($tags)) $tags_value = implode(', ', $tags);
?>

<script>
tinymce.init({
    selector: "textarea",
    theme: "modern",
    plugins: [
        "emoticons link image"
    ],
    toolbar1: "bold italic underline strikethrough | bullist numlist | link unlink image emoticons",
    image_advtab: true,
    height: 400,
    menubar: false,
    forced_root_block : 'div',
    relative_urls : false,
    remove_script_host : true,
    document_base_url : '<?php echo FULL_BASE_URL . $this->request->root?>'
});

jQuery(document).ready(function(){ 
    jQuery('#photos_upload').fineUploader({
        autoUpload: false,
        request: {
            endpoint: "<?php echo $this->request->base?>/upload/images"
        },
        text: {
            uploadButton: '<?php echo __('Select Files')?>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            sizeLimit: 10 * 1024 * 1024
        }
    }).on('complete', function(event, id, fileName, response) {
        tinyMCE.activeEditor.insertContent('<p align="center"><a href="<?php echo $this->request->webroot?>uploads/images/' + response.filename + '" class="attached-image"><img src="<?php echo $this->request->webroot?>uploads/images/t_' + response.filename + '"></a></p><br>');        
    });
    
    jQuery('#triggerUpload').click(function() {
        jQuery('#photos_upload').fineUploader("uploadStoredFiles");
        return false;
    });
    
    jQuery('#createButton').click(function(){
        jQuery('#editor').val(tinyMCE.activeEditor.getContent());
        createItem('blogs');
        
        return false;
    })
});

function toggleUploader()
{
    jQuery('#images-uploader').slideToggle();
}
</script>

<div class="box3">
	<form id="createForm">
	<?php
	if (!empty($blog['Blog']['id']))
		echo $this->Form->hidden('id', array('value' => $blog['Blog']['id']));
	?>
	
	<h1><?php if (empty($blog['Blog']['id'])) echo __('Write New Entry'); else echo __('Edit Entry');?></h1>	
	
	<ul class="list6 list6sm2">
		<li><label><?php echo __('Title')?></label><?php echo $this->Form->text('title', array('value' => $blog['Blog']['title'])); ?></li>
		<li><label><?php echo __('Body')?></label><?php echo $this->Form->textarea('body', array('value' => $blog['Blog']['body'], 'id' => 'editor')); ?></li>
		<li><label><?php echo __('Tags')?></label><?php echo $this->Form->text('tags', array('value' => $tags_value)); ?> <a href="javascript:void(0)" class="tip" title="<?php echo __('Separated by commas')?>">(?)</a></li>
		<li><label><?php echo __('Privacy')?></label>
			<?php echo $this->Form->select( 'privacy', 
											array( PRIVACY_EVERYONE => __('Everyone'), 
												   PRIVACY_FRIENDS  => __('Friends Only'), 
												   PRIVACY_ME 		=> __('Me Only') ), 
											array( 'value' => $blog['Blog']['privacy'],
												   'empty' => false
										 ) ); 
			?>
		</li>
	</ul>
	</form>
	
	<div style="margin-left:100px;">		
		<div id="images-uploader" style="display:none;margin:10px 0;">
            <div id="photos_upload"></div>
            <a href="#" class="button button-primary" id="triggerUpload"><?php echo __('Upload Queued Files')?></a>
        </div>
        <a href="javascript:void(0)" onclick="toggleUploader()"><?php echo __('Toggle Images Uploader')?></a>
		
		<div style="margin:20px 0">			
		    <a href="#" class="button button-action" id="createButton"><i class="icon-save"></i> <?php echo __('Save')?></a>
			<?php if ( !empty( $blog['Blog']['id'] ) ): ?>
			<a href="<?php echo $this->request->base?>/blogs/view/<?php echo $blog['Blog']['id']?>" class="button"><i class="icon-remove"></i> <?php echo __('Cancel')?></a>
			<?php endif; ?>
			<?php if ( ($blog['Blog']['user_id'] == $uid ) || ( !empty( $blog['Blog']['id'] ) && $cuser['Role']['is_admin'] ) ): ?>
			<a href="javascript:void(0)" onclick="jsnsocialConfirm( '<?php echo __('Are you sure you want to remove this entry?')?>', '<?php echo $this->request->base?>/blogs/do_delete/<?php echo $blog['Blog']['id']?>' )" class="button button-caution"><i class="icon-trash"></i> <?php echo __('Delete')?></a>
			<?php endif; ?>	
		</div>
		<div class="error-message" id="errorMessage" style="display: none;"></div>
	</div>	
</div>