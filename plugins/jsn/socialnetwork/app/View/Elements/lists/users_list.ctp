<?php if ( isset($type) && $type == 'home' ): ?>
<script>
jQuery(document).ready(function(){

	jQuery("#list-content li").hover(
		function () {
		jQuery(this).contents().find('.delete-icon').show();
	  }, 
	  function () {
		jQuery(this).contents().find('.delete-icon').hide();
	  }
	);
});
</script>
<?php endif; ?>

<?php
echo $this->element('lists/users_list_bit');
?>

<?php if (count($users) >= RESULTS_LIMIT):?>
    <div class="view-more">
    <?php if ( !empty($type) && $type == 'search' ): ?>
    <a href="javascript:void(0)" onclick="moreUserSearchResults('<?php echo $more_url?>', 'list-content', this)">
    <?php else: ?>
	<a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url?>', 'list-content', this)">
	<?php endif; ?>
	<?php echo __('Load More')?>
	</a>
	</div>
<?php endif; ?>