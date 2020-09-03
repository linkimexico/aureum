<link rel="stylesheet" type="text/css" href="<?php echo $this->request->webroot?>theme/light/css/main.css" />

<script>
function doStep( step )
{
	jQuery("#step_but i").attr("class", "icon-refresh icon-spin");
    jQuery("#step_but").addClass('disabled');
	jQuery.post("<?php echo $this->request->base?>/install/ajax_step" + step, jQuery("#installForm").serialize(), function(data){
		jQuery("#step_but i").attr("class", 'icon-ok');
        jQuery("#step_but").removeClass('disabled');
		if (data.indexOf('jsnsocialError') > 0) {
			jQuery(".error-message").show();
			jQuery(".error-message").html(data);
		} else {				
			jQuery(".error-message").hide();
			jQuery("#install").html(data);
		}
	});
}
</script>

<div id="header">
    <div class="wrapper">
        <div id="logo" style="float:none;padding: 10px 0;margin:0px;">
            <a id="logo_default" href="<?php echo $this->request->webroot?>"></a>
        </div>
    </div>
</div>

<div class="wrapper">
	<div id="content">
		<h1>Welcome to Easy Profile - Social Network Installation</h1>
		
		<div class="error-message" style="display:none"></div>
		<div id="install">
			<p>Please make sure that your server meets all the requirements before proceeding</p>
			<ul>
				<li>PHP 5.2.8+ with short tags enabled or PHP 5.4+</li>
				<li>MySql 5+</li>
				<li>PHP extensions: MySql PDO, GD2, Curl, libxml, exif, zlib (if you need to export theme)</li>
				<li>Magic quotes must be disabled</li>
				<li>Memory Limit: 128M+</li>
				<li>The following directories are writable by the web server user (e.g. change permission to 755 ): app/Config, app/tmp and all its subdirectories, app/webroot/uploads and all its subdirectories</li> 
			</ul>
			
			<h2>Step 1: Database Configuration</h2>
			<form id="installForm">
			<ul class="list6">
				<li><label>Database Host</label>
					<?php echo $this->Form->text('db_host', array('value' => 'localhost')); ?> (this is usually "localhost")
				</li>				
				<li><label>Database Username</label>
					<?php echo $this->Form->text('db_username'); ?>
				</li>
				<li><label>Database Password</label>
					<?php echo $this->Form->password('db_password'); ?>
				</li>
				<li><label>Database Name</label>
					<?php echo $this->Form->text('db_name'); ?>
				</li>
				<li><label>Unix Socket</label>
					<?php echo $this->Form->text('db_socket'); ?> (leave empty if you are not sure)
				</li>
				<li><label>Table Prefix</label>
					<?php echo $this->Form->text('db_prefix'); ?> (choose an optional table prefix which must end in an underscore)
				</li>
				<li><label>&nbsp;</label>
				    <a href="javascript:void(0)" onclick="doStep(1)" id="step_but" class="button button-action"><i class="icon-ok"></i> Next</a>
				</li> 
			</ul>
			</form>
		</div>		
	</div>
</div>