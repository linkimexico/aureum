<ul class="list3 friends">	
	<?php foreach ($users as $u): ?>
	<li><?php echo $this->Jsnsocial->getUserAvatar($u['User'])?><br><?php echo $this->Jsnsocial->getName($u['User'], false)?></li>
	</li>
	<?php endforeach; ?>
</ul>