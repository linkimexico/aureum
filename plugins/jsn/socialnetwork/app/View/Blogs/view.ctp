<?php 
echo $this->Html->css(array('jquery.mp'), null, array('inline' => false));
echo $this->Html->script(array('jquery.mp.min'), array('inline' => false)); 
?>

<script>
jQuery(document).ready(function(){
	registerImageOverlay();
	jQuery(".sharethis").hideshare({media: '<?php echo FULL_BASE_URL . $this->request->webroot?>img/og-image.png', linkedin: false});
});
</script>

<div id="leftnav">
	<i class="status_big <?php if($blog['User']['online']) echo 'ep_online'; else echo 'ep_offline';?>"></i><img <?php if(empty($blog['User']['avatar_clean'])) echo 'avatar="'.$blog['User']['name'].'"'; ?> src="<?php echo $this->Jsnsocial->getUserPicture($blog['User']['photo'], false)?>" class="main-img">
	<div class="menu">
    	<ul class="list2" id="left-nav" style="margin:4px 0 10px 0">
    		<li><a href="<?php echo $this->request->base?>/users/view/<?php echo $blog['User']['id']?>"><i class="icon-user icon-small"></i> <?php echo h($blog['User']['name'])?></a></li>
    		<?php if ($blog['User']['id'] == $uid || ( !empty($cuser) && $cuser['Role']['is_admin'] )): ?>
    		<li><a href="<?php echo $this->request->base?>/blogs/create/<?php echo $blog['Blog']['id']?>"><i class="icon-pencil icon-small"></i> <?php echo __('Edit Entry')?></a></li>
    		<?php endif; ?>
    		<?php if ( !empty($uid) && ($uid != $blog['User']['id']) && !$areFriends ): ?>
    		<li><a href="<?php echo $this->request->base?>/friends/ajax_add/<?php echo $blog['User']['id']?>" class="overlay" title="<?php printf( __('Send %s a friend request'), h($blog['User']['name']) )?>"><i class="icon-plus-sign-alt icon-small"></i> <?php echo __('Add as Friend')?></a></li>
    		<?php endif; ?> 
    	</ul>
    </div>
	
	<?php echo $this->element('hooks', array('position' => 'blog_detail_sidebar') ); ?>
	
	<div class="box2 box_style1">
		<h3><?php echo __('Tags')?></h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/tags_item_block' ); ?>
		</div>
	</div>
	
	<?php echo $this->element('likes', array('item' => $blog['Blog'], 'type' => 'blog')); ?>
	
	<div class="box2">
		<h3><?php echo __('Other Entries')?></h3>
		<div class="box_content">
		    <?php echo $this->element('blocks/blogs_block', array('blogs' => $other_entries)); ?>
		</div>
	</div>
	
	<div class="box4">
		<ul class="list6 list6sm">
			<li><a href="<?php echo $this->request->base?>/reports/ajax_create/blog/<?php echo $blog['Blog']['id']?>" class="overlay" title="<?php echo __('Report Blog')?>"><?php echo __('Report Blog')?></a></li>
			<li><a href="#" class="sharethis"><?php echo __('Share This')?></a></li>
		</ul>	
	</div>	
	
</div>

<div id="center">		
	<div class="comment_message post_body">
	    <h1><?php echo h($blog['Blog']['title'])?></h1>    
	    <?php echo $this->Jsnsocial->cleanHtml( $blog['Blog']['body'] )?><br /><br />
	    <span class="date"><?php echo $this->Jsnsocial->getTime($blog['Blog']['created'], $jsnsocial_setting['date_format'], $utz)?></span>
	</div><br />	

	<h2><?php echo __('Comments')?> (<span id="comment_count"><?php echo $blog['Blog']['comment_count']?></span>)</h2>
	<ul class="list6 comment_wrapper" id="comments">
	<?php echo $this->element('comments');?>
	</ul>
	
	<?php echo $this->element( 'comment_form', array( 'target_id' => $blog['Blog']['id'], 'type' => APP_BLOG ) ); ?>
</div>