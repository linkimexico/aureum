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
    jQuery('#attachments_upload').fineUploader({
        autoUpload: false,
        request: {
            endpoint: "<?php echo $this->request->base?>/upload/attachments/<?php echo PLUGIN_TOPIC_ID?>"
        },
        text: {
            uploadButton: '<?php echo __('Select Files')?>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png', 'txt', 'zip', 'pdf'],
            sizeLimit: 10 * 1024 * 1024
        }
    }).on('complete', function(event, id, fileName, response) {
    	var attachs = jQuery('#attachments').val();
    	
    	if ( attachs == '' )
        	jQuery('#attachments').val( response.attachment_id );
        else
        	jQuery('#attachments').val( attachs + ',' + response.attachment_id );
    });
    
    jQuery('#triggerUpload').click(function() {
        jQuery('#attachments_upload').fineUploader("uploadStoredFiles");
        return false;
    });
    
    jQuery('.attach_remove').click(function(){
		var obj = jQuery(this);
		jQuery.post('<?php echo $this->request->base?>/attachments/ajax_remove/' + jQuery(this).attr('data-id'), function(data){
			obj.parent().fadeOut();
			var arr = jQuery('#attachments').val().split(',');
			var pos = arr.indexOf(obj.attr('data-id'));
			arr.splice(pos, 1);
			jQuery('#attachments').val(arr.join(','));	
		});
		
		return false;
	});
	
	jQuery('#createButton').click(function(){
        jQuery('#editor').val(tinyMCE.activeEditor.getContent());
        createItem('topics');
        
        return false;
    })
});

function toggleUploader()
{
    jQuery('#images-uploader').slideToggle();
}
</script>