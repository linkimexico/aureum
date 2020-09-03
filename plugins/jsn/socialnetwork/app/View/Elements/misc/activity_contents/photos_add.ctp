<ul class="list4 activity_content p_photos">
<?php foreach ( $activity['Content'] as $photo ): ?>
	<li style="background-image:url(<?php echo $this->request->webroot?><?php echo $photo['Photo']['thumb']?>);">
	   <a href="<?php echo $this->request->base?>/photos/view/<?php echo $photo['Photo']['id']?>#content"></a>	   
	</li>					
<?php endforeach; ?>
</ul>