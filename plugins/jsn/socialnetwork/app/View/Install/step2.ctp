<h2>Step 2: Site Settings</h2>
<form id="installForm">
<?php echo $this->Form->hidden('db_serialized', array('value' => $db_serialized)); ?>
<ul class="list6">
	<li><label>Site Name</label>
		<?php echo $this->Form->text('site_name'); ?>
	</li>
	<li><label>Site Email</label>
		<?php echo $this->Form->text('site_email'); ?>
	</li>
	<li><label>Default Timezone</label>
        <?php 
        $timezones = $this->Time->listTimezones(null, null, false);
        asort($timezones);
        echo $this->Form->select('timezone', $timezones); 
        ?>
    </li> 
	<li><label>&nbsp;</label>
		<a href="javascript:void(0)" onclick="doStep(2)" id="step_but" class="button button-action"><i class="icon-ok"></i> Next</a>
	</li>
</ul>
</form>