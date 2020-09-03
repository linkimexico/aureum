<?php
echo $this->Html->css(array('token-input'), null, array('inline' => false));
echo $this->Html->script(array('jquery.tokeninput'), array('inline' => false));
?>

<div id="leftnav">
	<div class="box2 box_style1">
		<h3><?php echo __('Participants')?> (<?php echo count($convo_users)?>)</b></h3>
		<div class="box_content">
			<ul class="list6 list6sm">
				<?php
				foreach ($convo_users as $convo_user):
				?>
				<li><?php echo $this->element( 'misc/user_mini', array( 'user' => $convo_user['User'], 'areFriends' => in_array( $convo_user['User']['id'], $friends) ) ); ?></li>
				<?php
				endforeach;
				?>
			</ul>
		</div>
	</div>
	
	<div class="box4">
		<ul class="list6 list6sm">
			<li><a href="<?php echo $this->request->base?>/conversations/ajax_add/<?php echo $conversation['Conversation']['id']?>" class="overlay" title="<?php echo __('Add People To This Conversation')?>"><?php echo __('Add People')?></a></li>
			<li><a href="javascript:void(0)" onclick="jsnsocialConfirm('<?php echo __('Are you sure you want to leave this conversation')?>', '<?php echo $this->request->base?>/conversations/do_leave/<?php echo $conversation['Conversation']['id']?>')"><?php echo __('Leave Conversation')?></a></li>
		</ul>
	</div>
</div>
<div id="center">
    <a href="<?php echo $this->request->base?>/home/index/tab:messages" class="button topButton"><?php echo __('Back to Messages')?></a>
	<h1><?php echo h($conversation['Conversation']['subject'])?></h1>
	<div class="convo_msg comment_wrapper" style="margin-bottom:10px">
		<?php echo $this->Jsnsocial->getUserAvatar($conversation['User'])?>
		<div class="comment">
			<?php echo $this->Jsnsocial->getName($conversation['User'])?>
			<div class="comment_message"><?php echo $this->Jsnsocial->formatText( $conversation['Conversation']['message'], false, true )?></div>
			<span class="date"><?php echo $this->Jsnsocial->getTime($conversation['Conversation']['created'], $jsnsocial_setting['date_format'], $utz)?></span>
		</div>
	</div>

	<h2><?php echo __('Messages')?> (<span id="comment_count"><?php echo $conversation['Conversation']['message_count']?></span>)</h2>
	<ul class="list6 comment_wrapper" id="comments">
	<?php echo $this->element('comments');?>
	</ul>
	
	<?php
	if (!empty($uid)) 
		echo $this->element('comment_form', array('target_id' => $conversation['Conversation']['id'], 'type' => APP_CONVERSATION));
	?>
</div>