<a id="main_menu_toggle" href="#" onclick="jQuery('#leftnav').fadeOut();jQuery('#main_menu').slideToggle();return false;"><i class="icon-reorder"></i></a>
<a href="#" id="leftnav_toggle"><i class="icon-th-large"></i></a>
<script>
jQuery(document).ready(function(){
	if(jQuery('#leftnav').length==0) jQuery('#leftnav_toggle').remove();
	jQuery('#leftnav_toggle').click(function(){
		if(jQuery('#profileTabSocialnetwork').length>0) jQuery('#profileTabSocialnetwork').parent().click();
		jQuery('#main_menu').slideUp();
		jQuery('#leftnav').animate({width:'toggle',opacity:'toggle'},350);
		return false;
	});
	jQuery('#leftnav .menu a').click(function(){
		if(window.innerWidth<900){
			jQuery('#main_menu').slideUp();
			jQuery('#leftnav').fadeOut();
		}
	});
});
/*jQuery(window).load(function(){
	var lmtop=jQuery('.jsn_social_header').offset().top;
	jQuery('#leftnav').css('top',lmtop+'px');
});*/
</script>
<ul id="main_menu">
    <li <?php if ($this->request->controller == 'home') echo 'class="current"';?>>
        <a href="<?php echo $this->request->base?>/"><i class="icon-home"></i> <?php echo __('Home')?></a>
    </li>
    <?php foreach ($site_plugins as $p): ?>                
    <li <?php if ($this->request->controller == Inflector::pluralize($p['Plugin']['key'])) echo 'class="current"';?>>
        <a href="<?php echo $this->request->base . $p['Plugin']['url']?>"><i class="<?php echo $p['Plugin']['icon_class']?>"></i> <?php echo __($p['Plugin']['name'])?></a>
    </li>
    <?php endforeach; ?>
    
    <?php foreach ($menu_pages as $mp): ?>                
    <li <?php if ($this->request->controller == 'pages' && $this->request->action == 'display' && !empty($page['Page']['alias']) && $page['Page']['alias'] == $mp['Page']['alias']) echo 'class="current"';?>>
        <a href="<?php echo $this->request->base . '/pages/' . $mp['Page']['alias']?>"><i class="<?php echo $mp['Page']['icon_class']?>"></i> <?php echo __($mp['Page']['title'])?></a>
    </li>
    <?php endforeach; ?>
    
    <?php
    if ( $this->elementExists('menu/main') )
        echo $this->element('menu/main');
    ?>
</ul>
