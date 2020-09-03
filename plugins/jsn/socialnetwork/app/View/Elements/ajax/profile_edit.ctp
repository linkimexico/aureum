

<h2><?php echo __('User Settings')?></h2>
<ul class="list6">
	<li><label><?php echo __('Profile Privacy')?></label>
		<?php echo $this->Form->select('privacy', array( PRIVACY_EVERYONE => __('Everyone'), 
														 PRIVACY_FRIENDS => __('Friends Only'), 
														 PRIVACY_ME => __('Me Only')), 
												  array('value' => $cuser['privacy'], 'empty' => false)); ?>
	</li>
	<li><label><?php echo $this->Form->checkbox('notification_email', array('checked' => $cuser['notification_email'])); ?></label><?php echo __('Receive Site Emails (including daily notification summary email)')?></li>		
	<!-- <li><label><?php echo $this->Form->checkbox('hide_online', array('checked' => $cuser['hide_online'])); ?></label><?php echo __('Do not show my online status')?></li>        	-->
</ul>

<div align="center" style="margin-top:10px"><input type="submit" class="button button-action" value="<?php echo __('Save Changes')?>"></div>
