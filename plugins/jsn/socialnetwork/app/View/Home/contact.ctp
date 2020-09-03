<script>
function contactSubmit()
{
	if ( jQuery('#contact_name').val() == '' || jQuery('#contact_email').val() == '' || jQuery('#subject').val() == '' || jQuery('#message').val() == '' )
	{
		jsnsocialAlert('<?php echo __('All fields are required')?>');
		return false;
	}
	
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if( reg.test( jQuery('#contact_email').val() ) == false ) {
        jsnsocialAlert('<?php echo __('Invalid email address')?>');
        return false;
    }
	
	return true;
}
</script>

<?php
if ( !$uid )
{
	$cuser['name']  = '';
	$cuser['email'] = '';
}
?>

<div class="page-padding">
    <form action="<?php echo $this->request->base?>/home/contact" method="post" onsubmit="return contactSubmit()">
    <h1><?php echo __('Contact Us')?></h1>
    
    <ul class="list6 list6sm2">
    	<li><label><?php echo __('Your Name')?></label>
    		<?php echo $this->Form->text('name', array('value' => $cuser['name'], 'id' => 'contact_name')); ?>
    	</li>
    	<li><label><?php echo __('Email Address')?></label>
    		<?php echo $this->Form->text('email', array('value' => $cuser['email'], 'id' => 'contact_email')); ?>
    	</li>
    	<li><label><?php echo __('Subject')?></label>
    		<?php echo $this->Form->text('subject'); ?>
    	</li>
    	<li><label><?php echo __('Message')?></label>
    		<?php echo $this->Form->textarea('message'); ?>
    	</li>
    	<li><label>&nbsp;</label>
    		<?php echo $this->Form->submit('Send', array('class' => 'button button-action')); ?>
    	</li>
    </ul>	
    </form>
</div>