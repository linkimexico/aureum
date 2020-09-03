<a href="<?php echo $this->request->base?>/blogs/create" class="topButton button button-action"><?php echo __('Write New Entry')?></a>
<h1><?php echo __('My Blog')?></h1>	
<ul class="list6 comment_wrapper" id="list-content">
	<?php echo $this->element( 'lists/blogs_list', array('user_blog' => true) ); ?>
</ul> 