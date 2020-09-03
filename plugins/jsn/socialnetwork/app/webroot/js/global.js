/*if (jQuery.cookie('fullsite') && jQuery(window).width() <= 768) 
	window.location = baseUrl + '/home/do_fullsite';
else if ( screen.width <= 768 && jQuery(window).width() > 768 && !jQuery.cookie('fullsite') )
	window.location = baseUrl + '/home/do_theme/mobile';*/

jQuery(document).ready(function(){	
	jQuery('.jsn_social textarea:not(.no-grow)').elastic();
	jQuery('input, textarea').placeholder();
	jQuery(".tip").tipsy({ html: true, gravity: 's' });
	jQuery('.truncate').each(function(){
		if ( parseInt(jQuery(this).css('height')) >= 145 )
			jQuery(this).after('<a href="javascript:void(0)" onclick="showMore(this)" class="show-more">' + jQuery(this).data('more-text') + '</a>');
	});
	  
	jQuery('#loginButton').on('click', function(){
		jQuery('#loginForm').toggle();
		return false;
	});

	registerOverlay();
	
	jQuery('#browse a:not(.overlay):not(.no-ajax)').click(function(){		
		jQuery(this).children('.badge_counter').hide();
		jQuery(this).spin('tiny');
		
		jQuery('#browse .current').removeClass('current');
		jQuery(this).parent().addClass('current');
		
		var div = jQuery(this).attr('rel');
		if ( div == undefined )
			div = 'list-content';
		
		var el = jQuery(this);
		jQuery('#' + div).load( jQuery(this).attr('data-url'), {noCache: 1}, function(){
			//jQuery('#browseLoading').remove();
			el.children('.badge_counter').fadeIn();
			el.spin(false);
			
			// reattach events
			jQuery('textarea:not(.no-grow)').elastic();			
			jQuery(".tip").tipsy({ html: true, gravity: 's' });			
			//boxy.hide();
			registerOverlay();			
			jQuery('.truncate').each(function(){
				if ( parseInt(jQuery(this).css('height')) >= 145 )
					jQuery(this).after('<a href="javascript:void(0)" onclick="showMore(this)" class="show-more">' + jQuery(this).data('more-text') + '</a>');
			});
			
			window.history.pushState({},"", el.attr('href'));
		});
		
		return false;
	});
	
	jQuery('#keyword').keyup(function(event) {
		if (event.keyCode == '13') {
			jQuery('#browse_all').spin('tiny');
			jQuery('#browse .current').removeClass('current');
			jQuery('#browse_all').addClass('current');
			
			jQuery('#list-content').load( baseUrl + '/' + jQuery(this).attr('rel') + '/ajax_browse/search/' + encodeURI( jQuery(this).val() ), {noCache: 1}, function(){
				jQuery('#browse_all').spin(false);
				jQuery('#keyword').val('');
			});
		}
	});
	
	jQuery('#global-search').keyup(function(event) {
        if (event.keyCode == '13') {
            window.location = baseUrl + '/search/index/' + jQuery(this).val() + '/';
        }
    });
    
    jQuery('#global-search-filters a:not(.no-ajax)').click(function(){       
        jQuery(this).spin('tiny');
        jQuery('#global-search-filters .current').removeClass('current');
        jQuery(this).parent().addClass('current');
        
        switch ( jQuery(this).attr('id') )
        {
            case 'filter-blogs':
            case 'filter-groups':
            case 'filter-topics':
                jQuery('#search-content').html('<ul class="list6 comment_wrapper" id="list-content">Loading...</ul>');
                break;
                
            case 'filter-albums':
            case 'filter-videos':
                jQuery('#search-content').html('<ul class="list4 albums" id="list-content">Loading...</ul>');
                break;
                
            case 'filter-users':
                jQuery('#search-content').html('<ul class="list1 users_list" id="list-content">Loading...</ul>');
                break;
        }
        
        var obj = jQuery(this);
        jQuery('#list-content').load( encodeURI( jQuery(this).attr('data-url') ), {noCache: 1}, function(){
            obj.spin(false);    
        });
        return false;
    });
});

var sModal;

