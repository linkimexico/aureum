<script>
jQuery(document).ready(function(){
	registerCrossIcons();
});

function removeActivity(id)
{
	jQuery.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',        
        callback: function(){
            jQuery.post('<?php echo $this->request->base?>/activities/ajax_remove', {id: id}, function() {
                jQuery('#activity_'+id).fadeOut('normal', function() {
                    jQuery('#activity_'+id).remove();
                });         
            });
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: "<?php echo addslashes(__('Are you sure you want to remove this activity?'))?>",
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
}

function removeActivityComment(id)
{
	jQuery.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',        
        callback: function(){
            jQuery.post('<?php echo $this->request->base?>/activities/ajax_removeComment', {id: id}, function() {
                jQuery('#comment_'+id).fadeOut('normal', function() {
                    jQuery('#comment_'+id).remove();
                }); 
            });
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: "<?php echo addslashes(__('Are you sure you want to remove this comment?'))?>",
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
}

function removeItemComment(id)
{
    jQuery.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',        
        callback: function(){
            jQuery.post('<?php echo $this->request->base?>/comments/ajax_remove', {id: id}, function() {
                jQuery('#itemcomment_'+id).fadeOut('normal', function() {
                    jQuery('#itemcomment_'+id).remove();
                }); 
            });
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: "<?php echo addslashes(__('Are you sure you want to remove this comment?'))?>",
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
}
</script>

<style>
#list-content li {
	position: relative;
}
</style>

<?php 
foreach ($activities as $activity):
	//pr($activity); 
	$item_type = ( $activity['Activity']['item_type'] == APP_PHOTO && $activity['Activity']['params'] == 'item' ) ? APP_ALBUM : $activity['Activity']['item_type'];
	
	if ( !empty( $activity['Content'] ) )
		if ( $activity['Activity']['item_type'] == APP_PHOTO && $activity['Activity']['params'] == 'item' ) 
			$obj = $activity['Content'][0]['Album'];
		elseif ( !empty( $activity['Content'][ucfirst( $item_type )] ) )
			$obj = $activity['Content'][ucfirst( $item_type )];
?>
<li id="activity_<?php echo $activity['Activity']['id']?>">
	<?php 
	// delete link available for activity poster, site admin and item admins
	if ( $activity['Activity']['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $admins ) && in_array( $uid, $admins ) ) ):
	?>
	<a href="javascript:void(0)" onclick="return removeActivity(<?php echo $activity['Activity']['id']?>)" class="cross-icon"><i class="icon-remove"></i></a>
	<?php endif; ?>
	<?php echo $this->Jsnsocial->getUserAvatar($activity['User'])?>
	<div class="comment hasDelLink">
		<div class="activity_text">
			<?php echo $this->Jsnsocial->getName($activity['User'])?>
			<?php
			if ( !empty( $activity['TextContent'] ) )
				echo $this->element('misc/activity_texts/' . $activity['Activity']['action'], array('activity' => $activity));				
			else	
				echo $this->element('misc/activity_texts/general', array('activity' => $activity));		
			?>
		</div>
		<?php
		if ( !empty( $activity['Content'] ) ): 
		    //pr($activity);
			echo $this->element('misc/activity_contents/' . $activity['Activity']['action'], array('activity' => $activity));
		else:
		?>			
			<div class="comment_message truncate" data-more-text="<?php echo __('Show More')?>" data-less-text="<?php echo __('Show Less')?>">
			<?php 
			if ( !empty( $activity['Activity']['content'] ) && !$activity['Activity']['query'] ) 
			{
				if ( $activity['Activity']['action'] == 'wall_post' ) // wall post
				{
					if ( !empty( $activity['Activity']['params'] ) && $activity['Activity']['params'] != 'mobile' )
                        echo $this->element('misc/activity_contents/wall_post_link', array('activity' => $activity));
                    else
					   echo $this->Jsnsocial->formatText( $activity['Activity']['content'] );
                }
				else // everything else
					echo nl2br( $this->Text->autoLink( $activity['Activity']['content'], array( 'target' => '_blank' ) ) );
			}
			?>
			</div>
		<?php 
		endif; 
        //pr($activity);
		?>							
		<span class="date">
			<?php if ( $activity['Activity']['params'] != 'no-comments' ): ?>
				<a href="<?php echo $this->request->base?>/users/view/<?php echo $activity['Activity']['user_id']?>/activity_id:<?php echo $activity['Activity']['id']?>" class="date"><?php echo $this->Jsnsocial->getTime( $activity['Activity']['created'], $jsnsocial_setting['date_format'], $utz )?></a>
				<?php if ( $activity['Activity']['params'] == 'mobile' ) echo __('via mobile'); ?>
				<?php if ( !isset($is_member) || $is_member ): ?>
					. <a href="javascript:void(0)" onclick="showCommentForm(<?php echo $activity['Activity']['id']?>)"><?php echo __('Comment')?></a> 
					<?php if ( $activity['Activity']['params'] == 'item' ): ?>
						&nbsp;<a href="javascript:void(0)" onclick="likeActivity('<?php echo $item_type?>', <?php echo $activity['Activity']['item_id']?>, 1)" id="<?php echo $item_type?>_l_<?php echo $activity['Activity']['item_id']?>" class="comment-thumb <?php if ( !empty( $uid ) && !empty( $activity['Likes'][$uid] ) ): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i></a> 
						<a href="<?php echo $this->request->base?>/likes/ajax_show/<?php echo $item_type?>/<?php echo $activity['Activity']['item_id']?>" class="overlay" title="<?php echo __('People Who Like This')?>"><span id="<?php echo $item_type?>_like_<?php echo $activity['Activity']['item_id']?>"><?php echo $obj['like_count']?></span></a>
                        <a href="javascript:void(0)" onclick="likeActivity('<?php echo $item_type?>', <?php echo $activity['Activity']['item_id']?>, 0)" id="<?php echo $item_type?>_d_<?php echo $activity['Activity']['item_id']?>" class="comment-thumb <?php if ( !empty( $uid ) && isset( $activity['Likes'][$uid] ) && $activity['Likes'][$uid] == 0 ): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i></a> <span id="<?php echo $item_type?>_dislike_<?php echo $activity['Activity']['item_id']?>"><?php echo $obj['dislike_count']?></span>
					<?php else: ?>				
						&nbsp;<a href="javascript:void(0)" onclick="likeActivity('activity', <?php echo $activity['Activity']['id']?>, 1)" id="activity_l_<?php echo $activity['Activity']['id']?>" class="comment-thumb <?php if ( !empty( $uid ) && !empty( $activity_likes['activity_likes'][$activity['Activity']['id']] ) ): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i></a> 
						<a href="<?php echo $this->request->base?>/likes/ajax_show/activity/<?php echo $activity['Activity']['id']?>" class="overlay" title="<?php echo __('People Who Like This')?>"><span id="activity_like_<?php echo $activity['Activity']['id']?>"><?php echo $activity['Activity']['like_count']?></span></a>
                        <a href="javascript:void(0)" onclick="likeActivity('activity', <?php echo $activity['Activity']['id']?>, 0)" id="activity_d_<?php echo $activity['Activity']['id']?>" class="comment-thumb <?php if ( !empty( $uid ) && isset( $activity_likes['activity_likes'][$activity['Activity']['id']] ) && $activity_likes['activity_likes'][$activity['Activity']['id']] == 0 ): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i></a> <span id="activity_dislike_<?php echo $activity['Activity']['id']?>"><?php echo $activity['Activity']['dislike_count']?></span>
					<?php endif; ?>
				<?php endif; ?>
			<?php else: ?>
				<span class="date"><?php echo $this->Jsnsocial->getTime( $activity['Activity']['created'], $jsnsocial_setting['date_format'], $utz )?></span>
			<?php endif; ?>
		</span>							
	</div>
	
	<ul class="activity_comments" id="comments_<?php echo $activity['Activity']['id']?>" <?php if (empty($activity['ActivityComment']) && empty($activity['Activity']['like_count']) && empty($activity['ItemComment']) && ( $activity['Activity']['params'] != 'item' || empty($obj['like_count']) ) ) echo 'style="display:none"'; ?>>
	<?php 
	// item comments
	if ( !empty($activity['ItemComment']) ):
        if ( count( $activity['ItemComment'] ) >= 2 ):
    ?>
        <li><i class="icon-comments-alt icon-small"></i> <a href="<?php echo $this->request->base?>/<?php echo $item_type?>s/view/<?php echo $activity['Activity']['item_id']?>/<?php echo seoUrl($obj['title'])?>"><?php echo __('View all comments')?></a></li>
    <?php
        endif; 
		foreach ($activity['ItemComment'] as $comment): 
	?>
		<li id="itemcomment_<?php echo $comment['Comment']['id']?>"><?php echo $this->Jsnsocial->getUserAvatar($comment['User'], 32)?>
		    <?php 
            // delete link available for activity poster, site admin and admins array
            if ( $comment['Comment']['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $admins ) && in_array( $uid, $admins ) ) ):
            ?>
            <a href="javascript:void(0)" onclick="return removeItemComment(<?php echo $comment['Comment']['id']?>)" class="cross-icon"><i class="icon-remove"></i></a>
            <?php endif; ?>
			<div class="comment hasDelLink">
				<?php echo $this->Jsnsocial->getName($comment['User'])?>				
				<div class="comment_message truncate" data-more-text="<?php echo __('Show More')?>" data-less-text="<?php echo __('Show Less')?>"><?php echo $this->Jsnsocial->formatText( $comment['Comment']['message'] )?></div>				
				<span class="date">
					<?php echo $this->Jsnsocial->getTime( $comment['Comment']['created'], $jsnsocial_setting['date_format'], $utz )?>					
					&nbsp;<a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo $comment['Comment']['id']?>, 1)" id="comment_l_<?php echo $comment['Comment']['id']?>" class="comment-thumb <?php if ( !empty( $uid ) && !empty( $activity_likes['item_comment_likes'][$comment['Comment']['id']] ) ): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i></a> 
					<a href="<?php echo $this->request->base?>/likes/ajax_show/comment/<?php echo $comment['Comment']['id']?>" class="overlay" title="<?php echo __('People Who Like This')?>"><span id="comment_like_<?php echo $comment['Comment']['id']?>"><?php echo $comment['Comment']['like_count']?></span></a>
                    <a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo $comment['Comment']['id']?>, 0)" id="comment_d_<?php echo $comment['Comment']['id']?>" class="comment-thumb <?php if ( !empty( $uid ) && isset( $activity_likes['item_comment_likes'][$comment['Comment']['id']] ) && $activity_likes['item_comment_likes'][$comment['Comment']['id']] == 0 ): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i></a> <span id="comment_dislike_<?php echo $comment['Comment']['id']?>"><?php echo $comment['Comment']['dislike_count']?></span>
				</span>
			</div>
		</li>
	<?php
		endforeach; 
	endif; 
	?> 
		
	<?php 
	// activity comments
	if (!empty($activity['ActivityComment'])):
        if ( count( $activity['ActivityComment'] ) > 2 ):
    ?>
        <li id="all_comments_<?php echo $activity['Activity']['id']?>"><i class="icon-comments-alt icon-small"></i> <a href="javascript:void(0)" onclick="showAllComments(<?php echo $activity['Activity']['id']?>)"><?php echo __('View all %s comments', count($activity['ActivityComment']))?></a></li>
    <?php
        endif; 
		foreach ($activity['ActivityComment'] as $key => $comment):
			$class = '';
			if ( count($activity['ActivityComment']) > 2 && $key < count($activity['ActivityComment']) - 2 )
				$class = 'hidden';
	?>
		<li id="comment_<?php echo $comment['id']?>" class="<?php echo $class?>"><?php echo $this->Jsnsocial->getUserAvatar($comment['User'], 32)?>
			<?php 
			// delete link available for activity poster, site admin and admins array
			if ( $comment['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $admins ) && in_array( $uid, $admins ) ) ):
			?>
			<a href="javascript:void(0)" onclick="return removeActivityComment(<?php echo $comment['id']?>)" class="cross-icon"><i class="icon-remove"></i></a>
			<?php endif; ?>
			<div class="comment hasDelLink">
				<?php echo $this->Jsnsocial->getName($comment['User'])?>				
				<div class="comment_message truncate" data-more-text="<?php echo __('Show More')?>" data-less-text="<?php echo __('Show Less')?>"><?php echo $this->Jsnsocial->formatText( $comment['comment'] )?></div>				
				<span class="date">
					<?php echo $this->Jsnsocial->getTime( $comment['created'], $jsnsocial_setting['date_format'], $utz )?>					 
					&nbsp;<a href="javascript:void(0)" onclick="likeActivity('activity_comment', <?php echo $comment['id']?>, 1)" id="activity_comment_l_<?php echo $comment['id']?>" class="comment-thumb <?php if ( !empty( $uid ) && !empty( $activity_likes['comment_likes'][$comment['id']] ) ): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i></a> 
					<a href="<?php echo $this->request->base?>/likes/ajax_show/activity_comment/<?php echo $comment['id']?>" class="overlay" title="<?php echo __('People Who Like This')?>"><span id="activity_comment_like_<?php echo $comment['id']?>"><?php echo $comment['like_count']?></span></a>
                    <a href="javascript:void(0)" onclick="likeActivity('activity_comment', <?php echo $comment['id']?>, 0)" id="activity_comment_d_<?php echo $comment['id']?>" class="comment-thumb <?php if ( !empty( $uid ) && isset( $activity_likes['comment_likes'][$comment['id']] ) && $activity_likes['comment_likes'][$comment['id']] == 0 ): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i></a> <span id="activity_comment_dislike_<?php echo $comment['id']?>"><?php echo $comment['dislike_count']?></span>
				</span>
			</div>
		</li>
	<?php
		endforeach;			
	endif; 
	?>
	
	<?php 
	// comment form
	if ( ( !isset($is_member) || $is_member ) && $activity['Activity']['params'] != 'no-comments' && empty( $activity['Content']['Topic']['locked'] ) ): 
	?>			
		<li id="newComment_<?php echo $activity['Activity']['id']?>">
			<?php echo $this->Jsnsocial->getUserAvatar($cuser, 32)?>
			<div class="comment">
				<textarea class="commentBox" onfocus="showCommentButton(<?php echo $activity['Activity']['id']?>)" placeholder="<?php echo __('Write a comment...')?>" id="commentForm_<?php echo $activity['Activity']['id']?>"></textarea>
				<div class="commentButton" id="commentButton_<?php echo $activity['Activity']['id']?>">
					<?php if ( !empty( $uid ) ): ?>					
					<a href="javascript:void(0)" class="button button-tiny button-primary" onclick="<?php if ( $activity['Activity']['params'] == 'item' ): ?>submitItemComment('<?php echo $item_type?>', <?php echo $activity['Activity']['item_id']?>, <?php echo $activity['Activity']['id']?>)<?php else: ?>submitComment(<?php echo $activity['Activity']['id']?>)<?php endif; ?>"> <i class="icon icon-comment-alt"></i> <?php echo __('Comment')?></a>
					<?php else: ?>
					<?php echo __('Please login or register')?>
					<?php endif; ?>
				</div>
			</div>	
		</li>		
	<?php 
	endif; 
	?>
	</ul>
	
</li>
<?php
endforeach;
?>

<?php if (count($activities) >= RESULTS_LIMIT / 2): ?>
    <div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url?>', 'list-content', this)"><?php echo __('Load More')?></a>
    </div>
<?php endif; ?>