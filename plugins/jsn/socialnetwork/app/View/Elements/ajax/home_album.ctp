<a href="<?php echo $this->request->base?>/albums/ajax_create" class="overlay topButton button button-action" title="<?php echo __('Create New Album')?>"><?php echo __('Create New Album')?></a>
<h1><?php echo __('My Photos')?></h1>	
<ul class="list4 albums" id="list-content">
	<?php echo $this->element( 'lists/albums_list' ); ?>
</ul>