function registerOverlay()
{
	jQuery('.overlay').unbind('click');
	jQuery('.overlay').click(function()
	{
		overlay_title = jQuery(this).attr('title');
		overlay_url = jQuery(this).attr('href');
		overlay_div = jQuery(this).attr('rel');

		if (overlay_div)
		{
			sModal = jQuery.fn.SimpleModal({
		        model: 'modal',
		        title: overlay_title,
		        contents: jQuery('#' + overlay_div).html()
		   });
		}
		else
		{
			sModal = jQuery.fn.SimpleModal({
		        width: 600,
		        model: 'modal-ajax',
		        title: overlay_title,
		        offsetTop: 100,
		        param: {
		            url: overlay_url,
		            onRequestComplete: function() {
		            	jQuery(".tip").tipsy({ html: true, gravity: 's' });
		            },
		            onRequestFailure: function() { }
		        }
		    });
		}
		
		sModal.showModal();

		return false;
	});
}

function registerImageOverlay()
{
	jQuery('.attached-image').magnificPopup({
        type:'image',
        gallery: { enabled: true },
        zoom: { 
            enabled: true, 
            opener: function(openerElement) {
              return openerElement.parent();
            }
        }
    });
}

function submitComment(activity_id)
{
	if (jQuery.trim(jQuery("#commentForm_"+activity_id).val()) != '')
	{
		jQuery('#commentButton_' + activity_id + ' a').addClass('disabled');
		jQuery('#commentButton_' + activity_id + ' a i').addClass('icon-refresh icon-spin').removeClass('icon-comment-alt');
		jQuery.post(baseUrl + "/activities/ajax_comment", {activity_id: activity_id, comment: jQuery("#commentForm_"+activity_id).val()}, function(data){
			if (data != '')
				showPostedComment(activity_id, data);
		});
	}
}

function submitItemComment(item_type, item_id, activity_id)
{
	if (jQuery.trim(jQuery("#commentForm_"+activity_id).val()) != '')
	{
		jQuery('#commentButton_' + activity_id + ' a i').addClass('icon-refresh icon-spin').removeClass('icon-comment-alt');
		jQuery('#commentButton_' + activity_id + ' a').addClass('disabled');
		jQuery.post(baseUrl + "/comments/ajax_share", {type: item_type, target_id: item_id, message: jQuery("#commentForm_"+activity_id).val(), activity: 1}, function(data){
			if (data != '')
				showPostedComment(activity_id, data);
		});
	}
}

function showPostedComment(activity_id, data)
{
	jQuery('#newComment_'+activity_id).before(data);
	jQuery('.slide').slideDown();
	jQuery('#commentButton_' + activity_id + ' a').removeClass('disabled');
	jQuery('#commentButton_' + activity_id + ' a i').removeClass('icon-refresh icon-spin').addClass('icon-comment-alt');
	jQuery("#commentForm_"+activity_id).val('');
	jQuery("#commentButton_"+activity_id).hide();
	registerCrossIcons();				
	jQuery('.commentBox').css('height', '27px');
}

function showCommentButton(activity_id)
{
	jQuery("#commentButton_"+activity_id).fadeIn();
}

function showCommentForm(activity_id)
{
	jQuery("#comments_"+activity_id).slideDown();
	jQuery("#newComment_"+activity_id).slideDown();
}

function postWall()
{
	var msg = jQuery('#message').val();
	if (jQuery.trim(msg) != '')
	{
		disableButton('status_btn');
		jQuery.post(baseUrl + "/activities/ajax_share", jQuery("#wallForm").serialize(), function(data){
			enableButton('status_btn');
			jQuery('#message').val("");
			if (data != '')
			{
				jQuery('#list-content').prepend(data);			
				registerCrossIcons();
				jQuery('#message').css('height', '25px');
				jQuery('#wall_photo_preview').fadeOut(function(){
					jQuery('.slide').slideDown();
					jQuery('#wall_photo_preview').html('');
					jQuery('#wall_photo_id').val('');
					jQuery('#wall_photo_preview').show();
				});
			}
		});
	}
}

function postComment()
{
	if (jQuery.trim(jQuery('#message').val()) != '')
	{
		jQuery('#shareButton').addClass('disabled');
		jQuery('#shareButton i').addClass('icon-refresh icon-spin').removeClass('icon-comment-alt');
		jQuery.post(baseUrl + "/comments/ajax_share", jQuery("#commentForm").serialize(), function(data){
			jQuery('#shareButton').removeClass('disabled');
			jQuery('#shareButton i').removeClass('icon-refresh icon-spin').addClass('icon-comment-alt');
			
			jQuery('#message').val("");
			if (data != '')
			{
				jQuery('#comments').append(data);
				jQuery('.slide').slideDown();	
				jQuery('#message').css('height', '37px');
				jQuery("#comment_count").html( parseInt(jQuery("#comment_count").html()) + 1 );
								
				jQuery("#comments li").hover(
					function () {
					jQuery(this).contents('.cross-icon').show();
				  }, 
				  function () {
					jQuery(this).contents('.cross-icon').hide();
				  }
				);
			}
		});
	}
}

