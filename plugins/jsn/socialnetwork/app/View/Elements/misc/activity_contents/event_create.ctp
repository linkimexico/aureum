<div class="activity_item">
	<a href="<?php echo $this->request->base?>/events/view/<?php echo $activity['Content']['Event']['id']?>/<?php echo seoUrl($activity['Content']['Event']['title'])?>"><img src="<?php echo $this->Jsnsocial->getItemPicture($activity['Content']['Event'], 'events', true)?>" class="img_wrapper2" style="margin-right:10px;padding:2px"></a>
	<a href="<?php echo $this->request->base?>/events/view/<?php echo $activity['Content']['Event']['id']?>/<?php echo seoUrl($activity['Content']['Event']['title'])?>"><b><?php echo h($activity['Content']['Event']['title'])?></b></a><br />
	<div class="date comment_message">
		<?php echo $this->Time->format($activity['Content']['Event']['from'],"%A, %e %B %Y")?> <?php echo $activity['Content']['Event']['from_time']?> - 
		<?php echo $this->Time->format($activity['Content']['Event']['to'],"%A, %e %B %Y")?> <?php echo $activity['Content']['Event']['to_time']?>
		(<?php if (!empty($activity['Content']['User']['timezone'])) echo $activity['Content']['User']['timezone']; else echo $jsnsocial_setting['timezone'];?>)<br />
		<?php echo __('Location')?>: <?php echo h($activity['Content']['Event']['location'])?>
	</div>
</div>