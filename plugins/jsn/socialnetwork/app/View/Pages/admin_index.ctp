<?php
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
?>

<script>
jQuery(document).ready(function(){
    jQuery( ".jsnsocialTable" ).sortable( {
        items: "tr:not(.tbl_head)", 
        handle: ".reorder",
        update: function(event, ui) {
            var list = jQuery('.jsnsocialTable').sortable('toArray');
            jQuery.post('<?php echo $this->request->base?>/admin/pages/ajax_reorder', { pages: list });
        }
    });
    
    jQuery('.footable').footable();
});
</script>

<?php echo $this->element('admin/adminnav', array("cmenu" => "pages"));?>

<div id="center">	
    <a href="<?php echo $this->request->base?>/admin/pages/create" class="button button-action topButton"><?php echo __('Create New Page')?></a>
	<h1>Pages Manager</h1>
	
	<table class="jsnsocialTable footable" cellpadding="0" cellspacing="0">
		<thead>
		<tr class="tbl_head">
			<th width="50px"><?php echo $this->Paginator->sort('Page.id', 'ID'); ?></th>
			<th><?php echo $this->Paginator->sort('Page.title', 'Title'); ?></th>
			<th><?php echo $this->Paginator->sort('Page.alias', 'Alias'); ?></th>
			<th data-hide="phone"><?php echo $this->Paginator->sort('Page.menu', 'Menu'); ?></th>
			<th data-hide="phone"><?php echo $this->Paginator->sort('Page.modified', 'Last Updated'); ?></th>
			<th data-hide="phone" width="50px">Actions</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($pages as $page):
		?>
		<tr id="<?php echo $page['Page']['id']?>">
			<td width="50px"><?php echo $page['Page']['id']?></td>
			<td><a href="<?php echo $this->request->base?>/admin/pages/create/<?php echo $page['Page']['id']?>"><?php echo $page['Page']['title']?></a></td>
			<td class="reorder"><?php echo $page['Page']['alias']?></td>
			<td class="reorder"><?php if ($page['Page']['menu']) echo 'Yes'; else echo 'No'; ?></td>
			<td class="reorder"><?php echo $this->Jsnsocial->getTime($page['Page']['modified'], $jsnsocial_setting['date_format'], $utz)?></td>
			<td width="70">
			    <a href="<?php echo $this->request->base?>/pages/<?php echo $page['Page']['alias']?>" target="_blank" class="tip" title="View"><i class="icon-file-alt icon-small"></i></a>&nbsp;
			    <a href="javascript:void(0)" class="tip" title="Delete" onclick="jsnsocialConfirm('Are you sure you want to delete this page?', '<?php echo $this->request->base?>/admin/pages/delete/<?php echo $page['Page']['id']?>')"><i class="icon-trash icon-small"></i></a>
			</td>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>
</div>

<div class="pagination">
	<?php echo $this->Paginator->prev('« Previous', null, null, array('class' => 'disabled')); ?>
	<?php echo $this->Paginator->numbers(); ?>
	<?php echo $this->Paginator->next('Next »', null, null, array('class' => 'disabled')); ?> 
</div>