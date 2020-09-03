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
?>

<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){		
	if(task=='users_export'){	
		document.location.href = 'index.php?option=com_adminmenumanager&view=users&layout=csv';		
	} 
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

<form action="<?php echo JRoute::_('index.php?option=com_adminmenumanager&view=users');?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<?php if (!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>	
	<div id="j-main-container"<?php echo empty($this->sidebar) ? '' : ' class="span10"'; ?>>
		<p><?php echo JText::_('COM_ADMINMENUMANAGER_SUBTITLE_USERS'); ?>.</p>	
		<fieldset id="filter-bar">
			<?php	
			
			//search bar						
			$sortfields = JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);			
			echo $this->helper->search_toolbar(1, 1, 1, 1, $this->state->get('filter.search'), $sortfields, $listDirn, $this->pagination->getLimitBox());								
			
			if($this->helper->joomla_version < '3.0'){
			?>
				
				<div class="filter-select fltrt">
					<select name="filter_group_id" class="inputbox" onchange="this.form.submit()">
						<option value=""> - <?php echo JText::_('JSELECT').' '.JText::_('JLIB_RULES_GROUPS'); ?> - </option>
						<?php echo JHtml::_('select.options', $this->get_groups(), 'value', 'text', $this->state->get('filter.group_id'));?>
					</select>
					<select name="filter_level_id" class="inputbox" onchange="this.form.submit()">
						<option value=""> - <?php echo JText::_('JSELECT').' '.JText::_('MOD_MENU_COM_USERS_LEVELS'); ?> - </option>
						<?php echo JHtml::_('select.options', $this->get_levels(), 'value', 'text', $this->state->get('filter.level_id'));?>
					</select>
				</div>
			<?php
			}
			?>
		</fieldset>
		<div class="clr"> </div>	
		<table class="adminlist table table-striped" width="100%">
			<thead>
				<tr>				
					<th class="left">
						<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
					</th>							
					<th class="nowrap center">
						<?php echo ucfirst(JText::_('JLIB_RULES_GROUPS')); ?>								
					</th>				
					<th class="nowrap center">
						<?php echo ucfirst(JText::_('MOD_MENU_COM_USERS_LEVELS')); ?>
					</th>								
					<th class="nowrap" width="3%">
						<?php echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ID'), 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>			
			</thead>		
			<tbody>		
			<?php 		
			foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo ($i+1) % 2; ?>">				
					<td>
						<?php echo $this->escape($item->name); ?>
					</td>
					<td class="center">					
						<a href="index.php?option=com_users&task=user.edit&id=<?php echo $item->id; ?>">					
						<?php echo $this->escape($item->username); ?>	
						</a>				
					</td>				
					<td class="center">
						<?php 	
						$group_ids_array = $this->get_users_groups($item->id);									
						foreach($this->groups_title_order_back as $temp){
							if(in_array($temp[0], $group_ids_array)){
								echo $temp[1];
								echo '<br />';
							}
						}
						?>
					</td>
					<td class="center">
						<?php 					
						$levels_ids_array = $this->get_groups_levels($group_ids_array);
							
						foreach($this->levels_title_order as $temp){
							if(in_array($temp->level_id, $levels_ids_array)){
								echo $temp->level_title;
								echo '<br />';
							}
						}
						
						?>
					</td>								
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</form>
