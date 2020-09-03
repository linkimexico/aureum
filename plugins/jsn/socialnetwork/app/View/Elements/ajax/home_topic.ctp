<a href="<?php echo $this->request->base?>/topics/create" class="topButton button button-action"><?php echo __('Create New Topic')?></a>
<h1><?php echo __('My Topics')?></h1>	
<ul class="list6 comment_wrapper" id="list-content">
	<?php echo $this->element( 'lists/topics_list' ); ?>
</ul>