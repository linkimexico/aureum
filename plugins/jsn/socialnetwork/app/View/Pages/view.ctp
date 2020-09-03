<div class="page-padding">
    <h1><?php echo $page['Page']['title']?></h1>
    <?php echo $page['Page']['content']?>
    
    <?php if ( $params['comments'] ): ?>
    <h2><?php echo __('Comments')?></h2>
	<ul class="list6 comment_wrapper" id="comments">
	<?php echo $this->element('comments');?>
	</ul>
	<div>
		<?php echo $this->element( 'comment_form', array( 'target_id' => $page['Page']['id'], 'type' => APP_PAGE ) ); ?>
	</div>
	<?php endif; ?>
</div>