<?php 
if (!empty($videos)): 
?>
<ul class="list6 list6sm">
<?php foreach ($videos as $video): ?>
	<li><a href="<?php echo $this->request->base?>/videos/view/<?php echo $video['Video']['id']?>/<?php echo seoUrl($video['Video']['title'])?>"><img src="<?php echo $this->request->webroot?>uploads/videos/<?php echo $video['Video']['thumb']?>" class="img_wrapper2" style="width:50px"></a>
		<div style="margin-left: 60px;">
			<a href="<?php echo $this->request->base?>/videos/view/<?php echo $video['Video']['id']?>/<?php echo seoUrl($video['Video']['title'])?>"><?php echo h($this->Text->truncate($video['Video']['title'], 40))?></a><br />
			<span class="date"><?php echo __n( '%s like', '%s likes', $video['Video']['like_count'], $video['Video']['like_count'] )?></span>
		</div>
	</li>
<?php endforeach; ?>
</ul>
<?php 
else:
	echo __('Nothing found');
endif; 
?>