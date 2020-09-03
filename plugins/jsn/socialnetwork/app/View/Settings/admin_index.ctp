<script>
jQuery(document).ready(function(){
	initTabs('settings_index');
});
</script>

<style>
.list6 label {
	width: 190px;
}
</style>

<?php echo $this->element('admin/adminnav', array("cmenu" => "settings"));?>

<div id="center">
<form action="<?php echo $this->request->base?>/admin/settings" enctype="multipart/form-data" method="post">
	<h1>System Settings</h1>
	
	<div id="settings_index">
    	<div class="tabs-wrapper">
    		<ul class="tabs">
    			<li id="general" class="active">General</li>
    			<li id="features">Features</li>			
    			<li id="adsetting">Custom Blocks</li>
    			
    		</ul>
    	</div>
    	<div id="general_content" class="tab" style="display:block">
    		<ul class="list6">
    					
    			<li><label>Popular Items Interval (<a href="javascript:void(0)" class="tip" title="Display popular items within X days">?</a>)</label>
    				<?php echo $this->Form->text('popular_interval', array('value' => $jsnsocial_setting['popular_interval'])); ?>
    			</li>
    			<li><label>Default Profile Privacy</label>
    				<?php echo $this->Form->select('profile_privacy', array( PRIVACY_EVERYONE => 'Everyone', PRIVACY_FRIENDS => 'Friends & Me', PRIVACY_ME => 'Me Only' ), array('value' => $jsnsocial_setting['profile_privacy'], 'empty' => false)); ?>
    			</li>
    			<li><label>Default Theme</label>
    				<?php echo $this->Form->select('default_theme', $site_themes, array('value' => $jsnsocial_setting['default_theme'], 'empty' => false)); ?>
    			</li>			
    			<li><label>Default Home Feed</label>
    				<?php echo $this->Form->select('default_feed', array( 'everyone' => 'Everyone', 'friends' => 'Friends & Me' ), array('value' => $jsnsocial_setting['default_feed'], 'empty' => false)); ?>
    			</li>
    			<li><label>Time Format</label>
    				<?php echo $this->Form->select('time_format', array( '12h' => '12-hour', '24h' => '24-hour' ), array('value' => $jsnsocial_setting['time_format'], 'empty' => false)); ?>
    			</li>
    			<li><label>Date Format (<a href="http://php.net/manual/en/function.date.php" target="_blank" class="tip" title="Refer to PHP date function for more information about<br />strftime format. Click this for more details">?</a>)</label>
    				<?php echo $this->Form->text('date_format', array('value' => $jsnsocial_setting['date_format'])); ?>
    			
                <br /> 
                <h2>Mail Settings</h2> 
                <li><label>Mail Transport</label>
                    <?php echo $this->Form->select('mail_transport', array( 'Mail' => 'PHP Mail', 'Smtp' => 'SMTP' ), array('value' => $jsnsocial_setting['mail_transport'], 'empty' => false)); ?>
                </li>	
                <li><label>SMTP Host (<a href="javascript:void(0)" class="tip" title="In some cases, you might need to include ssl:// in the hostname">?</a>)</label>
                    <?php echo $this->Form->text('smtp_host', array('value' => $jsnsocial_setting['smtp_host'])); ?>
                </li>
                <li><label>SMTP Username</label>
                    <?php echo $this->Form->text('smtp_username', array('value' => $jsnsocial_setting['smtp_username'])); ?>
                </li>
                <li><label>SMTP Password</label>
                    <?php echo $this->Form->password('smtp_password', array('value' => $jsnsocial_setting['smtp_password'])); ?>
                </li>	
                <li><label>SMTP Port</label>
                    <?php echo $this->Form->text('smtp_port', array('value' => $jsnsocial_setting['smtp_port'])); ?>
                </li>   
    		</ul>
    	</div>	
    	<div id="features_content" class="tab">
    		<ul class="list6">			
    			<li><label>Allow Guests To Search</label>
    				<?php echo $this->Form->checkbox('guest_search', array('checked' => $jsnsocial_setting['guest_search'])); ?>
    			</li>					
    			<li><label>Enable Activities Feed Selection</label>
    				<?php echo $this->Form->checkbox('feed_selection', array('checked' => $jsnsocial_setting['feed_selection'])); ?>
    			</li>	
    			<li><label>Hide Activities Feed From Guests</label>
    				<?php echo $this->Form->checkbox('hide_activites', array('checked' => $jsnsocial_setting['hide_activites'])); ?>
    			</li>			
    			<li><label>Save Original Image (no for profile image) (<a href="javascript:void(0)" class="tip" title="Check this to store original image when users upload photos<br />You might wanna disable if you are concerned about server space">?</a>)</label>
                    <?php echo $this->Form->checkbox('save_original_image', array('checked' => $jsnsocial_setting['save_original_image'])); ?>
                </li>   
    			
    		</ul>
    	</div>	
    	
    	<div id="adsetting_content" class="tab">
    		You can use these to insert banner ads or custom static content<br /><br />
    		<ul class="list6">
    		    <li><label>Registration Message</label>
                    <?php echo $this->Form->textarea('registration_message', array('value' => $jsnsocial_setting['registration_message'])); ?>
                </li>
    		    <li><label>Homepage Guest Message</label>
                    <?php echo $this->Form->textarea('guest_message', array('value' => $jsnsocial_setting['guest_message'])); ?>
                </li>
                <li><label>Homepage Member Message</label>
                    <?php echo $this->Form->textarea('member_message', array('value' => $jsnsocial_setting['member_message'])); ?>
                </li>
    			<!-- <li><label>Header Block Code</label>
    				<?php echo $this->Form->textarea('header_code', array('value' => $jsnsocial_setting['header_code'])); ?>
    			</li> -->
    			<!-- <li><label>Sidebar Block Code</label>
    				<?php echo $this->Form->textarea('sidebar_code', array('value' => $jsnsocial_setting['sidebar_code'])); ?>
    			</li> -->
    			<li><label>Homepage Block Code</label>
    				<?php echo $this->Form->textarea('homepage_code', array('value' => $jsnsocial_setting['homepage_code'])); ?>
    			</li>
    			<li><label>Footer Block Code</label>
    				<?php echo $this->Form->textarea('footer_code', array('value' => $jsnsocial_setting['footer_code'])); ?> 
    			</li>
    		</ul>
    	</div>
    	
    </div>
	
	<div class="regSubmit">
	    <input type="submit" value="Save Settings" class="button button-action button-medium">
    </div>
</form>
</div>