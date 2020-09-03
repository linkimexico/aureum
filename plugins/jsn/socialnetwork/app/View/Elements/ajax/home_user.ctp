<script>
function removeFriend(id)
{
	jQuery.fn.SimpleModal({
        btn_ok: 'OK',
        model: 'confirm',
        callback: function(){
            jQuery.post('<?php echo $this->request->base?>/friends/ajax_remove', {id: id}, function() {
                jQuery('#friend_'+id).fadeOut(function(){
                    jQuery('#friend_'+id).remove();    
                });
                
                if ( jQuery("#friend_count").html() != '0' )
                    jQuery("#friend_count").html( parseInt(jQuery("#friend_count").html()) - 1 );
            });
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: '<?php echo addslashes(__('Are you sure you want to remove this friend?'))?>',
        hideFooter: false, 
        closeButton: false
    }).showModal();

	return false;
}
</script>
<style>
#list-content li {
	position: relative;
}
</style>

<a href="<?php echo $this->request->base?>/home/index/tab:invite-friends" class="topButton button button-action"><?php echo __('Invite Friends')?></a>
<h1><?php echo __('Friends')?></h1>	
<ul class="list1 users_list" id="list-content">
	<?php echo $this->element( 'lists/users_list' ); ?>
</ul> 