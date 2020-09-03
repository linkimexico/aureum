<?php if ($user_id == $uid): ?>
<a href="<?php echo $this->request->base?>/topics/create" class="topButton button button-action" style="margin-top: -255px"><?php echo __('Create New Topic')?></a>    
<?php endif; ?>

<ul class="list6 comment_wrapper" id="list-content">
	<?php echo $this->element('lists/topics_list'); ?>
</ul>