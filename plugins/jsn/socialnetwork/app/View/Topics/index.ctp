<div id="leftnav">
	<div class="box2 box_style1 menu">
		<h3><?php echo __('Browse')?></h3>
		<ul class="list2" id="browse">
			<li <?php if ( empty( $this->request->named['category_id'] ) ): ?>class="current"<?php endif; ?> id="browse_all"><a data-url="<?php echo $this->request->base?>/topics/ajax_browse/all" href="<?php echo $this->request->base?>/topics"><?php echo __('All Categories')?></a></li>
			<li id="my_topics"><a data-url="<?php echo $this->request->base?>/topics/ajax_browse/my" href="#"><?php echo __('My Topics')?></a></li>
			<li id="friend_topics"><a data-url="<?php echo $this->request->base?>/topics/ajax_browse/friends" href="#"><?php echo __("Friends' Topics")?></a></li>
		    <?php echo $this->element('lists/categories_list', array('controller' => 'topics'))?>
		</ul>
		<div id="filters" style="margin-top:5px">
			<?php echo $this->Form->text( 'keyword', array( 'placeholder' => __('Enter keyword to search'), 'rel' => 'topics' ) );?>
		</div>
	</div>
	
	<?php echo $this->element('hooks', array('position' => 'topics_sidebar') ); ?> 
	
	<?php echo $this->element('blocks/tags_block'); ?>
</div>

<div id="center">	
    
    <?php echo $this->element('hooks', array('position' => 'topics_top') ); ?> 
    
	<?php 
	if (!empty($uid)):
	?>
	<a href="<?php echo $this->request->base?>/topics/create" class="button button-action topButton"><?php echo __('Create New Topic')?></a>
	<?php
	endif;
	?>

	<h1><?php echo __('Topics')?></h1>
	<ul class="list6 comment_wrapper" id="list-content">
		<?php 
		if ( !empty( $this->request->named['category_id'] ) )
			echo $this->element( 'lists/topics_list', array( 'more_url' => '/topics/ajax_browse/category/' . $this->request->named['category_id'] . '/page:2' ) );
		else
			echo $this->element( 'lists/topics_list', array( 'more_url' => '/topics/ajax_browse/all/page:2' ) ); 
		?>
	</ul>		
</div>