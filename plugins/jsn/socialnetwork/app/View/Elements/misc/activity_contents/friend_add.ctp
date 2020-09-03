<?php
$activity['Content'] = array_slice($activity['Content'], 0, 10);
?>

<ul class="list5 activity_content">
<?php foreach ( $activity['Content'] as $u ): ?>
	<li><?php echo $this->Jsnsocial->getUserAvatar($u['User'], 25, 'tip')?></li>
<?php endforeach; ?>
</ul>