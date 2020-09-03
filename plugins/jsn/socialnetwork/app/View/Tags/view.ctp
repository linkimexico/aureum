<div id="leftnav">
	<?php echo $this->element('blocks/tags_block'); ?>
</div>

<div id="center">
	<?php if ( !empty( $items ) ): ?>
	<ul class="list7" style="float:right;">
		<li><a href="<?php echo $this->request->base?>/tags/view/<?php echo $tag?>" <?php if ( $order != 'like_count' ) echo 'class="current"'; ?>><?php echo __('Latest')?></a></li>
		<li><a href="<?php echo $this->request->base?>/tags/view/<?php echo $tag?>/popular" <?php if ( $order == 'like_count' ) echo 'class="current"'; ?>><?php echo __('Popular')?></a></li>
	</ul>
	<?php endif; ?>
	
	<h1><?php echo __('Tag')?> "<?php echo $tag?>"</h1>
	
	<?php if ( !empty( $items ) ): ?>
	<ul class="list6 comment_wrapper" id="list-content">
		<?php 
		foreach ($items as $item): 
			if ( $unions == 1 )
				$item[0] = array_merge( $item[0], $item['i'] );
			
			$item[0]['name']=JsnHelper::getUser($item[0]['user_id'])->getFormatName();
			
			switch ( $item[0]['type'] )
			{
				case 'topic':
				case 'blog':
					$thumb = $this->Jsnsocial->getUserPicture( JsnHelper::getUser($item[0]['user_id'])->avatar );
					break;
				
				case 'album':
					$thumb = $this->Jsnsocial->getAlbumCover( $item[0]['thumb'] );
					break;
					
				case 'video':
					$thumb = $this->request->webroot . 'uploads/videos/' . $item[0]['thumb'];
					break;
			}
		?>
		<li>				
			<a href="<?php echo $this->request->base?>/<?php echo $item[0]['type']?>s/view/<?php echo $item[0]['id']?>"><img src="<?php echo $thumb?>" title="<?php echo $item[0]['type']?>" style="width:36px;float:left" class="img_wrapper2"></a>
			<div style="margin-left:50px">
				<a href="<?php echo $this->request->base?>/<?php echo $item[0]['type']?>s/view/<?php echo $item[0]['id']?>"><b><?php echo h($item[0]['title'])?></b></a><br />
				<div class="comment_message date">
					<?php echo __('Posted by')?> <a href="<?php echo $this->request->base?>/users/view/<?php echo $item[0]['user_id']?>"><?php echo h($item[0]['name'])?></a> <?php echo $this->Jsnsocial->getTime($item[0]['created'], $jsnsocial_setting['date_format'], $utz)?> . 
					<?php echo __n( '%s like', '%s likes', $item[0]['like_count'], $item[0]['like_count'] )?>
				</div>
			</div>	
		</li>
		<?php endforeach; ?>
	</ul>
	<?php else: ?>
		<div align="center"><?php echo __('Nothing found')?></div>
	<?php endif; ?>
</div>