<form id="commentForm"> 
<?php
echo $this->Form->hidden('target_id', array('value' => $target_id));
echo $this->Form->hidden('type', array('value' => $type));

if ( !empty( $class ) )
    $cls = $class;
else
    $cls = 'commentForm';
?>
<?php echo $this->Jsnsocial->getUserAvatar($cuser)?>
<div class="comment">
    <?php echo $this->Form->textarea('message', array('class' => $cls, 'placeholder' => __('Write a comment'), 'onfocus' => 'showCommentButton(0)'));?>
    <div style="text-align:right;display:none;margin-top:5px" id="commentButton_0">
        <?php if ( $uid ): ?>
        <a href="javascript:void(0)" class="button button-primary button-tiny" onclick="postComment()" id="shareButton"> <i class="icon icon-comment-alt"></i> <?php echo __('Post')?></a>
        <?php else: ?>
        <?php echo __('Login or register to post your comment')?>
        <?php endif; ?>
    </div>
</div>	
</form>