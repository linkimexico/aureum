<?php if ($user_id == $uid): ?>
<a href="<?php echo $this->request->base?>/blogs/create" class="topButton button button-action" style="margin-top: -255px"><?php echo __('Write New Entry')?></a>
<?php endif; ?>

<ul class="list6 comment_wrapper" id="list-content">
<?php echo $this->element('lists/blogs_list', array('user_blog' => true)); ?>
</ul>