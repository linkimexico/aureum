<script>
jQuery(document).ready(function()
{       
    jQuery('#cover-img').Jcrop({
        aspectRatio: 3.5,
        onSelect: storeCoords,
        minSize: [ 800, 210 ],
        boxWidth: jQuery('#cover_wrapper').width()
    }, function(){
        jcrop_api = this;
		jcrop_api.setImage(jQuery('#cover-img').attr('src'));
    });
    
    jQuery('#select-1').fineUploader({
        request: {
            endpoint: "<?php echo $this->request->base?>/upload/cover"
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
        
        jQuery('#cover').css('background-image', 'url(' + response.cover + ')');
        jcrop_api.setImage( response.photo );
    });
});
</script>

<div id="cover_wrapper">
    <?php if ( !empty( $photo['Photo']['path'] ) ): ?>        
    <img src="<?php echo $this->request->webroot?><?php echo $photo['Photo']['path']?>"  id="cover-img">
    <?php else: ?>
    <img src="<?php echo $this->request->webroot?>theme/<?php echo $this->theme?>/img/cover.jpg"  id="cover-img">
    <?php endif; ?>
</div>

<div id="select-1" style="margin-top: 10px;text-align: center;"></div>
