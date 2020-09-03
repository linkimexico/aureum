<script>
jQuery(document).ready(function(){
	jQuery("#comments li").hover(
		function () {
		jQuery(this).contents('.cross-icon').show();
	  }, 
	  function () {
		jQuery(this).contents('.cross-icon').hide();
	  }
	);
});

function removeItemComment(id)
{
    jQuery.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',        
        callback: function(){
            jQuery.post('<?php echo $this->request->base?>/comments/ajax_remove', {id: id}, function() {
                jQuery('#itemcomment_'+id).fadeOut('normal', function() {
                    jQuery('#itemcomment_'+id).remove();
                    jQuery('#comment_count').html( parseInt(jQuery('#comment_count').html()) - 1 );
                }); 
            });
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: "<?php echo addslashes(__('Are you sure you want to remove this comment?'))?>",
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
}
</script>

<?php if (count($comments) >= RESULTS_LIMIT): ?>
	<div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_comments?>', 'comments', this)"><?php echo __('Load More')?></a>
    </div>
<?php endif; ?>	
	
<?php 
if ( !empty( $comments ) ):
	foreach ($comments as $comment):
?>
	<li id="itemcomment_<?php echo $comment['Comment']['id']?>" style="position: relative">
		<?php 
		// delete link available for commenter, site admin and item author (except convesation)
		if ( ( $this->request->controller != Inflector::pluralize(APP_CONVERSATION) ) && ( $comment['Comment']['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $admins ) && in_array( $uid, $admins ) ) ) ):
		?>
		<a href="javascript:void(0)" onclick="return removeItemComment(<?php echo $comment['Comment']['id']?>)" class="cross-icon"><i class="icon-remove"></i></a>
		<?php endif; ?>
		    
		<?php echo $this->Jsnsocial->getUserAvatar($comment['User'])?>		
		<div class="comment hasDelLink">
			<?php echo $this->Jsnsocial->getName($comment['User'])?>
			<div class="comment_message"><?php echo $this->Jsnsocial->formatText( $comment['Comment']['message'], false, true )?></div>
			
			<span class="date">
				<?php echo $this->Jsnsocial->getTime( $comment['Comment']['created'], $jsnsocial_setting['date_format'], $utz )?>
			</span>
			
			<?php if ( $this->request->controller != Inflector::pluralize(APP_CONVERSATION) ): ?> 
            <a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo $comment['Comment']['id']?>, 1)" id="comment_l_<?php echo $comment['Comment']['id']?>" class="comment-thumb <?php if ( !empty( $uid ) && !empty( $comment_likes[$comment['Comment']['id']] ) ): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i></a> 
            <a href="<?php echo $this->request->base?>/likes/ajax_show/comment/<?php echo $comment['Comment']['id']?>" class="overlay" title="<?php echo __('People Who Like This')?>"><span id="comment_like_<?php echo $comment['Comment']['id']?>"><?php echo $comment['Comment']['like_count']?></span></a>
            
            <a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo $comment['Comment']['id']?>, 0)" id="comment_d_<?php echo $comment['Comment']['id']?>" class="comment-thumb <?php if ( !empty( $uid ) && isset( $comment_likes[$comment['Comment']['id']] ) && $comment_likes[$comment['Comment']['id']] == 0 ): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i></a> 
            <span id="comment_dislike_<?php echo $comment['Comment']['id']?>"><?php echo $comment['Comment']['dislike_count']?></span>
            <?php endif; ?> 
		</div>
	</li>
<?php
	endforeach;
endif;
?>