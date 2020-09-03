<?php if (!empty($activity)): ?>
<li class="slide" id="activity_<?php echo $activity['Activity']['id']?>">
	<a href="javascript:void(0)" onclick="return removeActivity(<?php echo $activity['Activity']['id']?>)" class="cross-icon"><i class="icon-remove"></i></a>
	<?php echo $this->Jsnsocial->getUserAvatar($activity['User'])?>
	<div class="comment">
		<?php echo $this->Jsnsocial->getName($activity['User'])?>
		<?php
		if ( !empty( $activity['Content'] ) ):
			echo $this->element( 'misc/activity_contents/wall_post'); 
        elseif ( !empty( $activity['Activity']['params'] ) && $activity['Activity']['params'] != 'mobile' ):
            echo $this->element('misc/activity_contents/wall_post_link', array('activity' => $activity));
		else:
		?>
		<div class="comment_message"><?php echo $this->Jsnsocial->formatText( $activity['Activity']['content'] )?></div>
		<?php 
		endif; 
		?>
		<span class="date">
			<?php echo __('Just now')?>
			<?php if ( $activity['Activity']['params'] == 'mobile' ) echo __('via mobile'); ?>
			. <a href="javascript:void(0)" onclick="showCommentForm(<?php echo $activity['Activity']['id']?>)"><?php echo __('Comment')?></a>
			&nbsp;<a href="javascript:void(0)" onclick="likeActivity('activity', <?php echo $activity['Activity']['id']?>, 1)" class="comment-thumb"><i class="icon-thumbs-up-alt"></i></a> <span id="activity_like_<?php echo $activity['Activity']['id']?>">0</span>
            <a href="javascript:void(0)" onclick="likeActivity('activity', <?php echo $activity['Activity']['id']?>, 0)" class="comment-thumb"><i class="icon-thumbs-down-alt"></i></a> <span id="activity_dislike_<?php echo $activity['Activity']['id']?>">0</span>
		</span>
	</div>
	<ul class="activity_comments" style="display:none" id="comments_<?php echo $activity['Activity']['id']?>">
		<li id="newComment_<?php echo $activity['Activity']['id']?>" style="display:none"><?php echo $this->Jsnsocial->getUserAvatar($cuser, 32)?>
			<div class="comment"><textarea class="commentBox" onfocus="showCommentButton(<?php echo $activity['Activity']['id']?>)" placeholder="<?php echo __('Write a comment...')?>" id="commentForm_<?php echo $activity['Activity']['id']?>"></textarea>
				<div style="overflow:hidden;display:none;margin: 5px 0px 0px;" id="commentButton_<?php echo $activity['Activity']['id']?>">
				    <a href="javascript:void(0)" onclick="submitComment(<?php echo $activity['Activity']['id']?>)" class="button button-primary button-tiny topButton"> <i class="icon icon-comment-alt"></i> <?php echo __('Comment')?></a>					
				</div>
			</div>
		</li>
	</ul>
</li>
<?php endif;?> 