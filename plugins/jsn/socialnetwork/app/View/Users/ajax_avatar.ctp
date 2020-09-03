<script>
jQuery(document).ready(function()
{       
    jQuery('#av-img2').Jcrop({
        aspectRatio: 1,
        onSelect: storeCoords,
        minSize: [ 45, 45 ]
    }, function(){
        jcrop_api = this;
    });
    
    jQuery('#select-0').fineUploader({
        request: {
            endpoint: "<?php echo $this->request->base?>/upload/avatar"
        },
        text: {
            uploadButton: '<?php echo __('Upload New Picture')?>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            sizeLimit: 10 * 1024 * 1024
        },
        multiple: false
    }).on('complete', function(event, id, fileName, response) {
        jQuery('#av-img').attr('src', response.avatar);
        jQuery('#av-img2').attr('src', response.avatar);
        jQuery('#member-avatar').attr('src', response.thumb);
        
        jcrop_api.setImage( response.avatar );
    });
});
</script>

<div id="avatar_wrapper" style="display: inline-block;vertical-align: top;margin: 0 10px 10px 0">
    <img src="<?php echo $this->Jsnsocial->getUserPicture($cuser['photo'], false)?>"  id="av-img2">
</div>

<div id="select-0" style="display: inline-block; width: 300px;"></div>
