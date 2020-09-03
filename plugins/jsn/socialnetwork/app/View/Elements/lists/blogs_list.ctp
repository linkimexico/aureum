<?php if ( !empty( $user_blog ) ): ?>
<script>
jQuery(document).ready(function(){
	registerImageOverlay();
});
</script>
<?php endif; ?>

<?php
if (count($blogs) > 0)
{
	$i = 1;
	foreach ($blogs as $blog):
?>
	<li <?php if( $i == count($blogs) ) echo 'style="border-bottom:0"'; ?>><a href="<?php echo $this->request->base?>/blogs/view/<?php echo $blog['Blog']['id']?>/<?php echo seoUrl($blog['Blog']['title'])?>"><i class="<?php if($blog['User']['online']) echo 'ep_online'; else echo 'ep_offline';?>"></i><img <?php if(empty($blog['User']['avatar_clean'])) echo 'avatar="'.$blog['User']['name'].'"'; ?> src="<?php echo $this->Jsnsocial->getUserPicture($blog['User']['avatar'])?>" class="img_wrapper2"></a>
		<div class="topics_count"><?php echo $blog['Blog']['comment_count']?></div>
		<div class="comment">
			<a href="<?php echo $this->request->base?>/blogs/view/<?php echo $blog['Blog']['id']?>/<?php echo seoUrl($blog['Blog']['title'])?>"><b><?php echo h($blog['Blog']['title'])?></b></a> 			
			<div class="comment_message">
				<?php 
				if ( !empty( $user_blog ) )
					echo $this->Jsnsocial->cleanHtml( $blog['Blog']['body'] );
				else
					echo ($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $blog['Blog']['body'])), 425));
				?>
				<div class="date">
    				<?php echo __('Posted by')?> <?php echo $this->Jsnsocial->getName($blog['User'], false)?> 
    				<?php echo $this->Jsnsocial->getTime( $blog['Blog']['created'], $jsnsocial_setting['date_format'], $utz )?> . <a href="<?php echo $this->request->base?>/blogs/view/<?php echo $blog['Blog']['id']?>/<?php echo seoUrl($blog['Blog']['title'])?>"><?php echo __('Read more')?></a>
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

<?php if (count($blogs) >= RESULTS_LIMIT): ?>
    <div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url?>', 'list-content', this)"><?php echo __('Load More')?></a>
    </div>
<?php endif; ?>