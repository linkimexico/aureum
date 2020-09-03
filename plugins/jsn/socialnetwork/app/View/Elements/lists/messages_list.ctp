<?php 
if ( !empty($conversations) )
{
	foreach ($conversations as $conversation): ?>
	<li <?php if ($conversation['ConversationUser']['unread']) echo 'class="unread"';?>>
		<a href="<?php echo $this->request->base?>/conversations/view/<?php echo $conversation['Conversation']['id']?>"><img <?php if(empty(JsnHelper::getUser($conversation['Conversation']['LastPoster']['id'])->avatar_clean)) echo 'avatar="'.$conversation['Conversation']['LastPoster']['name'].'"'; ?> src="<?php echo $this->Jsnsocial->getUserPicture($conversation['Conversation']['LastPoster']['avatar'])?>" class="img_wrapper2"></a>
		<div class="comment">
			<a href="<?php echo $this->request->base?>/conversations/view/<?php echo $conversation['Conversation']['id']?>"><b><?php echo h($conversation['Conversation']['subject'])?></b></a>
			<div class="comment_message"><?php echo h($this->Text->truncate($conversation['Conversation']['message'], 85))?></div>
			<span class="date">
				<?php echo $conversation['Conversation']['message_count']?> <?php echo __n( 'message', 'messages', $conversation['Conversation']['message_count'] )?> . 
				<?php echo __('Participants')?>:
				<?php 
				$i = 1;
                $count = count( $conversation['Conversation']['ConversationUser'] );
				foreach ( $conversation['Conversation']['ConversationUser'] as $user ):
				    echo $this->Jsnsocial->getName( $user['User'], false );
                    $remaining = $count - $i;
                    
                    if ( $i == $count )
                        break; 
                    elseif ( $i >= 3 && ( $remaining > 0  ) )
                    {
                        printf(__(' and %s others'), $remaining);
                        break;
                    }
                    else
                        echo ', ';
                    
                    $i++;
                endforeach; 
                ?>
			</span>
		</div>
	</li>
<?php 
	endforeach;
}
else
	echo '<div align="center" style="margin-top:10px">' . __('No more results found') . '</div>';
?>

<?php if (count($conversations) >= RESULTS_LIMIT): ?>
    <div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url?>', 'list-content', this)"><?php echo __('Load More')?></a>
    </div>
<?php endif; ?>