<?php 
echo $this->Html->css(array('jquery.mp'), null, array('inline' => false));
echo $this->Html->script(array('jquery.mp.min'), array('inline' => false)); 
?>

<div id="leftnav">
	<div class="box2 box_style1 menu">
		<h3><?php echo __('Browse')?></h3>
		<ul class="list2" id="browse">
			<li class="current" id="browse_all"><a data-url="<?php echo $this->request->base?>/blogs/ajax_browse/all" href="<?php echo $this->request->base?>/blogs"><?php echo __('All Entries')?></a></li>
			<li><a data-url="<?php echo $this->request->base?>/blogs/ajax_browse/my" href="#"><?php echo __('My Entries')?></a></li>
			<li><a data-url="<?php echo $this->request->base?>/blogs/ajax_browse/friends" href="#"><?php echo __("Friends' Entries")?></a></li>
		</ul>
		<div id="filters" style="margin-top:5px">
			<?php echo $this->Form->text( 'keyword', array( 'placeholder' => __('Enter keyword to search'), 'rel' => 'blogs' ) );?>
		</div>
	</div>
	
	<?php echo $this->element('hooks', array('position' => 'blogs_sidebar') ); ?> 
	
	<?php echo $this->element('blocks/tags_block'); ?>
</div>

<div id="center">
	<?php echo $this->element('hooks', array('position' => 'blogs_top') ); ?>	
	<?php if (!empty($uid)): ?>
	<a href="<?php echo $this->request->base?>/blogs/create" class="button button-action topButton"><?php echo __('Write New Entry')?></a>
	<?php endif; ?>
        
    <?php echo $this->element('hooks', array('position' => 'blog_top') ); ?>
        
	<h1><?php echo __('Blogs')?></h1>
	<ul class="list6 comment_wrapper" id="list-content">
		<?php echo $this->element( 'lists/blogs_list', array( 'more_url' => '/blogs/ajax_browse/all/page:2' ) ); ?>
	</ul>
</div>