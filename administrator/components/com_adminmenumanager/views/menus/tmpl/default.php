<?php
/**
* @package Admin-Menu-Manager (com_adminmenumanager)
* @version 2.3.2
* @copyright Copyright (C) 2012 - 2019 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/



// no direct access.
defined('_JEXEC') or die;

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

$saveOrder 	= ($listOrder == 'a.ordering' && $listDirn == 'asc');
if ($saveOrder && $this->helper->joomla_version >= '3.0'){
	$saveOrderingUrl = 'index.php?option=com_adminmenumanager&task=save_order_ajax_menus&tmpl=component';
	JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

?>
<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){
	if (task == 'menu') {		
		<?php
		if($this->controller->get_version_type()=='free'){
			echo 'alert(\''.addslashes(JText::_('COM_ADMINMENUMANAGER_LIMITED_MENUS')).'\');';
			echo 'return;';
		}else{
			echo 'document.location.href = \'index.php?option=com_adminmenumanager&view=menu&id=0\';';
		}
		?>			
	}	
	if (task == 'menu_delete') {
		<?php
		if($this->controller->get_version_type()=='free'){
			echo 'alert(\''.addslashes(JText::_('COM_ADMINMENUMANAGER_LIMITED_MENUS').'. '.JText::_('COM_ADMINMENUMANAGER_DONT_DELETE_LAST_MENU')).'.\');';
			echo 'return;';
		}
		?>
		if (document.adminForm.boxchecked.value == '0') {						
			alert('<?php echo addslashes(JText::_('COM_MENUS_NO_MENUS_SELECTED')); ?>');
			return;
		} else {
			if(confirm("<?php echo addslashes(JText::_('COM_ADMINMENUMANAGER_SURE_DELETE_MENUS')); ?>?")){
				submitform('menu_delete');
			}
		}
	}
	if (task == 'menus_copy') {		
		<?php
		if($this->controller->get_version_type()=='free'){
			echo 'alert(\''.addslashes(JText::_('COM_ADMINMENUMANAGER_LIMITED_MENUS')).'\');';
			echo 'return;';
		}else{
		?>
			if (document.adminForm.boxchecked.value == '0') {						
				alert('<?php echo addslashes(JText::_('COM_MENUS_NO_MENUS_SELECTED')); ?>');
				return;
			} else {
				submitform('menus_copy');
			}
		<?php
		}
		?>
	}
}

function reorder_listitem(id, direction){
	document.adminForm.reorder_id.value = id;
	document.adminForm.reorder_direction.value = direction;	
	submitform('reorder_items');
}

Joomla.orderTable = function(){
	if(document.getElementById("sortTable")){
		sort_table = document.getElementById("sortTable").value;
	}else{
		sort_table = document.adminForm.filter_order.value;
	}
	if(document.getElementById("directionTable")){
		direction_table = document.getElementById("directionTable").value;
	}else{
		direction_table = document.adminForm.filter_order_Dir.value;
	}	
	Joomla.tableOrdering(sort_table, direction_table, '');	
}

</script>
<form action="<?php echo JRoute::_('index.php?option=com_adminmenumanager&view=menus');?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="reorder_id" value="" />
	<input type="hidden" name="reorder_direction" value="" />	
	<input type="hidden" name="reorder_view" value="menus" />	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="go_to_menu" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<?php if (!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>	
	<div id="j-main-container"<?php echo empty($this->sidebar) ? '' : ' class="span10"'; ?>>
		<?php
		if($this->controller->get_version_type()=='free'){
			echo '<div class="amm_red amm_page_title">';
			echo JText::_('COM_ADMINMENUMANAGER_LIMITED_MENUS').'.';
			echo '</div>';
		}
		?>	
		<fieldset id="filter-bar">
			<?php	
			
			//search bar						
			$sortfields = JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);			
			echo $this->helper->search_toolbar(1, 1, 1, 1, $this->state->get('filter.search'), $sortfields, $listDirn, $this->pagination->getLimitBox());	
			
			?>	
		</fieldset>
		<div class="clr"> </div>	
		<table class="adminlist table table-striped" width="100%" id="itemList">
			<thead>
				<tr>
					<?php
					if($this->helper->joomla_version >= '3.0'){
					?>
					<th width="5" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<?php
					}
					?>
					<th width="5" align="left">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>						
					<th class="left">
						<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th>
					</th>
					<th class="left">
						<?php echo JText::_('JGLOBAL_DESCRIPTION'); ?>
					</th>	
					<?php
					if($this->helper->joomla_version < '3.0'){
					?>
					<th class="center">
						<div style="width: 180px; margin: 0 auto;">
							<?php 											
								echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ORDERING'), 'a.ordering', $listDirn, $listOrder);
							?>				
							<a href="javascript:submitform('items_order_save');" class="saveorder" title="Save Order"></a>
						</div>				
					</th>	
					<?php
					}
					?>										
					<th class="nowrap" width="3%">
						<?php echo JHtml::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>			
			</thead>		
			<tbody>		
			<?php 		
			foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo ($i+1) % 2; ?>">
					<?php
					if($this->helper->joomla_version >= '3.0'){
					?>
					<td class="order nowrap center hidden-phone">
						<?php 						
						if($saveOrder){
							$disableClassName = '';
							$disabledLabel = '';
						}else{
							$disabledLabel = JText::_('JORDERINGDISABLED');
							$disableClassName = 'inactive tip-top';
						}
						?>
						<span class="sortable-handler hasTooltip <?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
							<i class="icon-menu"></i>
						</span>
						<input type="text" name="order[]" style="display: none;" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />				
					</td>
					<?php
					}
					?>
					<td>							
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>					
						<a href="index.php?option=com_adminmenumanager&view=menu&id=<?php echo $item->id; ?>">					
							<?php echo $this->escape($item->name); ?>	
						</a>				
					</td>	
					<td>
						<a href="javascript:void;" onclick="document.adminForm.go_to_menu.value='<?php echo $item->id; ?>';submitform('go_to_menuitems');">					
							<?php echo $this->controller->amm_strtolower(JText::_('COM_MENUS_SUBMENU_ITEMS')); ?>	
						</a>
					</td>			
					<td>
						<?php echo $item->description; ?>
					</td>
					<?php
					if($this->helper->joomla_version < '3.0'){
					?>	
					<td class="center order">								
						<?php					
						if($listOrder=='a.ordering'){
							 if ($listDirn == 'asc'){							
								if($i){
								?>
									<span><a class="jgrid" href="javascript:reorder_listitem(<?php echo $item->id; ?>,'up')" title="Move Up"><span class="state uparrow"><span class="text"><?php echo JText::_('JLIB_HTML_MOVE_UP'); ?></span></span></a></span>
							<?php }else{ ?>
									<span>&nbsp;</span>
							<?php } ?>
							
							<?php if($i!=(count($this->items)-1)){ ?>
									<span><a class="jgrid" href="javascript:reorder_listitem(<?php echo $item->id; ?>,'down')" title="Move Down"><span class="state downarrow"><span class="text"><?php echo JText::_('JLIB_HTML_MOVE_DOWN'); ?></span></span></a></span>
							<?php }else{ ?>
									<span>&nbsp;</span>
							<?php } ?>
							
						<?php }else{ ?>
						
							<?php 
									if(!$i || $i!=(count($this->items)-1)){
							?>
									<span><a class="jgrid" href="javascript:reorder_listitem(<?php echo $item->id; ?>,'up')" title="Move Up"><span class="state downarrow"><span class="text"><?php echo JText::_('JLIB_HTML_MOVE_UP'); ?></span></span></a></span>
							<?php }else{ ?>
									<span>&nbsp;</span>
							<?php } ?>	
							
							<?php if($i){  ?>
									<span><a class="jgrid" href="javascript:reorder_listitem(<?php echo $item->id; ?>,'down')" title="Move Down"><span class="state uparrow"><span class="text"><?php echo JText::_('JLIB_HTML_MOVE_DOWN'); ?></span></span></a></span>
							<?php }else{ ?>
									<span>&nbsp;</span>
							<?php } 
							}
						}
						?>					
						<input type="text" name="orders[]" size="5" value="<?php echo $item->ordering;?>" class="text-area-order" />
						<input type="hidden" name="order_ids[]" value="<?php echo $item->id; ?>" />
					</td>	
					<?php
					}
					?>					
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>	
	</div>
</form>