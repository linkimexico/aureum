<?php
if (count($photos) > 0)
{
	if ( empty($page) || $page == 1 )
		if ( !empty( $type ) && $type == APP_GROUP || !empty( $param ) )
			echo '<ul class="list4 p_photos2" id="list-content">';
		else
			echo '<ul class="list4" id="list-content">';
	
	foreach ($photos as $photo):
?>
	<li>
		<a style="background-image:url(<?php echo $this->request->webroot?><?php echo $photo['Photo']['thumb']?>);" href="<?php echo $this->request->base?>/photos/view/<?php echo $photo['Photo']['id']?>#content">
	        <div class="infoLayer">
	           <p><i class="icon-thumbs-up-alt"></i> <?php echo $photo['Photo']['like_count']?>  
                  <i class="icon-thumbs-down-alt"></i> <?php echo $photo['Photo']['dislike_count']?>
               </p> 
	        </div>	
		</a>
	</li>
<?php
	endforeach;
	
	if (count($photos) >= RESULTS_LIMIT):
?>
	<div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url?>', 'list-content', this)"><?php echo __('Load More')?></a>
    </div>
<?php
	endif;	
	
	if ( empty($page) || $page == 1 )
		echo '</ul>';
}
else
	echo '<div align="center" style="width:100%;overflow:hidden">' . __('No more results found') . '</div>';
?>