function createItem( type )
{
	disableButton('createButton');	
	jQuery.post(baseUrl + "/" + type + "/ajax_save", jQuery("#createForm").serialize(), function(data){
		enableButton('createButton');
		var json = jQuery.parseJSON(data);
            
        if ( json.result == 1 )
        	window.location = baseUrl + '/' + type + '/view/' + json.id;
        else
        {
        	jQuery(".error-message").show();
			jQuery(".error-message").html(json.message);
        }		
	});
} 

function moreResults(url, div, obj)
{	
	jQuery(obj).spin('small');
	jQuery(obj).css('color', 'transparent');
	jQuery.post(baseUrl + url, function(data){
		jQuery(obj).spin(false);
		jQuery('#' + div + ' .view-more').remove();
		if ( div == 'comments' )
			jQuery("#" + div).prepend(data);
		else
			jQuery("#" + div).append(data);
			
		registerOverlay();
	});
}

function jsnsocialAlert(msg)
{
	jQuery.fn.SimpleModal({btn_ok: 'OK', title: 'Message', hideFooter: false, closeButton: false, model: 'alert', contents: msg}).showModal();
}

function jsnsocialConfirm( msg, url )
{
	jQuery.fn.SimpleModal({
        btn_ok: 'OK',
        model: 'confirm',
        callback: function(){
            window.location = url;
        },
        title: 'Please Confirm',
        contents: msg,
        hideFooter: false, 
        closeButton: false
    }).showModal();
}

function toggleCheckboxes(obj)
{
	if ( obj.checked )
		jQuery('.check').attr('checked', 'checked');
	else
		jQuery('.check').attr('checked', false);
}

function confirmSubmitForm(msg, form_id)
{
	jQuery.fn.SimpleModal({
        btn_ok: 'OK',
        model: 'confirm',
        callback: function(){
            document.getElementById(form_id).submit();		
        },
        title: 'Please Confirm',
        contents: msg,
        hideFooter: false, 
        closeButton: false
    }).showModal();
}

function registerCrossIcons()
{
	jQuery("#list-content li").hover(
		function () {
		jQuery(this).contents('.cross-icon').show();
	  }, 
	  function () {
		jQuery(this).contents('.cross-icon').hide();
	  }
	);
}

function likeIt( type, item_id, thumb_up )
{
	jQuery.post(baseUrl + '/likes/ajax_add/' + type + '/' + item_id + '/' + thumb_up, { noCache: 1 }, function(data){
	    try
	    {
    	    var res = jQuery.parseJSON(data);
    	    
            jQuery('#like_count').html( parseInt(res.like_count) );
            jQuery('#dislike_count').html( parseInt(res.dislike_count) );  
            jQuery('#like_count2').html( parseInt(res.like_count) );       
            
            if ( thumb_up )
            {
                jQuery('#like_count').parent().toggleClass('active');
                jQuery('#dislike_count').parent().removeClass('active');
            }
            else
            {
                jQuery('#dislike_count').parent().toggleClass('active');
                jQuery('#like_count').parent().removeClass('active');
            }
        } 
        catch (err)
        {
            alert(data);
        }
	});
}

function likePhoto( item_id, thumb_up )
{ 
    jQuery.post(baseUrl + '/likes/ajax_add/photo/' + item_id + '/' + thumb_up, { noCache: 1 }, function(data){
        try
        {
            var res = jQuery.parseJSON(data);
            
            jQuery('#photo_like_count2').html( parseInt(res.like_count) );
            jQuery('#photo_dislike_count2').html( parseInt(res.dislike_count) );        
            
            if ( thumb_up )
            {
                jQuery('#photo_like_count').toggleClass('active');
                jQuery('#photo_dislike_count').removeClass('active');
            }
            else
            {
                jQuery('#photo_dislike_count').toggleClass('active');
                jQuery('#photo_like_count').removeClass('active');
            }
        } 
        catch (err)
        {
            alert(data);
        }
    });
}

