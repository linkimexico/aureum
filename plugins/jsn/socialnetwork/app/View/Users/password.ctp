<?php echo $this->element('profilenav', array("cmenu" => "password"));?>

<form action="<?php echo $this->request->base?>/users/password" method="post">
<div id="center" class="post_body">
    <h1><?php echo __('Change Password')?></h1> 
    <p><?php echo __('To change your password, please enter your current password to for verification')?></p>
    
    <ul class="list6">
        <li><label><?php echo __('Current Password')?></label><?php echo $this->Form->password('old_password'); ?></li>     
        <li><label><?php echo __('New Password')?></label><?php echo $this->Form->password('password'); ?></li>         
        <li><label><?php echo __('Verify Password')?></label><?php echo $this->Form->password('password2'); ?></li>
    </ul>
    <div align="center" style="margin-top:10px"><input type="submit" value="<?php echo __('Change Password')?>" class="button button-action"></div>
</div>
</form>