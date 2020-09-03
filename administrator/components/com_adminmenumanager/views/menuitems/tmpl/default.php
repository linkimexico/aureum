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

$ds = DIRECTORY_SEPARATOR;

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

$saveOrder 	= ($listOrder == 'a.ordertotal' && $listDirn == 'asc');
if ($saveOrder && $this->helper->joomla_version >= '3.0'){
	$saveOrderingUrl = 'index.php?option=com_adminmenumanager&task=save_order_ajax_menuitems&tmpl=component';
	JHtml::_('sortablelist.sortable', 'items_list', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);		
}

?>
<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){
	if (task=='menuitem'){	
		<?php
		if($this->controller->get_version_type()=='free' && $this->menuitems_total>4){
		?>
			alert('<?php echo addslashes(JText::_('COM_ADMINMENUMANAGER_LIMITED_MENUITEMS')); ?>');
		<?php
		}else{
		?>
			document.location.href = 'index.php?option=com_adminmenumanager&view=menuitem&id=0&menu='+document.adminForm.filter_menu.value;	
		<?php
		}
		?>	
	}	
	if (task=='menuitem_delete' || task=='menuitems_publish' || task=='menuitems_unpublish' || task=='menuitems_copy' || task=='menuitems_export' || task=='menuitems_batch_access' || task=='menuitems_batch_parent' || task=='menuitems_batch_constant') {
		if (document.adminForm.boxchecked.value == '0') {						
			alert('<?php echo addslashes(JText::_('COM_MENUS_NO_ITEM_SELECTED')); ?>');			
			return false;
		}
	}	
	if (task=='menuitem_delete'){		
		if(confirm("<?php echo addslashes(JText::_('COM_ADMINMENUMANAGER_SURE_DELETE_MENUITEMS')); ?>?")){
			submitform('menuitems_delete');
		}		
	}
	if (task=='menuitems_publish'){					
		submitform('menuitems_publish');
	}
	if (task=='menuitems_unpublish'){					
		submitform('menuitems_unpublish');		
	}
	if(task=='menuitems_copy'){
		<?php
		if($this->controller->get_version_type()=='free'){
		?>
			alert('<?php echo addslashes(JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION')); ?>');
			return false;
		<?php
		}else{
		?>
			submitform('menuitems_copy');	
		<?php
		}
		?>		
	}
	if(task=='menuitems_import'){					
		document.location.href = 'index.php?option=com_adminmenumanager&view=menuitemsimport&menu='+document.adminForm.filter_menu.value;	
	}
	if(task=='menuitems_export'){		
		submitform('menuitems_export');		
	}
	if(task=='menuitems_batch_access'){			
		<?php
		if($this->controller->get_version_type()=='free'){
		?>
			alert('<?php echo addslashes(JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION')); ?>');
			return false;
		<?php
		}else{
		?>
			submitform('menuitems_batch_access');	
		<?php
		}
		?>		
	}	
	if(task=='menuitems_batch_parent'){			
		submitform('menuitems_batch_parent');				
	}	
	if(task=='menuitems_batch_constant'){			
		<?php
		if($this->controller->get_version_type()=='free'){
		?>
			alert('<?php echo addslashes(JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION')); ?>');
			return false;
		<?php
		}else{
		?>
			submitform('menuitems_batch_constant');	
		<?php
		}
		?>		
	}	
}

function publish(id){
	document.adminForm.menuitem_id.value = id;
	submitform('menuitem_publish');
}

function unpublish(id){
	document.adminForm.menuitem_id.value = id;
	submitform('menuitem_unpublish');
}

