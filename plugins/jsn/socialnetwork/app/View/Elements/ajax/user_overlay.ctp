<?php if ( $page == 1 ): ?>
<ul class="list1 users_list" id="list-content2">
<?php endif; ?>

<?php echo $this->element('lists/users_list_bit'); ?>

<?php if (count($users) >= RESULTS_LIMIT):?>
	<div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url?>', 'list-content2', this)"><?php echo __('Load More')?></a>
    </div>
<?php endif; ?>

<?php if ( $page == 1 ): ?>
</ul>
<?php endif; ?>