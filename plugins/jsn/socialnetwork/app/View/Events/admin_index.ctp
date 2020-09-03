<?php
echo $this->Html->css(array('footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('footable'), array('inline' => false));

echo $this->element('admin/adminnav', array("cmenu" => "events"));
$this->Paginator->options(array('url' => $this->passedArgs));
?>

<script>
jQuery(document).ready(function(){
	jQuery('.footable').footable();
});
</script>

<div id="center">
	<form method="post" action="<?php echo $this->request->base?>/admin/events">
	<?php echo $this->Form->text('keyword', array('style' => 'float:right', 'placeholder' => 'Search by title'));?>
	<?php echo $this->Form->submit('', array( 'style' => 'display:none' ));?>
	</form>
	
	<h1>Events Manager</h1>
	<form method="post" action="<?php echo $this->request->base?>/admin/events/delete" id="deleteForm">
	<table class="jsnsocialTable footable" cellpadding="0" cellspacing="0">
		<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('id', 'ID'); ?></th>
			<th><?php echo $this->Paginator->sort('title', 'Title'); ?></th>
			<th data-hide="phone"><?php echo $this->Paginator->sort('User.name', 'Creator'); ?></th>
			<th data-hide="phone"><?php echo $this->Paginator->sort('Category.name', 'Category'); ?></th>
			<th data-hide="phone"><?php echo $this->Paginator->sort('created', 'Date'); ?></th>
			<?php if ( $cuser['Role']['is_super'] ): ?>
			<th width="30"><input type="checkbox" onclick="toggleCheckboxes(this)"></th>
			<?php endif; ?>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($events as $event): ?> 
		<tr>
			<td><?php echo $event['Event']['id']?></td>
			<td><a href="<?php echo $this->request->base?>/events/create/<?php echo $event['Event']['id']?>" target="_blank"><?php echo h($event['Event']['title'])?></a></td>
			<td><a href="<?php echo $this->request->base?>/admin/users/edit/<?php echo $event['User']['id']?>"><?php echo h($event['User']['name'])?></a></td>
			<td><?php echo $event['Category']['name']?></td>
			<td><?php echo $this->Time->niceShort($event['Event']['created'])?></td>
			<?php if ( $cuser['Role']['is_super'] ): ?>
			<td><input type="checkbox" name="events[]" value="<?php echo $event['Event']['id']?>" class="check"></td>
			<?php endif; ?>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>
	
	<div style="float:right">
        <select onchange="doModeration(this.value, 'events')">
            <option value="">With selected...</option>
            <option value="move">Move to</option>
            <option value="delete">Delete</option>
        </select>
        <?php echo $this->Form->select('category_id', $categories, array( 'onchange' => "confirmSubmitForm('Are you sure you want to move these events', 'deleteForm')", 'style' => 'display:none' ) ); ?>
    </div>
	</form>
	
	<div class="pagination">
        <?php echo $this->Paginator->prev('« Previous', null, null, array('class' => 'disabled')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next('Next »', null, null, array('class' => 'disabled')); ?> 
    </div>
</div>