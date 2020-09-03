<?php
echo $this->element('admin/adminnav', array("cmenu" => "roles"));
?>

<div id="center">
    <a href="<?php echo $this->request->base?>/admin/roles/ajax_create" class="overlay button button-action topButton" title="Add New Role">Add New Role</a>
    <h1>Roles Manager</h1>
	<form method="post" action="<?php echo $this->request->base?>/admin/roles/delete" id="deleteForm">
    <table class="jsnsocialTable" cellpadding="0" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Admin</th>
            <th>Super Admin</th>
			<?php if ( $cuser['Role']['is_super'] ): ?>
			<th data-hide="phone" width="30"><input type="checkbox" onclick="toggleCheckboxes(this)"></th>
			<?php endif; ?>
        </tr>
        <?php foreach ($roles as $role): ?>
        <tr>
            <td><?php echo $role['Role']['id']?></td>
            <td><a href="<?php echo $this->request->base?>/admin/roles/ajax_create/<?php echo $role['Role']['id']?>" class="overlay" title="<?php echo h($role['Role']['name'])?> Role"><?php echo h($role['Role']['name'])?></a></td>
            <td><?php echo ($role['Role']['is_admin']) ? 'Yes' : 'No'?></td>
            <td><?php echo ($role['Role']['is_super']) ? 'Yes' : 'No'?></td>
			<?php if ( $cuser['Role']['is_super'] ): ?>
			<td><?php if($role['Role']['id']>3) : ?><input type="checkbox" name="roles[]" value="<?php echo $role['Role']['id']?>" class="check"><?php endif; ?></td>
			<?php endif; ?>
        </tr>
        <?php endforeach ?>
    </table>
	<input type="button" value="Delete" class="topButton button button-caution" style="margin-top: 2px" onclick="confirmSubmitForm('Are you sure you want to delete these roles?<br /><br />', 'deleteForm')">
	</form>
</div>