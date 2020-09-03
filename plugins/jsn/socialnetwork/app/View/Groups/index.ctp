<div id="leftnav">
	<div class="box2 box_style1 menu">
		<h3><?php echo __('Browse')?></h3>
		<ul class="list2" id="browse">
			<li class="current" id="browse_all"><a data-url="<?php echo $this->request->base?>/groups/ajax_browse/all" href="<?php echo $this->request->base?>/groups"><?php echo __('All Groups')?></a></li>
			<li><a data-url="<?php echo $this->request->base?>/groups/ajax_browse/my" href="#"><?php echo __('My Groups')?></a></li>			
			<li><a data-url="<?php echo $this->request->base?>/groups/ajax_browse/friends" href="#"><?php echo __("Friends' Groups")?></a></li>
			<?php echo $this->element('lists/categories_list', array('controller' => 'groups'))?>
		</ul>
		<div id="filters" style="margin-top:5px">
			<?php echo $this->Form->text( 'keyword', array( 'placeholder' => __('Enter keyword to search'), 'rel' => 'groups' ) );?>
		</div>
	</div>	
	
	<?php echo $this->element('hooks', array('position' => 'groups_sidebar') ); ?> 
</div>

<div id="center">	
    <?php echo $this->element('hooks', array('position' => 'groups_top') ); ?> 
    
	<?php if (!empty($uid)): ?>
	<a href="<?php echo $this->request->base?>/groups/create" class="button button-action topButton"><?php echo __('Create New Group')?></a>
	<?php endif; ?>

	<h1><?php echo __('Groups')?></h1>
	<ul class="list6 comment_wrapper" id="list-content">
		<?php echo $this->element( 'lists/groups_list', array( 'more_url' => '/groups/ajax_browse/all/page:2' ) ); ?>
	</ul>
</div>