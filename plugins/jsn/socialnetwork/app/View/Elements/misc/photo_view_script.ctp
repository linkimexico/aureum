<script>
var targetX, targetY;
var tagCounter = 0;
var tagging  = false;
var page = 2;
var photo_id = <?php echo $photo['Photo']['id']?>;
var photo_thumb = '<?php echo $photo['Photo']['thumb']?>';
var tag_uid = 0;
var loaded = false;
<?php if ( !empty( $this->request->named['uid'] ) ): ?>
tag_uid = <?php echo $this->request->named['uid']?>;
<?php endif; ?>

jQuery('body').addClass('lightbox_mode');

window.onpopstate = function(event) {
    //if ( event.state )
        //displayPhoto(event.state.photo_id);    
};

jQuery(document).ready(function(){
	jQuery("body").keydown(function(e) {
			if(e.which == 39) {
				jQuery('#photo_right_arrow').click();
			}
			else if(e.which == 37) {
				jQuery('#photo_left_arrow').click();
			}
	});
    jQuery('#photo_thumbs').on('mouseenter', function(){
       jQuery('#photo_load_btn').slideDown();
    }) 
   
    jQuery('#photo_thumbs').on('mouseleave', function(){
       jQuery('#photo_load_btn').slideUp();
    }) 
    
    jQuery('#photo_thumbs ul li').click(function(){
        jQuery('#photo_thumbs ul li').removeClass('active');
        jQuery(this).addClass('active');
    });
    
    jQuery('#photo_thumb_' + photo_id).addClass('active');
    jQuery(".sharethis:not(.hideshare-btn)").hideshare({media: '<?php echo FULL_BASE_URL . $this->request->webroot . $photo['Photo']['thumb']?>', linkedin: false});
});

function loadMoreThumbs()
{
    jQuery('#photo_wrapper').spin('large');
    jQuery.post('<?php echo $this->request->base?>/photos/ajax_fetch', {type: '<?php echo $type?>', target_id: <?php echo $target_id?>, page: page }, function(data)
    {
        jQuery('#photo_wrapper').spin(false);
        if ( data != '' )
        {
            page++;
            jQuery('#photo_thumbs ul').append(data);
            jQuery('#photo_load_btn').remove();
        }
    });
}

function showPhotoWrapper()
{
    if ( loaded )
        return;   
        
    loaded = true;
    
    var preload = jQuery('#preload').html();    
    
    if ( preload != '' )
    {
        jQuery('#preload').html('');    
        jQuery('#photo-content').html(preload);        
        
        registerOverlay();    
        jQuery(".sharethis:not(.hideshare-btn)").hideshare({link: document.URL, media: '<?php echo FULL_BASE_URL?>' + jQuery('#photo_src').attr('src'), linkedin: false});
        
        if ( tagging )
            tagPhoto();
    }
    
    jQuery('#photo_wrapper').fadeIn();      
}

function showPhoto(id)
{
    photo_id = id;
    jQuery('#photo_wrapper').spin('large');
    var url = '';
    loaded = false;
    
    if ( tag_uid )
        url = id + '/uid:' + tag_uid;
    else
        url = id;
    
    jQuery('#preload').load( '<?php echo $this->request->base?>/photos/ajax_view/' + url, {noCache: 1}, function(){
        jQuery('#photo_thumbs .active').removeClass('active');
        jQuery('#photo_thumb_' + id).addClass('active');
    });
    
    window.history.pushState({photo_id: photo_id},"", '<?php echo $this->request->base?>/photos/view/' + url + '#content' );    
}

