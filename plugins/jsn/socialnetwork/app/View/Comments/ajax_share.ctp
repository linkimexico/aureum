<?php if (!empty($comment)): ?>
<li class="slide" id="itemcomment_<?php echo $comment['Comment']['id']?>" style="position: relative">
	<?php if ( $comment['Comment']['type'] != APP_CONVERSATION ): ?>
	<a href="javascript:void(0)" onclick="return removeItemComment(<?php echo $comment['Comment']['id']?>)" class="cross-icon"><i class="icon-remove"></i></a>
	<?php endif; ?>
	<?php 
	if ( !empty( $activity ) )
		echo $this->Jsnsocial->getUserAvatar($comment['User'], 32);
	else
		echo $this->Jsnsocial->getUserAvatar($comment['User']);
	?>
	<div class="comment">
		<?php echo $this->Jsnsocial->getName($comment['User'])?>
		<div class="comment_message">
		    <?php 
		    if ( !empty( $activity ) )
                echo $this->Jsnsocial->formatText( $comment['Comment']['message'] );
            else
                echo $this->Jsnsocial->formatText( $comment['Comment']['message'], false, true );
            ?>
		</div>
		<span class="date">
			<?php echo __('Just now')?>
			<?php if ( $comment['Comment']['type'] != APP_CONVERSATION ): ?> 
			&nbsp;<a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo $comment['Comment']['id']?>, 1)" id="comment_l_<?php echo $comment['Comment']['id']?>" class="comment-thumb"><i class="icon-thumbs-up-alt"></i></a> <span id="comment_like_<?php echo $comment['Comment']['id']?>">0</span>
            <a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo $comment['Comment']['id']?>, 0)" id="comment_d_<?php echo $comment['Comment']['id']?>" class="comment-thumb"><i class="icon-thumbs-down-alt"></i></a> <span id="comment_dislike_<?php echo $comment['Comment']['id']?>">0</span>
		    <?php endif; ?>  
		</span>
	</div>
</li>
<?php endif;?>