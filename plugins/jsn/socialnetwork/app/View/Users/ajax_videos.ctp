<?php if ($user_id == $uid): ?>
<a href="<?php echo $this->request->base?>/videos/ajax_create" class="overlay topButton button button-action" style="margin-top: -255px" title="<?php echo __('Share New Video')?>"><?php echo __('Share New Video')?></a>   
<?php endif; ?>

<ul class="list4 albums" id="list-content">
<?php echo $this->element('lists/videos_list'); ?>
</ul>