function submitTag( uid, tagValue ) 
{
    if ( uid != '' || jQuery("#tag-name").val() != '' )
    {
        var style = 'left:' + targetX + '%; top:' + targetY + '%';
        jQuery('#photo_wrapper').spin('large');
        jQuery.post( '<?php echo $this->request->base?>/photos/ajax_tag', {photo_id: photo_id, uid: uid, value: jQuery("#tag-name").val(), style: style}, function( data ){
            jQuery('#photo_wrapper').spin(false);
            var json = jQuery.parseJSON(data);
            
            if ( json.result == 1 )
            {
                if ( uid )
                    tagValue = '<a href="<?php echo $this->request->base?>/users/view/' + uid + '">' + tagValue + '</a>';       
                else
                    tagValue = jQuery("#tag-name").val();
                
                jQuery("#tags").append('<span id="hotspot-item-' + tagCounter + '" onmouseover="showTag(' + tagCounter + ')" onmouseout="hideTag(' + tagCounter + ')">' + tagValue + '<a href="javascript:void(0)" onclick="removeTag(' + tagCounter + ', ' + json.id + ')"><i class="icon-remove cross-icon-sm"></i></a></span>');
                jQuery("#tag-wrapper").append('<div id="hotspot-' + tagCounter + '" class="hotspot" style="' + style + '"><span>' + tagValue + '</span></div>');
        
                //Adds a new hotspot to image
                closeTagInput();
                tagCounter++;
            }
            else
                jsnsocialAlert(json.message);
        });
    }
}
function closeTagInput() {
    jQuery("#tag-target").fadeOut();
    jQuery("#tag-input").fadeOut();
    jQuery("#tag-name").val("");
}
function removeTag(i, tag_id) {
    jQuery("#hotspot-item-"+i).fadeOut();
    jQuery("#hotspot-"+i).fadeOut();
    jQuery.post( '<?php echo $this->request->base?>/photos/ajax_remove_tag', {tag_id: tag_id} );
}
function showTag(i) {
    jQuery("#hotspot-"+i).addClass("hotspothover");
}
function hideTag(i) {
    jQuery("#hotspot-"+i).removeClass("hotspothover");
}
function tagPhoto() {
    tagging = true;
    jQuery("#tag-wrapper img").css('cursor', 'crosshair');
    jQuery("#tagPhoto").html('<a href="javascript:void(0)" onclick="doneTagging()"><?php echo __('Done Tagging')?></a>');
    
    jQuery("#tag-wrapper img").click(function(e){   
    	if ( tagging )
    	{ 
	        //Determine area within element that mouse was clicked
	        mouseX = e.pageX - jQuery("#tag-wrapper").offset().left;
	        mouseY = e.pageY - jQuery("#tag-wrapper").offset().top;
	        
	        //Get height and width of #tag-target
	        targetWidth = jQuery("#tag-target").outerWidth();
	        targetHeight = jQuery("#tag-target").outerHeight();
	        
	        //Determine position for #tag-target
	        targetX = mouseX-targetWidth/2;
	        targetY = mouseY-targetHeight/2;
	        
	        //Determine position for #tag-input
	        inputX = mouseX+targetWidth/2;
	        inputY = mouseY-targetHeight/2;
	        
	        //Animate if second click, else position and fade in for first click
	        if(jQuery("#tag-target").css("display")=="block")
	        {
	            jQuery("#tag-target").animate({left: targetX, top: targetY}, 500);
	            jQuery("#tag-input").animate({left: inputX, top: inputY}, 500);
	        } else {
	            jQuery("#tag-target").css({left: targetX, top: targetY}).fadeIn();
	            jQuery("#tag-input").css({left: inputX, top: inputY}).fadeIn();
	        }
	        
	        //Give input focus
	        jQuery("#tag-name").focus(); 
	        
	        jQuery("#friends_list").html( jQuery("#friends").html() );
	
			targetX=mouseX*100/jQuery("#tag-wrapper").outerWidth();
			targetY=mouseY*100/jQuery("#tag-wrapper").outerHeight();
	    }
    });
    
    //If cancel button is clicked
    jQuery('#tag-cancel').click(function(){
        closeTagInput();
        return false;
    });
    
    //If enter button is clicked within #tag-input
    jQuery("#tag-name").keyup(function(e) {
        if(e.keyCode == 13) submitTag(0, '');
    }); 
    
    //If submit button is clicked
    jQuery('#tag-submit').click(function(){
        submitTag(0, '');
        return false;
    });
}
function doneTagging() {
    tagging = false;
    jQuery("#tag-wrapper img").css('cursor', 'default');
    jQuery("#tagPhoto").html('<a href="javascript:void(0)" onclick="tagPhoto()"><?php echo __('Tag Photo')?></a>');
    //jQuery('#tag-wrapper img').unbind();
    jQuery('#tag-name').unbind();
    jQuery('#tag-submit').unbind();
    closeTagInput();
}

function deletePhoto()
{
    jQuery.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',
        model: 'confirm',
        callback: function(){
            jQuery.post('<?php echo $this->request->base?>/photos/ajax_remove', {photo_id: photo_id}, function() {
            	jQuery.fn.SimpleModal({btn_ok: '<?php echo addslashes(__('OK'))?>', title: '<?php echo addslashes(__('Done!'))?>', hideFooter: false, closeButton: false, model: 'alert', contents: '<?php echo addslashes(__('Photo has been marked for deletion'))?>'}).showModal();
	        });
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: "<?php echo addslashes(__('Are you sure you want to delete this photo?'))?>",
        hideFooter: false, 
        closeButton: false
    }).showModal();
}

</script>
