<?php if ( $jsnsocial_setting['fb_integration'] && !empty( $jsnsocial_setting['fb_app_id'] ) && in_array( $this->request->action, array( 'fb_register' ) ) ): ?>
<div id="fb-root"></div>
<script>
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo $jsnsocial_setting['fb_app_id']?>";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php endif; ?>