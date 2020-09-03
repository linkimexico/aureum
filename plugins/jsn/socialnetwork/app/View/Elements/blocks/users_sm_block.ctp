<?php if ( !empty( $users ) ): ?>
<ul class="list5">	
	<?php foreach ($users as $u): ?>
	<li><?php echo $this->Jsnsocial->getUserAvatar($u['User'], 25, 'tip')?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>