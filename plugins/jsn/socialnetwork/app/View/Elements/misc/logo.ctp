<div id="logo">
	<?php if ( !empty( $jsnsocial_setting['logo'] ) ): ?>
	<a href="<?php echo $this->request->base?>/"><img src="<?php echo $this->request->webroot . $jsnsocial_setting['logo']?>" alt="<?php echo $jsnsocial_setting['site_name']; ?>"></a>
	<?php else: ?> 
	<a href="<?php echo $this->request->base?>/" id="logo_default"><?php echo $jsnsocial_setting['site_name']; ?></a>
	<?php endif; ?>
</div>