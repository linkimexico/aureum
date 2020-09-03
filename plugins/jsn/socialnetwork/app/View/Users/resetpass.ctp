<div class="page-padding">
    <h1><?php echo __('Reset Password')?></h1>
    <form action="<?php echo $this->request->base?>/users/resetpass" method="post">	
    <?php echo $this->Form->hidden('code', array('value' => $code)); ?>
    <ul class="list6">
    	<li><label><?php echo __('New Password')?></label><?php echo $this->Form->password('password'); ?></li>			
    	<li><label><?php echo __('Verify Password')?></label><?php echo $this->Form->password('password2'); ?></li>
    	<li><label>&nbsp;</label><?php echo $this->Form->submit(__('Submit'), array('class' => 'button button-action')); ?></li>
    </ul>
    </form>
</div>