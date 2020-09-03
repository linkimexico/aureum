<a href="<?php echo $this->request->base?>/videos/ajax_create" class="overlay topButton button button-action" title="<?php echo __('Share New Video')?>"><?php echo __('Share New Video')?></a>
<h1><?php echo __('My Videos')?></h1>	
<ul class="list4 albums" id="list-content">
	<?php echo $this->element( 'lists/videos_list' ); ?>
</ul>