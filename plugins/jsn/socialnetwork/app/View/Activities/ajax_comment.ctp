<?php if (!empty($comment)): ?>
<li id="comment_<?php echo $comment['ActivityComment']['id']?>" class="slide"><?php echo $this->Jsnsocial->getUserAvatar($comment['User'], 32)?>
	<a href="javascript:void(0)" onclick="return removeActivityComment(<?php echo $comment['ActivityComment']['id']?>)" class="cross-icon"><i class="icon-remove"></i></a>
	<div class="comment">
		<?php echo $this->Jsnsocial->getName($comment['User'])?>
		<div class="comment_message"><?php echo $this->Jsnsocial->formatText( $comment['ActivityComment']['comment'] )?></div>
		<span class="date"><?php echo __('Just now')?> 
		    &nbsp;<a href="javascript:void(0)" onclick="likeActivity('activity_comment', <?php echo $comment['ActivityComment']['id']?>, 1)" id="activity_comment_l_<?php echo $comment['ActivityComment']['id']?>" class="comment-thumb"><i class="icon-thumbs-up-alt"></i></a> <span id="activity_comment_like_<?php echo $comment['ActivityComment']['id']?>">0</span>
            <a href="javascript:void(0)" onclick="likeActivity('activity_comment', <?php echo $comment['ActivityComment']['id']?>, 0)" id="activity_comment_l_<?php echo $comment['ActivityComment']['id']?>" class="comment-thumb"><i class="icon-thumbs-down-alt"></i></a> <span id="activity_comment_dislike_<?php echo $comment['ActivityComment']['id']?>">0</span>
		</span>
	</div>
</li>
<?php endif;?>