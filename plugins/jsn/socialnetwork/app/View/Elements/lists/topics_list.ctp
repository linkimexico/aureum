<?php
if (count($topics) > 0)
{	
    $i = 1;
	foreach ($topics as $topic):
?>
	<li <?php if( $i == count($topics) ) echo 'style="border-bottom:0"'; ?>><a href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_view/<?php echo $topic['Topic']['id']?>')"<?php else: ?>"<?php echo $this->request->base?>/topics/view/<?php echo $topic['Topic']['id']?>/<?php echo seoUrl($topic['Topic']['title'])?>"<?php endif; ?>><i class="<?php if($topic['User']['online']) echo 'ep_online'; else echo 'ep_offline';?>"></i><img <?php if(empty($topic['User']['avatar_clean'])) echo 'avatar="'.$topic['User']['name'].'"'; ?> src="<?php echo $this->Jsnsocial->getUserPicture($topic['User']['avatar'])?>" class="img_wrapper2"></a>
		<div class="topics_count"><?php echo $topic['Topic']['comment_count']?></div>
		<div class="comment">
			<a href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_view/<?php echo $topic['Topic']['id']?>')"<?php else: ?>"<?php echo $this->request->base?>/topics/view/<?php echo $topic['Topic']['id']?>/<?php echo seoUrl($topic['Topic']['title'])?>"<?php endif; ?>><b><?php echo h($topic['Topic']['title'])?></b></a>
			&nbsp;
			<?php if ( $topic['Topic']['pinned'] ): ?>
			<i class="icon-pushpin icon-small tip" title="<?php echo __('Pinned')?>"></i>
			<?php endif; ?>
			<?php if ( $topic['Topic']['attachment'] ): ?>
            <i class="icon-paper-clip icon-small tip" title="<?php echo __('Attached files')?>"></i>
            <?php endif; ?>
			<?php if ( $topic['Topic']['locked'] ): ?>
            <i class="icon-lock icon-small tip" title="<?php echo __('Locked')?>"></i>
            <?php endif; ?>
			<div class="comment_message">
    			<?php echo ($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $topic['Topic']['body'])), 85))?>
    		    <div class="date">
    				<?php echo __('Last posted by %s', $this->Jsnsocial->getName($topic['LastPoster'], false))?>
    				<?php echo $this->Jsnsocial->getTime( $topic['Topic']['last_post'], $jsnsocial_setting['date_format'], $utz )?>
    			</div>
    		</div>
		</div>	
	</li>
<?php 
    $i++;
	endforeach;
}
else
	echo '<div align="center">' . __('No more results found') . '</div>';
?>

<?php if (count($topics) >= RESULTS_LIMIT): ?>
	<div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url?>', 'list-content', this)"><?php echo __('Load More')?></a>
    </div>
<?php endif; ?>