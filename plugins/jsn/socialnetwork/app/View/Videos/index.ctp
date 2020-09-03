<div id="leftnav">
	<div class="box2 box_style1 menu">
		<h3><?php echo __('Browse')?></h3>
		<ul class="list2" id="browse">
			<li <?php if ( empty( $this->request->named['category_id'] ) ): ?>class="current"<?php endif; ?> id="browse_all"><a data-url="<?php echo $this->request->base?>/videos/ajax_browse/all" href="<?php echo $this->request->base?>/videos"><?php echo __('All Videos')?></a></li>
			<li><a data-url="<?php echo $this->request->base?>/videos/ajax_browse/my" href="#"><?php echo __('My Videos')?></a></li>
			<li><a data-url="<?php echo $this->request->base?>/videos/ajax_browse/friends" href="#"><?php echo __("Friends' Videos")?></a></li>
		    <?php echo $this->element('lists/categories_list', array('controller' => 'videos'))?>
		</ul>
		<div id="filters" style="margin-top:5px">
			<?php echo $this->Form->text( 'keyword', array( 'placeholder' => __('Enter keyword to search'), 'rel' => 'videos' ) );?>
		</div>
	</div>
	
	<?php echo $this->element('hooks', array('position' => 'videos_sidebar') ); ?> 
	
	<?php echo $this->element('blocks/tags_block');	?>
</div>

<div id="center">
    
    <?php echo $this->element('hooks', array('position' => 'videos_top') ); ?> 
    
	<?php if (!empty($uid)): ?>	
	<a href="<?php echo $this->request->base?>/videos/ajax_create" class="overlay button button-action topButton" title="<?php echo __('Share New Video')?>"><?php echo __('Share New Video')?></a>
	<?php endif; ?>

	<h1><?php echo __('Videos')?></h1>
	<ul class="list4 albums" id="list-content">
		<?php 
		if ( !empty( $this->request->named['category_id'] ) )
			echo $this->element( 'lists/videos_list', array( 'more_url' => '/videos/ajax_browse/category/' . $this->request->named['category_id'] . '/page:2' ) );
		else
			echo $this->element( 'lists/videos_list', array( 'more_url' => '/videos/ajax_browse/all/page:2' ) ); 
		?>		
	</ul>
</div>