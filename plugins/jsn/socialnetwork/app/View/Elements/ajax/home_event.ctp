<a href="<?php echo $this->request->base?>/events/create" class="topButton button button-action"><?php echo __('Create New Event')?></a>
<h1><?php echo __('Upcoming Events')?></h1>	
<ul class="list6 comment_wrapper" id="list-content">
	<?php echo $this->element( 'lists/events_list' ); ?>
</ul>