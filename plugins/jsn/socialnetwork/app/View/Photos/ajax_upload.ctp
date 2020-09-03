<?php
if ($target_id):
?>
<script>
var newPhotos = new Array();

jQuery(document).ready(function(){ 
    jQuery('#photos_upload').fineUploader({
        autoUpload: false,
        request: {
            endpoint: "<?php echo $this->request->base?>/upload/photos/<?php echo $type?>/<?php echo $target_id?>/<?php echo $jsnsocial_setting['save_original_image']?>"
        },
        text: {
            uploadButton: '<?php echo __('Select Files')?>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            sizeLimit: 10 * 1024 * 1024
        }
    }).on('complete', function(event, id, fileName, response) {
        newPhotos.push( response.photo_id );
        jQuery('#new_photos').val( newPhotos.join(',') )
        jQuery('#nextStep').show();
        
        
    });
    
    jQuery('#triggerUpload').click(function() {
        jQuery('#photos_upload').fineUploader("uploadStoredFiles");
		return false;
    });
});

function setNewPhotos()
{
	jQuery('#new_photos').val( newPhotos.join(',') );
}
</script>

<form action="<?php echo $this->request->base?>/photos/do_activity/<?php echo $type?>" method="post">
<div id="photos_upload"></div>
<a href="#" class="button button-primary" id="triggerUpload"><?php echo __('Upload Queued Files')?></a>
<input type="hidden" name="new_photos" id="new_photos">
<input type="hidden" name="target_id" value="<?php echo $target_id?>">
<input type="submit" class="button button-action" id="nextStep" value="<?php echo __('Save Photos')?>" style="display:none">
</form>
<?php
endif;
?>