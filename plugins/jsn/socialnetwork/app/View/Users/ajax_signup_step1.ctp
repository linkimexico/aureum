<?php if ( !empty( $jsnsocial_setting['require_birthday'] ) ): ?> 
<li><label><?php echo __('Birthday')?> <a href="javascript:void(0)" class="tip" title="<?php echo __('Only month and date will be shown on your profile')?>">(?)</a></label><?php echo $this->Form->month('birthday')?> <?php echo $this->Form->day('birthday')?> <?php echo $this->Form->year('birthday', 1930, date('Y'))?></li>	
<?php endif; ?>
<?php if ( !empty( $jsnsocial_setting['enable_timezone_selection'] ) ): ?>
<li><label><?php echo __('Timezone')?></label><?php echo $this->Form->select('timezone', $this->Jsnsocial->getTimeZones()); ?>
</li>
<?php endif; ?>
<li><label><?php echo __('Gender')?></label><?php echo $this->Form->select('gender', array('Male' => __('Male'), 'Female' => __('Female'))); ?></li>

<?php
echo $this->element( 'custom_fields', array( 'show_heading' => true ) );
