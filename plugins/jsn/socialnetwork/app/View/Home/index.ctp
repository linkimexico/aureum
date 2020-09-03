<?php
if ( !empty($uid) )
{
    echo $this->Html->css(array('token-input', 'fineuploader', 'jquery.mp'), null, array('inline' => false));
    echo $this->Html->script(array('jquery.tokeninput', 'jquery.fineuploader', 'jquery.mp.min'), array('inline' => false));
}
?>

<script>
jQuery(document).ready(function(){	
	<?php if ( !empty( $tab ) ): ?>	
	if (jQuery("#<?php echo $tab?>").length > 0)
	{
		jQuery('#<?php echo $tab?>').spin('tiny');
		jQuery('#<?php echo $tab?>').children('.badge_counter').hide();
		jQuery('#browse .current').removeClass('current');
		jQuery('#<?php echo $tab?>').parent().addClass('current');
		
		jQuery('#home-content').load( jQuery('#<?php echo $tab?>').attr('data-url'), {noCache: 1}, function(){
			jQuery('#<?php echo $tab?>').spin(false);
			jQuery('#<?php echo $tab?>').children('.badge_counter').fadeIn();
			
			// reattach events
			jQuery('textarea').elastic();
			jQuery(".tip").tipsy({ html: true, gravity: 's' });
			registerOverlay();
		});
	}
	else
		jQuery('#home-content').load( '<?php echo $this->request->base?>/activities/ajax_browse/home', {noCache: 1} );
	<?php endif; ?>
});
</script>

<?php 
if ( empty($uid) && $jsnsocial_setting['force_login'] ):
    if ( !empty($jsnsocial_setting['guest_message']) ): ?>
    <div class="box1"><?php echo nl2br($jsnsocial_setting['guest_message'])?></div>
<?php
    endif;
else:
?>
<div id="leftnav">
	<?php if ($uid): ?>
	<div class="box2 box_style1 menu" id="browse">
		<h3><?php echo h($cuser['name'])?></h3>
		<ul class="list2">			
			<li class="current"><a id="whats_new" data-url="<?php echo $this->request->base?>/activities/ajax_browse/home" rel="home-content" href="<?php echo $this->request->base?>/"><i class="icon-home"></i> <?php echo __("What's New")?></a></li>
			<li><a id="notifications" data-url="<?php echo $this->request->base?>/notifications/ajax_show/home" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:notifications"><i class="icon-globe"></i> <?php echo __('Notifications')?> <span id="notification_count" class="badge_counter"><?php echo $cuser['notification_count']?></span></a></li>
			<li><a id="messages" data-url="<?php echo $this->request->base?>/conversations/ajax_browse" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:messages"><i class="icon-envelope"></i> <?php echo __('Messages')?> <span class="badge_counter"><?php echo $cuser['conversation_user_count']?></span></a></li>
			<li><a id="my-friends" data-url="<?php echo $this->request->base?>/users/ajax_browse/home" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:my-friends"><i class="icon-user"></i> <?php echo __('Friends')?> <span id="friend_count" class="badge_counter"><?php echo $cuser['friend_count']?></span></a></li>
			<li><a id="invite-friends" data-url="<?php echo $this->request->base?>/friends/ajax_invite" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:invite-friends"><i class="icon-share"></i> <?php echo __('Invite Friends')?></a></li>			
			<li <?php if ( !$cuser['friend_request_count'] ) echo 'style="display:none"' ?>><a id="friend-requests" data-url="<?php echo $this->request->base?>/friends/ajax_requests" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:friend-requests"><i class="icon-inbox"></i> <?php echo __('Friend Requests')?> <span class="badge_counter"><?php echo $cuser['friend_request_count']?></span></a></li>			
			
			<?php if (!empty($plugin['event'])): ?>
			<li><a id="my-events" data-url="<?php echo $this->request->base?>/events/ajax_browse/home" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:my-events"><i class="<?php echo $plugin['event']['icon_class']?>"></i> <?php echo __('Upcoming Events')?> <span class="badge_counter"><?php echo $events_count?></span></a></li>
			<?php endif; ?>
			
			<?php if (!empty($plugin['group'])): ?>
			<li><a id="my-groups" data-url="<?php echo $this->request->base?>/groups/ajax_browse/home" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:my-groups"><i class="<?php echo $plugin['group']['icon_class']?>"></i> <?php echo __('My Groups')?></a></li>
			<?php endif; ?>
			
            <?php if (!empty($plugin['blog'])): ?>
			<li><a id="my-blogs" data-url="<?php echo $this->request->base?>/blogs/ajax_browse/home" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:my-blogs"><i class="<?php echo $plugin['blog']['icon_class']?>"></i> <?php echo __('My Blog')?> <span class="badge_counter"><?php echo $cuser['blog_count']?></span></a></li>
			<?php endif; ?>
			
            <?php if (!empty($plugin['photo'])): ?>
			<li><a id="my-photos" data-url="<?php echo $this->request->base?>/albums/ajax_browse/home" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:my-photos"><i class="<?php echo $plugin['photo']['icon_class']?>"></i> <?php echo __('My Photos')?> <span class="badge_counter"><?php echo $cuser['photo_count']?></span></a></li>
			<?php endif; ?>
			
            <?php if (!empty($plugin['video'])): ?>
			<li><a id="my-videos" data-url="<?php echo $this->request->base?>/videos/ajax_browse/home" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:my-videos"><i class="<?php echo $plugin['video']['icon_class']?>"></i> <?php echo __('My Videos')?> <span class="badge_counter"><?php echo $cuser['video_count']?></span></a></li>						
			<?php endif; ?>
			
            <?php if (!empty($plugin['topic'])): ?>
			<li><a id="my-topics" data-url="<?php echo $this->request->base?>/topics/ajax_browse/home" rel="home-content" href="<?php echo $this->request->base?>/home/index/tab:my-topics"><i class="<?php echo $plugin['topic']['icon_class']?>"></i> <?php echo __('My Topics')?> <span class="badge_counter"><?php echo $cuser['topic_count']?></span></a></li>
			<?php endif; ?>
			
			<?php
			if ( $this->elementExists('menu/home') )
                echo $this->element('menu/home');
            ?>
		</ul>
	</div>
	<?php endif; ?>	
	
	<?php echo $this->element('hooks', array('position' => 'home_sidebar') ); ?>

	<?php echo $this->element('blocks/tags_block'); ?>
	
	<?php echo html_entity_decode( $jsnsocial_setting['homepage_code'] )?>
</div>

<div id="center">	
	<div id="home-content">			
	<?php if ( empty( $tab ) ): ?>		
	    
	    <?php if ( empty( $uid ) && $jsnsocial_setting['guest_message'] ): ?>
        <div class="box1"><?php echo nl2br($jsnsocial_setting['guest_message'])?></div>
        <?php endif; ?>
		
		<?php echo $this->element('hooks', array('position' => 'home_top') ); ?>	
		
		<?php
		if ( isset( $activities ) )
			echo $this->element('ajax/home_activity' ); 
		?>
		
	<?php else: ?>
		<?php echo __('Loading...')?>
	<?php endif; ?>	
	</div>
</div>
<?php endif; ?>