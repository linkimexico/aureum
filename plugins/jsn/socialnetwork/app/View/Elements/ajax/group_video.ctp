<?php if ( !empty( $is_member ) ): ?>
<a href="javascript:void(0)" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_group_create')" class="topButton button button-action" style="margin-top: -40px"><?php echo __('Share New Video')?></a> 
<?php endif; ?>
<ul class="list4 albums" id="list-content">
	<?php echo $this->element( 'lists/videos_list' ); ?>
</ul>