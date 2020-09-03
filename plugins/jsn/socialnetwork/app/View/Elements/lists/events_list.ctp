<?php 
if (count($events) > 0):
	foreach ($events as $event):
?>
	<li><a href="<?php echo $this->request->base?>/events/view/<?php echo $event['Event']['id']?>/<?php echo seoUrl($event['Event']['title'])?>"><img src="<?php echo $this->Jsnsocial->getItemPicture($event['Event'], 'events', true)?>" class="img_wrapper2" style="width:50px"></a>
		<div class="event-date"><?php echo $this->Time->format($event['Event']['from'],'%b %d')?></div>
		<div class="comment">
			<a href="<?php echo $this->request->base?>/events/view/<?php echo $event['Event']['id']?>/<?php echo seoUrl($event['Event']['title'])?>"><b><?php echo h($event['Event']['title'])?></b></a>
			<div class="comment_message"><?php echo h($event['Event']['location'])?></div>
			<span class="date"><?php echo __('%s attending', $event['Event']['event_rsvp_count'])?></span>
		</div>	
	</li>
<?php 
	endforeach;
else:
	echo '<div align="center">' . __('No more results found') . '</div>';
endif;
?>

<?php
if (count($events) >= RESULTS_LIMIT):
?>
	<div class="view-more">
        <a href="javascript:void(0)" onclick="moreResults('<?php echo $more_url?>', 'list-content', this)"><?php echo __('Load More')?></a>
    </div>
<?php
endif;
?>
