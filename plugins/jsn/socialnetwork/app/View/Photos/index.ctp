<div id="leftnav">
	<div class="box2 box_style1 menu">
		<h3><?php echo __('Browse')?></h3>
		<ul class="list2" id="browse">
			<li <?php if ( empty( $this->request->named['category_id'] ) ): ?>class="current"<?php endif; ?> id="browse_all"><a data-url="<?php echo $this->request->base?>/albums/ajax_browse/all" href="<?php echo $this->request->base?>/albums"><?php echo __('All Photos')?></a></li>
			<li><a data-url="<?php echo $this->request->base?>/albums/ajax_browse/my" href="#"><?php echo __('My Photos')?></a></li>
			<li><a data-url="<?php echo $this->request->base?>/albums/ajax_browse/friends" href="#"><?php echo __("Friends' Photos")?></a></li>
			<?php echo $this->element('lists/categories_list', array('controller' => 'albums'))?>
		</ul>
		<div id="filters" style="margin-top:5px">
			<?php echo $this->Form->text( 'keyword', array( 'placeholder' => __('Enter keyword to search'), 'rel' => 'albums' ) );?>
		</div>
	</div>
	
	<?php echo $this->element('hooks', array('position' => 'photos_sidebar') ); ?>
	
	<?php echo $this->element('blocks/tags_block');	?>
</div>

<div id="center">	
    
    <?php echo $this->element('hooks', array('position' => 'photos_top') ); ?>
    
	<?php if (!empty($uid)): ?>
	<a href="<?php echo $this->request->base?>/albums/ajax_create" class="overlay button button-action topButton" title="<?php echo __('Create New Album')?>"><?php echo __('Create New Album')?></a>
	<?php endif; ?>

	<h1><?php echo __('Photos')?></h1>
	<ul class="list4 albums" id="list-content">
		<?php 
		if ( !empty( $this->request->named['category_id'] ) )
			echo $this->element( 'lists/albums_list', array( 'more_url' => '/albums/ajax_browse/category/' . $this->request->named['category_id'] . '/page:2' ) );
		else
			echo $this->element( 'lists/albums_list', array( 'more_url' => '/albums/ajax_browse/all/page:2' ) ); 
		?>	
	</ul>
</div>