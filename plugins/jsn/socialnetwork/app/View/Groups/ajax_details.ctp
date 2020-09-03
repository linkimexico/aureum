<h3>Information</h3>
<ul class="list6 info">
	<li><label>Type:</label><?php if ($group['Group']['type'] == 1) echo 'Public (anyone can view and join)'; else echo 'Private (only group members can view)';?></li>
	<?php if ($group['Group']['type'] == 1 || (!empty($my_membership) && ($my_membership['GroupUser']['status'] == 1 || $my_membership['GroupUser']['status'] == 3))): ?>
	<li><label>Description:</label><div><?php echo nl2br($group['Group']['description'])?></div></li>
	<?php endif; ?>
</ul>

<?php if (!empty($photos)): ?>
<h3>Photos</h3>
<ul class="list4 trip_photos">
<?php foreach ($photos as $photo): ?>
	<li><a href="/photos/view/<?php echo $photo['Photo']['id']?>"><img src="/<?php echo $photo['Photo']['thumb']?>"></a></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php 
if ( $group['Group']['type'] == 1 || 
	 (!empty($my_membership) && ($my_membership['GroupUser']['status'] == 1 || $my_membership['GroupUser']['status'] == 3))
   ):
?>
	<h3>Comments</h3>
	<?php
	if (!empty($my_membership) && ($my_membership['GroupUser']['status'] == 1 || $my_membership['GroupUser']['status'] == 3)) echo 			$this->element('comment_form', array('target_id' => $group['Group']['id'], 'type' => 'group', 'text' => 'Write a comment...', 'desc' => 1));
	?>

	<ul class="list6 comment_wrapper" id="list-content">
		<?php echo $this->element('activities', array('activities' => $activities)); ?>
	</ul>
<?php endif; ?>