<div class="activity_item">
	<a href="<?php echo $this->request->base?>/groups/view/<?php echo $activity['Content']['Group']['id']?>/<?php echo seoUrl($activity['Content']['Group']['name'])?>"><img src="<?php echo $this->Jsnsocial->getItemPicture($activity['Content']['Group'], 'groups', true)?>" class="img_wrapper2" style="margin-right:10px;padding:2px"></a>
	<a href="<?php echo $this->request->base?>/groups/view/<?php echo $activity['Content']['Group']['id']?>/<?php echo seoUrl($activity['Content']['Group']['name'])?>"><b><?php echo h($activity['Content']['Group']['name'])?></b></a><br />
	<div class="date comment_message">
		<?php echo h($this->Text->truncate($activity['Content']['Group']['description'], 125))?>
	</div>
</div>