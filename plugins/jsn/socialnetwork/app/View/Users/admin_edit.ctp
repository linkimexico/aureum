<style>
#content {
	overflow: visible;
</style>

<?php //echo $this->element('admin/adminnav', array("cmenu" => "users"));?>

<form action="<?php echo $this->request->base?>/admin/users/edit" method="post">
<?php echo $this->Form->hidden('id', array('value' => $user['User']['id'])); ?>
<div>	
	<h2>Profile Settings</h2>
	<ul class="list6">		
		<li><label>Registered Date</label><?php echo $this->Jsnsocial->getTime($user['User']['created'], $jsnsocial_setting['date_format'], $utz)?></li>
		<li><label>Last Online</label><?php echo $this->Jsnsocial->getTime($user['User']['last_login'], $jsnsocial_setting['date_format'], $utz)?></li>
		<li><label>Stats</label><?php echo $user['User']['friend_count']?> friends, <?php echo $user['User']['photo_count']?> photos, 
			<?php echo $user['User']['blog_count']?> blog entries, <?php echo $user['User']['topic_count']?> topics, <?php echo $user['User']['video_count']?> videos
		</li>
		<li><label>Role</label><?php echo $this->Form->select('role_id', $roles, 
																		 array( 'value' => $user['User']['role_id'],
																		 	    'empty' => false ) ); ?>
		</li>		
		<li><label>Active</label><?php echo $this->Form->select('active', array( 0 => 'No', 
																				 1 => 'Yes'), 
																		  array( 'value' => $user['User']['active'],
																		  		 'empty' => false ) ); ?>
		</li>	
		<li><label>Featured</label><?php echo $this->Form->select('featured', array( 0 => 'No', 
																				     1 => 'Yes'), 
																		      array( 'value' => $user['User']['featured'],
																		  		     'empty' => false ) ); ?>
		</li>
	</ul>
	
	<?php echo $this->element('ajax/profile_edit', array('cuser' => $user['User']));?>
</div>
</form>