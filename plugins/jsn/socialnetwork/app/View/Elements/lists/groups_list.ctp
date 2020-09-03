<?php 
if (count($groups) > 0):
    $i = 1;
	foreach ($groups as $group):
?>
	<li <?php if( $i == count($groups) ) echo 'style="border-bottom:0"'; ?>><a href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>/<?php echo seoUrl($group['Group']['name'])?>"><img src="<?php echo $this->Jsnsocial->getItemPicture($group['Group'], 'groups', true)?>" class="img_wrapper2" style="width:50px"></a>
		<div class="comment">
			<a href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>/<?php echo seoUrl($group['Group']['name'])?>"><b><?php echo h($group['Group']['name'])?></b></a>
			<div class="comment_message"><?php echo h($this->Text->truncate($group['Group']['description'], 80))?></div>
			<span class="date">
				<?php 
				switch ( $group['Group']['type'] )
				{
					case PRIVACY_PUBLIC:
						echo __('Public'); 
						break;
						
					case PRIVACY_RESTRICTED:
						echo __('Restricted'); 
						break;
						
					case PRIVACY_PRIVATE:
						echo __('Private'); 
						break;
				}
				?> . 
				<?php echo __n( '%s member', '%s members', $group['Group']['group_user_count'], $group['Group']['group_user_count'] )?>
			</span>
		</div>	
	</li>
<?php 
    $i++;
	endforeach;
else:
	echo '<div align="center">' . __('No more results found') . '</div>';
endif;
?>

<?php
if (count($groups) >= RESULTS_LIMIT):
?>
	<div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url?>', 'list-content', this)"><?php echo __('Load More')?></a>
    </div>
<?php
endif;
?>
