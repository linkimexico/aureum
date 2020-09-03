<div id="leftnav">
	<div class="box2 box_style1 menu">
		<h3><?php echo __('Browse')?></h3>
		<ul class="list2" id="browse">
			<li class="current"><a data-url="<?php echo $this->request->base?>/events/ajax_browse/all" href="<?php echo $this->request->base?>/events"><?php echo __('All Events')?></a></li>
			<li><a data-url="<?php echo $this->request->base?>/events/ajax_browse/my" href="#"><?php echo __('My Upcoming Events')?></a></li>
			<li><a data-url="<?php echo $this->request->base?>/events/ajax_browse/friends" href="#"><?php echo __('Friends Attending')?></a></li>
			<li><a data-url="<?php echo $this->request->base?>/events/ajax_browse/past" href="#"><?php echo __('Past Events')?></a></li>
			<?php echo $this->element('lists/categories_list', array('controller' => 'events'))?> 
		</ul>
	</div>
	
	<?php echo $this->element('hooks', array('position' => 'events_sidebar') ); ?> 
</div>

<div id="center">	
    <?php echo $this->element('hooks', array('position' => 'events_top') ); ?> 
    
	<?php if (!empty($uid)): ?>
	<a href="<?php echo $this->request->base?>/events/create" class="button button-action topButton"><?php echo __('Create New Event')?></a>
	<?php endif; ?>

	<h1><?php echo __('Events')?></h1>
	<ul class="list6 comment_wrapper" id="list-content">
		<?php echo $this->element( 'lists/events_list', array( 'more_url' => '/events/ajax_browse/all/page:2' ) ); ?>
	</ul>		
</div>