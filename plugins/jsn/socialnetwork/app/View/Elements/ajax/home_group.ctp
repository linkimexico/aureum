<a href="<?php echo $this->request->base?>/groups/create" class="topButton button button-action"><?php echo __('Create New Group')?></a>
<h1><?php echo __('My Groups')?></h1>	
<ul class="list6 comment_wrapper" id="list-content">
	<?php echo $this->element( 'lists/groups_list' ); ?>
</ul>