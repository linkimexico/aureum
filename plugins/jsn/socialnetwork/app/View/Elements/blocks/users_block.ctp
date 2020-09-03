<ul class="list3">
<?php
foreach ($users as $user): ?>
	<li><i class="<?php if($user['User']['online']) echo 'ep_online'; else echo 'ep_offline';?>"></i><?php echo $this->Jsnsocial->getUserAvatar($user['User'], 33, 'tip')?></li>
<?php
endforeach; ?>
</ul>