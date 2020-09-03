<div class="summary date">
	<a href="<?php echo $this->request->base?>/blogs/view/<?php echo $activity['Content']['Blog']['id']?>/<?php echo seoUrl($activity['Content']['Blog']['title'])?>"><b><?php echo h($activity['Content']['Blog']['title'])?></b></a><br />
	<?php echo $this->Text->truncate( strip_tags( str_replace( array('<br>','&nbsp;'), array(' ',''), $activity['Content']['Blog']['body'] ) ), 160 )?>
</div>