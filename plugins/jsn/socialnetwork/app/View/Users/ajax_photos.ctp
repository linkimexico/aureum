<script>
function showAlbums()
{
	jQuery('#user_photos').spin('tiny');
	jQuery('#user_photos').children('.badge_counter').hide();
	jQuery('#profile-content').load( '<?php echo $this->request->base?>/users/ajax_albums/<?php echo $tag_uid?>', {noCache: 1}, function(){
		jQuery('#user_photos').spin(false);
		jQuery('#user_photos').children('.badge_counter').fadeIn();
	});
}
</script>

<?php if ($tag_uid == $uid): ?>
<a href="<?php echo $this->request->base?>/albums/ajax_create" class="topButton button button-action overlay" style="margin-top: -255px" title="<?php echo __('Create New Album')?>"><?php echo __('Create New Album')?></a>
<?php endif; ?>

<div style="float:right"><a href="javascript:void(0)" onclick="showAlbums()"><?php echo __('View all')?></a></div>
<h2 style="margin-top: 0px;"><?php echo __('Photo Albums')?></h2>
<ul class="list4 albums">
<?php echo $this->element('lists/albums_list'); ?>
</ul>

<h2><?php echo __('Tagged Photos')?></h2>
<?php echo $this->element('lists/photos_list', array( 'type' => APP_USER, 'param' => $tag_uid )); ?> 