function reorder_listitem(id, direction){
	document.adminForm.menuitem_id.value = id;
	document.adminForm.direction.value = direction;	
	submitform('reorder_listitem');
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
<form action="<?php echo JRoute::_('index.php?option=com_adminmenumanager&view=menuitems');?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="menuitem_id" value="" />
	<input type="hidden" name="menu" value="<?php echo $this->state->get('filter.menu'); ?>" />	
	<input type="hidden" name="direction" value="" />	
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
		<?php
		if($this->controller->get_version_type()=='free'){
			echo '<div class="amm_red amm_page_title">';
			echo JText::_('COM_ADMINMENUMANAGER_LIMITED_MENUITEMS').'.';
			echo '</div>';
		}	
		if(count($this->menus_options)==0){
			echo '<div class="amm_red amm_page_title">';
			echo JText::_('COM_ADMINMENUMANAGER_NO_MENUS').'.';
			echo ' <a href="index.php?option=com_adminmenumanager&view=menu&id=0">'.$this->controller->amm_strtolower(JText::_('MOD_MENU_MENU_MANAGER_NEW_MENU')).'</a>';
			echo '</div>';
		}	
		?>		
		<fieldset id="filter-bar">
			<?php	
			
			//search bar						
			$sortfields = JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);			
			echo $this->helper->search_toolbar(1, 1, 1, 1, $this->state->get('filter.search'), $sortfields, $listDirn, $this->pagination->getLimitBox());								
			
			if($this->helper->joomla_version < '3.0'){
			?>
				
				<div class="filter-select fltrt">
					<span class="fltlft" style="margin-top: 6px;">
					<?php echo JText::_('COM_ADMINMENUMANAGER_MENU'); ?>:
					</span>
					<select name="filter_menu" class="inputbox" onchange="this.form.submit()">				
						<?php echo JHtml::_('select.options', $this->menus_options, 'value', 'text', $this->state->get('filter.menu'), true);?>
					</select>					
					<select name="filter_parent" class="inputbox" onchange="this.form.submit()">	
						<option value="0">- <?php echo JText::_('JSELECT').' '.JText::_('COM_MENUS_FIELD_VALUE_PARENT');?> -</option>			
						<?php echo JHtml::_('select.options', $this->menuitems_nested, 'value', 'text', $this->state->get('filter.parent')); ?>
					</select>					
					<select name="filter_level" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('COM_MENUS_OPTION_SELECT_LEVEL');?></option>
						<?php echo JHtml::_('select.options', $this->filter_levels, 'value', 'text', $this->state->get('filter.level')); ?>
					</select>
				</div>
			<?php
			}
			?>
		</fieldset>
		<div class="clr"> </div>	
		<table class="adminlist table table-striped" width="100%" id="items_list">
			<thead>
				<tr>
					<?php
					if($this->helper->joomla_version >= '3.0'){
					?>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordertotal', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<?php
					}
					?>
					<th width="5" align="left">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<?php
					if($this->helper->joomla_version >= '3.0'){
					?>	
					<th width="1%" style="min-width:55px" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<?php
					}
					?>		
					<th class="left">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
						
					<?php
					if($this->helper->joomla_version < '3.0'){
					?>
					<th class="left">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th class="center">
						<div style="width: 180px; margin: 0 auto;">
							<?php 											
								echo JHtml::_('grid.sort', JText::_('JGRID_HEADING_ORDERING'), 'a.ordertotal', $listDirn, $listOrder);
							?>				
							<a href="javascript:submitform('menuitems_order_save');" class="saveorder" title="Save Order"></a>
						</div>				
					</th>
					<?php
					}
					?>	
					<th class="center">
						<?php 						
							if($this->access_via_accessmanager){							
								echo '<span style="font-weight: normal;"> ('.JText::_('JFIELD_ACCESS_LABEL').' '.JText::_('COM_ADMINMENUMANAGER_VIA').' <a href="index.php?option=com_accessmanager&view=adminmenumanager">Access-Manager</a>)</span>';
							}else{
								echo JHtml::_('grid.sort', JText::_('JFIELD_ACCESS_LABEL'), 'gl.title', $listDirn, $listOrder); 
							}
						?>
					</th>	
					<?php
					if($this->controller->amm_config['multilanguage']){
					?>
						<th class="center">
							<?php 
							$temp_header = JText::_('JGLOBAL_TITLE').' '.$this->controller->amm_strtolower(JText::_('COM_INSTALLER_HEADING_TYPE'));
							echo JHtml::_('grid.sort', $temp_header, 'a.type', $listDirn, $listOrder); ?>
						</th>
					<?php
					}
					?>
					<th class="center">
						<?php echo JHtml::_('grid.sort', 'COM_INSTALLER_HEADING_TYPE', 'a.type', $listDirn, $listOrder); ?>
					</th>												
					<th class="nowrap" width="3%">
						<?php echo JHtml::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>			
			</thead>		
			<tbody>		
			<?php 				
			foreach ($this->items as $i => $item) : 
				$orderkey = array_search($item->id, $this->ordering[$item->parentid]);	
				// Get the parents of item for sorting
				if ($item->level > 1){
					$parentsStr = "";
					$_currentParentId = $item->parentid;
					$parentsStr = " ".$_currentParentId;
					for ($j = 0; $j < $item->level; $j++){
						foreach ($this->ordering as $k => $v){
							$v = implode("-", $v);
							$v = "-" . $v . "-";
							if (strpos($v, "-" . $_currentParentId . "-") !== false){
								$parentsStr .= " " . $k;
								$_currentParentId = $k;
								break;
							}
						}
					}
				}else{
					$parentsStr = "";
				}				
				?>
				<tr class="row<?php echo ($i+1) % 2; ?>" sortable-group-id="<?php echo $item->parentid; ?>" item-id="<?php echo $item->id; ?>" parents="<?php echo $parentsStr; ?>" level="<?php echo $item->level; ?>">
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
					<?php
					if($this->helper->joomla_version >= '3.0'){
					?>
					<td class="center">						
						<a class="btn btn-micro<?php if($item->published){echo ' active';} ?>"  href="javascript:void(0);" onclick="<?php if($item->published){echo 'un';} ?>publish('<?php echo $item->id; ?>');" title="<?php if($item->published){echo 'un';} ?>publish">
							<i class="icon-<?php if(!$item->published){echo 'un';} ?>publish"></i>
						</a>
					</td>
					<?php
					}
					?>
					<td>
						<?php 					
						$levels = $item->level;
						for($n = 1; $n < $levels; $n++){					
							 echo '<span class="gi">|&mdash;</span>';
							 echo '&nbsp;';
						}					
					
						if($item->icon && $item->type=='0'){											
							$src = 'components/com_adminmenumanager/images/blank.png';						
							if(file_exists(JPATH_ROOT.$ds.'administrator'.$ds.$item->icon)){									
								$src = $item->icon;														
							}							
							echo '<span class="icon_with_class2">';
							echo '<img src="'.$src.'" alt="icon" />';
							echo '</span>';						
						}
						if($item->type=='2'){
							//line separator
							if($item->level=='1'){
								//horizontal
								echo '<span class="icon_with_class2">';
								echo '<img src="components/com_adminmenumanager/images/line.png" alt="line" />';
								echo '</span>';
							}else{
								//vertical
								echo '<span style="display: inline-block; width: 160px; height: 1px; background: #ccc; margin-bottom: 2px;"></span>';
							}
						}
						?>									
						<a href="index.php?option=com_adminmenumanager&view=menuitem&id=<?php echo $item->id; ?>">					
							<?php echo $this->escape($item->title); ?>	
						</a>				
					</td>					
					<?php
					if($this->helper->joomla_version < '3.0'){
					?>
					<td>					
						<a href="javascript:void(0);" onclick="<?php if($item->published){echo 'un';} ?>publish('<?php echo $item->id; ?>');" class="jgrid" title="<?php if($item->published){echo 'un';} ?>publish">
							<span class="state <?php if(!$item->published){echo 'un';} ?>publish"></span>
						</a>
					</td>
					<td class="center order">					
						<?php if ($listDirn == 'asc'){ ?>						
						
							<?php if($item->ordering!='1'){ ?>
								<span><a class="jgrid" href="javascript:reorder_listitem(<?php echo $item->id; ?>,'up')" title="Move Up"><span class="state uparrow"><span class="text"><?php echo JText::_('JLIB_HTML_MOVE_UP'); ?></span></span></a></span>
							<?php }else{ ?>
								<span>&nbsp;</span>
							<?php } ?>
							
							<?php if((!in_array($item->id, $this->last_items)) && $item->id!=$this->last_menuitem){ ?>
								<span><a class="jgrid" href="javascript:reorder_listitem(<?php echo $item->id; ?>,'down')" title="Move Down"><span class="state downarrow"><span class="text"><?php echo JText::_('JLIB_HTML_MOVE_DOWN'); ?></span></span></a></span>
							<?php }else{ ?>
								<span>&nbsp;</span>
							<?php } ?>
							
						<?php }else{ ?>
						
							<?php if($item->ordering!='1'){ ?>
								<span><a class="jgrid" href="javascript:reorder_listitem(<?php echo $item->id; ?>,'up')" title="Move Up"><span class="state downarrow"><span class="text"><?php echo JText::_('JLIB_HTML_MOVE_UP'); ?></span></span></a></span>
							<?php }else{ ?>
								<span>&nbsp;</span>
							<?php } ?>	
							
							<?php if(!in_array($item->id, $this->last_items)){ ?>
								<span><a class="jgrid" href="javascript:reorder_listitem(<?php echo $item->id; ?>,'down')" title="Move Down"><span class="state uparrow"><span class="text"><?php echo JText::_('JLIB_HTML_MOVE_DOWN'); ?></span></span></a></span>
							<?php }else{ ?>
								<span>&nbsp;</span>
							<?php } ?>											
											
						<?php } ?>					
						<input type="text" name="orders[]" size="5" value="<?php echo $item->ordering;?>" class="text-area-order" />
						<input type="hidden" name="order_ids[]" value="<?php echo $item->id; ?>" />
					</td>	
					<?php
					}
					?>
					<td class="center">					
						<?php 					
						if($this->access_via_accessmanager){
							echo '-';
						}else{
							echo htmlspecialchars($item->gltitle);
						}
						?>
					</td>
					<?php
					if($this->controller->amm_config['multilanguage']){
					?>
						<td class="center">					
							<?php 					
							if($item->use_constant){
								echo JText::_('COM_LANGUAGES_VIEW_OVERRIDES_KEY');
							}else{
								echo JText::_('COM_LANGUAGES_VIEW_OVERRIDES_TEXT');
							}
							?>
						</td>	
					<?php
					}
					?>
					<td class="center">
						<?php 					
						if($item->type=='1'){
							echo $this->controller->amm_strtolower(JText::_('COM_MENUS_TYPE_SEPARATOR'));
						}elseif($item->type=='2'){
							echo JText::_('COM_ADMINMENUMANAGER_LINE');;	
						}else{
							echo $this->controller->amm_strtolower(JText::_('COM_MENUS_ITEM_FIELD_ALIAS_MENU_LABEL'));
						}
						?>
					</td>											
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>		
		</table>	
		<table class="adminlist">
			<tfoot>
				<tr>
					<td>
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>	
		<div class="width-100 fltlft">
			<fieldset class="adminform" style="margin: 0;">	
				<legend class="pi_legend"><?php echo JText::_('COM_MENUS_BATCH_OPTIONS'); ?></legend>
				<table class="adminlist tabletop amm_table">
					<tr>
						<td class="amm_nowrap" style="width: 150px;">
							<?php
								echo JText::_('COM_MENUS_FIELD_VALUE_PARENT');
							?>	
						</td>
						<td class="amm_nowrap" style="width: 150px;">						
							<select name="parentid" id="select_parentid">								
								<?php 
								echo '<option value="0">'.$this->controller->amm_strtolower(JText::_('JGLOBAL_ROOT')).'</option>';
								echo JHtml::_('select.options', $this->menuitems_nested, 'value', 'text'); 
								?>					
							</select>																
						</td>
						<td class="amm_nowrap">	
							<button type="submit" onclick="Joomla.submitbutton('menuitems_batch_parent');">
								<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
							</button>									
						</td>
					</tr>	
					<tr>
						<td class="amm_nowrap" style="width: 150px;">						
							<?php
								echo JText::_('JGRID_HEADING_ACCESS');
							?>						
						</td>
						<td class="amm_nowrap" style="width: 150px;">						
							<select name="access">	
								<?php
								foreach($this->groups_levels as $group_level){							
									echo '<option value="'.$group_level->id.'"';
									if($this->controller->amm_config[$this->default_access_type]==$group_level->id){
										echo ' selected="selected"';
									}
									echo '>';
									if($this->controller->amm_config['based_on']=='group'){
										echo str_repeat('- ',$group_level->hyrarchy);
									}												
									echo $group_level->title;
									echo '</option>';								
								}
								?>						
							</select> 										
						</td>
						<td class="amm_nowrap">
							<button type="submit" onclick="Joomla.submitbutton('menuitems_batch_access');">
								<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
							</button>
							<?php
									if($this->controller->get_version_type()=='free' && !$this->helper->check_if_access_via_accessmanager()){
										echo '<span class="amm_red">'.JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION').'</span>.<br />';
									}
								?>
						</td>	
					</tr>
					<?php
					if($this->controller->amm_config['multilanguage']){
					?>
						<tr>
							<td class="amm_nowrap" style="width: 150px;">
								<?php
									echo JText::_('JGLOBAL_TITLE').' '.$this->controller->amm_strtolower(JText::_('COM_INSTALLER_HEADING_TYPE'));
								?>	
							</td>
							<td class="amm_nowrap" style="width: 150px;">						
								<select name="use_constant" id="select_use_constant">								
									<?php 
									echo '<option value="0">'.$this->controller->amm_strtolower(JText::_('COM_LANGUAGES_VIEW_OVERRIDES_TEXT')).'</option>';
									echo '<option value="1">'.$this->controller->amm_strtolower(JText::_('JGRID_HEADING_LANGUAGE').' '.JText::_('COM_LANGUAGES_VIEW_OVERRIDES_KEY')).'</option>';								
									?>					
								</select>																								
							</td>
							<td class="amm_nowrap">	
								<button type="submit" onclick="Joomla.submitbutton('menuitems_batch_constant');">
									<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
								</button>	
								<?php
									if($this->controller->get_version_type()=='free' && !$this->helper->check_if_access_via_accessmanager()){
										echo '<span class="amm_red">'.JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION').'</span>.<br />';
									}
								?>								
							</td>
						</tr>
					<?php
					}
					?>				
				</table>			
			</fieldset>
		</div>	
	</div>	
</form>
