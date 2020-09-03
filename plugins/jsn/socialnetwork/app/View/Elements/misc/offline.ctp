<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo __('Offline Mode')?>
	</title>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css( array('main', 'all') );
		echo $this->Html->script( array('scripts', 'global') );
	?>
</head>
<body>

<div id="header" style="min-height: 50px;">
    <div class="wrapper">
    	<?php echo $this->element('misc/logo'); ?>
    	<?php echo $this->element('userbox'); ?>	
    </div>
</div>		
<div class="wrapper">
	<div id="content">
		<?php echo $this->Session->flash(); ?>
		<h1><?php echo __('Offline Mode')?></h1>
		<?php echo $offline_message?>
	</div>	
</div>
</body>
</html> 