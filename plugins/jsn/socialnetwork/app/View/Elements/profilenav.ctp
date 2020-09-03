<div id="leftnav">
	<div class="box2 box_style1 menu">
		<h3><?php echo __('User Menu')?></h3>
		<ul class="list2">					
			<li <?php if ($cmenu == 'profile') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/users/profile"><i class="icon-file-text"></i> <?php echo __('Profile Information')?></a>
			</li>
			<li <?php if ($cmenu == 'password') echo 'class="current"'; ?>>
                <a href="<?php echo $this->request->base?>/users/password"><i class="icon-key"></i> <?php echo __('Change Password')?></a>
            </li>
            <?php
            if ( $this->elementExists('menu/profile') )
                echo $this->element('menu/profile');
            ?>
		</ul>
	</div>
</div>