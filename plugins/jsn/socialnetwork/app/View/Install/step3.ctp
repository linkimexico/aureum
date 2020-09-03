<h2>Step 3: Root Admin Account</h2>
<form id="installForm">
<?php echo $this->Form->hidden('db_serialized', array('value' => $db_serialized)); ?>
<ul class="list6">
	<li><label>Name</label>
		<?php echo $this->Form->text('name'); ?>
	</li>
	<li><label>Email</label>
		<?php echo $this->Form->text('email'); ?>
	</li>
	<li><label>Password</label>
		<?php echo $this->Form->password('password'); ?>
	</li>
	<li><label>Confirm Password</label>
		<?php echo $this->Form->password('password2'); ?>
	</li>
	<li><label>Timezone</label>
		<?php 
        $timezones = $this->Time->listTimezones(null, null, false);
        asort($timezones);
        echo $this->Form->select('timezone', $timezones); 
        ?>
	</li>
	<li><label>&nbsp;</label>
	    <a href="javascript:void(0)" onclick="doStep(3)" id="step_but" class="button button-action"><i class="icon-ok"></i> Next</a>
	</li>
</ul>
</form>