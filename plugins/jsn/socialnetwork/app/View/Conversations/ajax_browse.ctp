<a href="<?php echo $this->request->base?>/conversations/ajax_send" title="<?php echo __('Send New Message')?>" class="overlay topButton button button-action"><?php echo __('Send New Message')?></a>
<h1><?php echo __('Conversations')?></h1>
<ul class="list6 comment_wrapper" id="list-content">
<?php echo $this->element( 'lists/messages_list', array( 'more_url' => '/conversations/ajax_browse/page:2' ) ); ?>
</ul>