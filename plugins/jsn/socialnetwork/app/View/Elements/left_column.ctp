<div id="noleft" <?php if ( isset( $this->request->params['admin'] ) || !empty($no_right_column) ) echo 'style="margin-right:0"' ?>>			
	<?php echo $this->Session->flash(); ?>
	<?php
    if ( empty($uid) && !$jsnsocial_setting['disable_registration'] && !in_array( $this->request->params['action'], array( 'register', 'fb_register', 'recover', 'resetpass' ) ) ):
		if ( $jsnsocial_setting['force_login'] ):
			echo $this->element('registration');
		else: 
    ?>
	<?php if(JRequest::getVar('view','profile')=='social') : ?>
    <div class="box1 guest_msg">
        <?php if ( $jsnsocial_setting['fb_integration'] && !empty( $jsnsocial_setting['fb_app_id'] ) ): ?>
        <div style="float:right">
            <a href="<?php echo $this->request->base?>/users/fb_register"><img src="<?php echo $this->request->webroot?>img/fb_register_button.png"></a>
        </div>
        <?php endif; ?>
        <h1 class="jsn_social_registration_title"><?php echo __('Welcome to')?> <?php echo $jsnsocial_setting['site_name']?></h1>


        <?php if ( !empty($jsnsocial_setting['registration_message']) ): ?>
        <p class="jsn_social_registration_message"><?php echo nl2br($jsnsocial_setting['registration_message'])?></p>
        <?php else: ?>
        <p class="jsn_social_registration_message"><?php echo __('Be part of the community and join us today!')?></p>
        <?php endif; ?>

		<div id="join_now">
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration',false); ?>" class="button button-action button-large" id="join_now_btn"><i class="icon-group"></i> <?php echo __('Join Now!')?></a>

		<div class="socialconnect"></div>
		</div>

        <div style="clear:both;"></div>
    </div>
    <?php 
			endif;
		endif;
    endif; 
    ?>
	<?php echo $this->fetch('content'); ?>
</div>