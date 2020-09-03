<?php 
if (!empty($blogs)): 
?>
<ul class="list6 list6sm">
<?php foreach ($blogs as $blog): ?>
	<li><a href="<?php echo $this->request->base?>/blogs/view/<?php echo $blog['Blog']['id']?>/<?php echo seoUrl($blog['Blog']['title'])?>"><?php echo h($blog['Blog']['title'])?></a><br />
		<span class="date">
			<?php echo __n( '%s comment', '%s comments', $blog['Blog']['comment_count'], $blog['Blog']['comment_count'] )?> . 
			<?php echo __n( '%s like', '%s likes', $blog['Blog']['like_count'], $blog['Blog']['like_count'] )?>
		</span>
	</li>
<?php endforeach; ?>
</ul>
<?php 
else:
	echo __('Nothing found');
endif; 
?>