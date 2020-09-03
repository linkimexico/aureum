<?php if ( !empty( $is_member ) ): ?> 
<a href="javascript:void(0)" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_group_create')" class="topButton button button-action" style="margin-top: -40px"><?php echo __('Create New Topic')?></a>    
<?php endif; ?>
<ul class="list6 comment_wrapper" id="list-content">
	<?php echo $this->element( 'lists/topics_list' ); ?>
</ul>