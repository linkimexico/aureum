<?php
echo $this->Html->css(array('footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('footable'), array('inline' => false));

echo $this->element('admin/adminnav', array("cmenu" => "blogs"));
$this->Paginator->options(array('url' => $this->passedArgs));
?>

<script>
jQuery(document).ready(function(){
	jQuery('.footable').footable();
});
</script>

<div id="center">
	<form method="post" action="<?php echo $this->request->base?>/admin/blogs">
	<?php echo $this->Form->text('keyword', array('style' => 'float:right', 'placeholder' => 'Search by title'));?>
	<?php echo $this->Form->submit('', array( 'style' => 'display:none' ));?>
	</form>
	
	<h1>Blogs Manager</h1>
	<form method="post" action="<?php echo $this->request->base?>/admin/blogs/delete" id="deleteForm">
	<table class="jsnsocialTable footable" cellpadding="0" cellspacing="0">
		<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('id', 'ID'); ?></th>
			<th><?php echo $this->Paginator->sort('title', 'Title'); ?></th>
			<th data-hide="phone"><?php echo $this->Paginator->sort('User.name', 'Author'); ?></th>
			<th data-hide="phone"><?php echo $this->Paginator->sort('created', 'Date'); ?></th>
			<?php if ( $cuser['Role']['is_super'] ): ?>
			<th width="30"><input type="checkbox" onclick="toggleCheckboxes(this)"></th>
			<?php endif; ?>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($blogs as $blog): ?>
		<tr>
			<td><?php echo $blog['Blog']['id']?></td>
			<td><a href="<?php echo $this->request->base?>/blogs/create/<?php echo $blog['Blog']['id']?>" target="_blank"><?php echo h($blog['Blog']['title'])?></a></td>
			<td><a href="<?php echo $this->request->base?>/admin/users/edit/<?php echo $blog['User']['id']?>"><?php echo h($blog['User']['name'])?></a></td> 
			<td><?php echo $this->Time->niceShort($blog['Blog']['created'])?></td>
			<?php if ( $cuser['Role']['is_super'] ): ?>
			<td><input type="checkbox" name="blogs[]" value="<?php echo $blog['Blog']['id']?>" class="check"></td>
			<?php endif; ?>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>
	
	<input type="button" value="Delete" class="topButton button button-caution" style="margin-top: 2px" onclick="confirmSubmitForm('Are you sure you want to delete these entries', 'deleteForm')">
	</form>
</div>

<div class="pagination">
	<?php echo $this->Paginator->prev('« Previous', null, null, array('class' => 'disabled')); ?>
	<?php echo $this->Paginator->numbers(); ?>
	<?php echo $this->Paginator->next('Next »', null, null, array('class' => 'disabled')); ?> 
</div>