function likeActivity(type, id, thumb_up)
{
	jQuery.post(baseUrl + '/likes/ajax_add/' + type + '/' + id + '/' + thumb_up, { noCache: 1 }, function(data){
	    try
	    {
    		var res = jQuery.parseJSON(data);
            jQuery('#' + type + '_like_' + id).html( parseInt(res.like_count) );
            jQuery('#' + type + '_dislike_' + id).html( parseInt(res.dislike_count) );
            
            if ( thumb_up )
            {
                jQuery('#' + type + '_l_' + id).toggleClass('active');
                jQuery('#' + type + '_d_' + id).removeClass('active');
            }
            else
            {
                jQuery('#' + type + '_d_' + id).toggleClass('active');
                jQuery('#' + type + '_l_' + id).removeClass('active');
            }
        } 
        catch (err)
        {
            alert(data);
        }
	});
}

function showFeedVideo( source, source_id, activity_id )
{
	jQuery('#video_teaser_' + activity_id + ' .vid_thumb').spin('small');
	jQuery('#video_teaser_' + activity_id).load(baseUrl + '/videos/ajax_embed', { source: source, source_id: source_id }, function(){
		jQuery('#video_teaser_' + activity_id + ' > .vid_thumb').spin(false);
	});
}

function showAllComments( activity_id )
{
	jQuery('#comments_' + activity_id + ' .hidden').fadeIn();
	jQuery('#all_comments_' + activity_id).hide();
}


function toggleMenu(menu)
{
    if ( menu == 'leftnav' )
    {
        if ( jQuery('#leftnav').css('left') == '-200px' )
        {
            jQuery('#leftnav').animate({left:0}, 300);
            jQuery('#right').animate({right:-204}, 300);
            jQuery('#center').animate({left:200}, 300);
        }
        else
        {
            jQuery('#leftnav').animate({left:-200}, 300);
            jQuery('#center').animate({left:0}, 300);
        }
    }
    else
    {
        if ( jQuery('#right').css('right') == '-204px' )
        {
            jQuery('#right').show();
            jQuery('#right').animate({right:0}, 300);
            jQuery('#leftnav').animate({left:-200}, 300);
            jQuery('#center').animate({left:0}, 300);
        }
        else
        {
            jQuery('#right').animate({right:-204}, 300, function(){
            	jQuery('#right').hide();
            });            
            //jQuery('#center').animate({left:0}, 300);
        }
    }
}

function globalSearchMore( filter )
{
    jQuery('#filter-' + filter).trigger('click');
}

function showJsnsocialDropdown(obj)
{
    jQuery(obj).next().toggle();
}

function doModeration( action, type )
{
    switch ( action )
    {
        case 'delete':
            jQuery('#deleteForm').attr('action', baseUrl + '/admin/' + type + '/delete');
            confirmSubmitForm('Are you sure you want to delete these ' + type + '?', 'deleteForm'); 
        break;
        
        case 'move':
            jQuery('#deleteForm').attr('action', baseUrl + '/admin/' + type + '/move');
            jQuery('#category_id').show();
        break;
        
        default:
            jQuery('#category_id').hide();
    }
}

var tmp_class;
function disableButton(button)
{
	tmp_class = jQuery("#" + button + " i").attr("class");
	jQuery("#" + button + " i").attr("class", "icon-refresh icon-spin");
	jQuery("#" + button).addClass('disabled');
}

function enableButton(button)
{
	jQuery("#" + button + " i").attr("class", tmp_class);
	jQuery("#" + button).removeClass('disabled');
}

function initTabs(tab)
{
	jQuery('#' + tab + ' .tabs > li').click(function(){
        jQuery('#' + tab + ' li').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('#' + tab + ' .tab').hide();
        jQuery('#'+jQuery(this).attr('id')+'_content').show();
    });
}

function showMore(obj)
{
	jQuery(obj).prev().css('max-height', 'none');
	jQuery(obj).replaceWith('<a href="javascript:void(0)" onclick="showLess(this)" class="show-more">' + jQuery(obj).prev().data('less-text') + '</a>');
}

function showLess(obj)
{
	jQuery(obj).prev().css('max-height', '');
	jQuery(obj).replaceWith('<a href="javascript:void(0)" onclick="showMore(this)" class="show-more">' + jQuery(obj).prev().data('more-text') + '</a>');
}

/*function viewFullSite()
{
	jQuery.cookie('fullsite', 1, { expires: 7 });
	location.reload();
}

function viewMobileSite()
{
	jQuery.removeCookie('fullsite');
	location.reload();
}*/
