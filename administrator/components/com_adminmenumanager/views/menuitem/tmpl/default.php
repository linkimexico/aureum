<?php
/**
* @package Admin-Menu-Manager (com_adminmenumanager)
* @version 2.3.2
* @copyright Copyright (C) 2012 - 2019 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/



// no direct access
defined('_JEXEC') or die('Restricted access');

$ds = DIRECTORY_SEPARATOR;

$checked = 'checked="checked"';

JHTML::_('behavior.modal');
?>
<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){		
	if (task=='cancel'){			
		document.location.href = 'index.php?option=com_adminmenumanager&view=menuitems';		
	} else {
		if (document.getElementById('menuitem_title').value == '') {			
			alert('<?php echo addslashes(JText::_('COM_ADMINMENUMANAGER_NO_TITLE_ENTERED')); ?>');
			return;
		}		
		if (task=='menuitem_apply'){	
			document.adminForm.apply.value = '1';
		}
		if (task=='menuitem_save_and_new'){				
			<?php
			if($this->controller->get_version_type()=='free'){
			?>
				alert('<?php echo addslashes(JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION')); ?>');
				return false;
			<?php
			}else{
			?>
				document.adminForm.save_and_new.value = '1';
			<?php
			}
			?>	
		}
		if(task=='menuitem_save_as_copy'){
			<?php
			if($this->controller->get_version_type()=='free'){
			?>
				alert('<?php echo addslashes(JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION')); ?>');
				return false;
			<?php
			}else{
			?>
				submitform('menuitem_save_as_copy');
				return false;
			<?php
			}
			?>	
		}	
		submitform('menuitem_save');
	}	
}

function do_icon(icon){	
	document.getElementById('menuitem_icon').value = icon;
	document.getElementById('preview_a').style.backgroundImage = 'url('+icon+')';
	document.getElementById('preview_div_separator_text').style.backgroundImage = 'url('+icon+')';	
	SqueezeBox.close();			
}

function do_menuitem(element_id, url, constant){	
	document.getElementById('radio_type_menuitem').checked = true;
	show_only_preview_type('menuitem');	
	document.getElementById('menuitem_title').value = document.getElementById(element_id).innerText;
	document.getElementById('menuitem_constant').value = constant;
	document.getElementById('preview_a').innerText = document.getElementById(element_id).innerText;
	document.getElementById('preview_a').href = url;
	document.getElementById('menuitem_url').value = url;
	temp = document.getElementById(element_id).style.backgroundImage;	
	if(element_id.indexOf("mm_component_")==true){				
		icon = document.getElementById(element_id).rel;		
	}else{
		temp = temp.replace(")", "");
		pos = temp.indexOf("administrator")+14;	
		icon = temp.slice(pos);
	}
	do_icon(icon);
}

function do_preview(){
	document.getElementById('preview_a').innerText = document.getElementById('menuitem_title').value;	
	document.getElementById('preview_a').style.backgroundImage = 'url('+document.getElementById('menuitem_icon').value+')';
	document.getElementById('preview_div_separator_text').style.backgroundImage = 'url('+document.getElementById('menuitem_icon').value+')';
	document.getElementById('preview_a').href = document.getElementById('menuitem_url').value;
}

function change_type(type){
	if(type=='separator_text' && document.getElementById('menuitem_title').value==''){
		document.getElementById('menuitem_title').value = '<?php echo addslashes($this->controller->amm_strtolower(JText::_('COM_MENUS_TYPE_SEPARATOR'))); ?>';			
	}
	if(type=='separator_line' && document.getElementById('menuitem_title').value==''){
		document.getElementById('menuitem_title').value = '<?php echo addslashes(JText::_('COM_ADMINMENUMANAGER_LINE')); ?>';	
	}	
	if(type=='separator_text' || type=='separator_line'){
		if(type=='separator_line'){
			document.getElementById('menuitem_icon').value = '';
		}
		document.getElementById('menuitem_url').value = '';	
		do_preview();					
	}	
	if(type=='separator_text'){
		document.getElementById('preview_div_separator_text').innerText = document.getElementById('menuitem_title').value;
	}
	if(type=='separator_line'){
		if(document.getElementById('select_parentid').value=='0'){
			line = '<img src="components/com_adminmenumanager/images/line_preview_h.png" alt="line" />';
		}else{
			line = '<img src="components/com_adminmenumanager/images/line_preview_v.png" alt="line" />';
		}
		document.getElementById('preview_div_separator_line').innerHTML = line;
	}
	show_only_preview_type(type);	
}

function show_only_preview_type(type){
	document.getElementById('preview_div_separator_text').style.display = 'none';
	document.getElementById('preview_div_separator_line').style.display = 'none';
	document.getElementById('preview_div_a').style.display = 'none';
	if(type=='separator_text'){
		document.getElementById('preview_div_separator_text').style.display = 'block';
	}else if(type=='separator_line'){
		document.getElementById('preview_div_separator_line').style.display = 'block';
	}else{
		document.getElementById('preview_div_a').style.display = 'block';
	}	
}

function show_hide_sizes(){
	target = document.getElementById('target').value;
	if(target=='2' || target=='3'){
		document.getElementById('div_sizes').style.display = 'block';
	}else{
		document.getElementById('div_sizes').style.display = 'none';
	}
}

function select_icon(){	
	var options = {size: {x: 300, y: 250}};
	SqueezeBox.initialize(options);	
	SqueezeBox.setContent('string',amm_all_icons);
}


</script>
<form action="" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="menu_ori" value="<?php echo $this->menuitem->menu; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="apply" value="" />
	<input type="hidden" name="save_and_new" value="" />
	<input type="hidden" name="menuitem_id" value="<?php echo $this->menuitem->id; ?>" />			
	<?php echo JHtml::_('form.token'); ?>
	<div class="fltlft">
		<div class="clr"> </div><!-- needed for some admin templates -->
		<h2 class="amm_page_title"><?php 		
			if($this->menuitem->id==0){
				echo JText::_('JTOOLBAR_NEW').' ';
			}
			echo JText::_('COM_MENUS_ITEM_FIELD_ALIAS_MENU_LABEL');
			if($this->menuitem->id){
				echo ': '.htmlspecialchars($this->menuitem->title); 
			}			
			?></h2>				
		<fieldset class="adminform pi_wrapper_nice">										
			<table class="adminlist tabletop amm_table">	
				<tr>
					<td class="amm_nowrap" style="width: 150px;">						
						<?php
							echo JText::_('JPUBLISHED');
						?>						
					</td>
					<td colspan="3">						
						<label>
							<input type="radio" name="published" value="1" class="radio" <?php if($this->menuitem->published){echo 'checked="checked"';} ?> />
							<?php echo JText::_('JYES'); ?>
						</label>
						<label>
							<input type="radio" name="published" value="0" class="radio" <?php if(!$this->menuitem->published){echo 'checked="checked"';} ?> />
							<?php echo JText::_('JNO'); ?>
						</label>												
					</td>					
				</tr>
				<tr<?php if(!$this->controller->amm_config['access_enabled'] && !$this->helper->check_if_access_via_accessmanager()){echo ' style="display: none;"';} ?>>
					<td class="amm_nowrap">											
						<?php
							echo JText::_('JGRID_HEADING_ACCESS');
						?>											
					</td>
					<td colspan="3">						
						<select name="access" id="jform_access">	
							<?php
							foreach($this->groups_levels as $group_level){							
								echo '<option value="'.$group_level->id.'"';
								if($this->menuitem->access==$group_level->id){
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
						<?php
							if($this->controller->get_version_type()=='free' && !$this->helper->check_if_access_via_accessmanager()){
								echo '<span class="amm_red">'.JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION').'</span>.<br />';
							}
						?>				
					</td>					
				</tr>							
				<tr>
					<td class="amm_nowrap">						
						<?php
							echo JText::_('COM_ADMINMENUMANAGER_MENU');
						?>						
					</td>
					<td colspan="3">						
						<select name="menu">	
							<?php
							foreach($this->menus as $menu){							
								echo '<option value="'.$menu->id.'"';
								if($this->menuitem->menu==$menu->id){
									echo ' selected="selected"';
								}
								echo '>';												
								echo $menu->name;
								echo '</option>';								
							}
							?>						
						</select> 				
					</td>					
				</tr>								
				<tr>
					<td class="amm_nowrap">						
						<?php
							echo JText::_('COM_MENUS_FIELD_VALUE_PARENT');
						?>						
					</td>
					<td colspan="3">						
						<select name="parentid" id="select_parentid">	
							<?php
							echo '<option value="0">'.$this->controller->amm_strtolower(JText::_('JGLOBAL_ROOT')).'</option>';	
							foreach($this->menuitems as $menuitem){	
													
								echo '<option value="'.$menuitem->id.'"';
								if($this->menuitem->parentid==$menuitem->id){
									echo ' selected="selected"';
								}
								echo '>';
								$levels = $menuitem->level;
								for($n = 0; $n < $levels; $n++){
									echo '- ';
								}												
								echo $menuitem->title;
								echo '</option>';								
							}
							?>						
						</select> 						
					</td>					
				</tr>
				<tr>
					<td class="amm_nowrap">
						<?php
							echo JText::_('JGRID_HEADING_MENU_ITEM_TYPE');
						?>											
					</td>
					<td colspan="3">
						<label onclick="change_type('menuitem')">
							<input type="radio" name="type" value="0" class="radio" <?php if(!$this->menuitem->type){echo 'checked="checked"';} ?> id="radio_type_menuitem" />
							<?php echo $this->controller->amm_strtolower(JText::_('COM_MENUS_ITEM_FIELD_ALIAS_MENU_LABEL')); ?>
						</label>
						<label onclick="change_type('separator_text')">
							<input type="radio" name="type" value="1" class="radio" <?php if($this->menuitem->type=='1'){echo 'checked="checked"';} ?> />
							<?php echo $this->controller->amm_strtolower(JText::_('COM_MENUS_TYPE_SEPARATOR')); ?>
						</label>
						<label onclick="change_type('separator_line')">
							<input type="radio" name="type" value="2" class="radio" <?php if($this->menuitem->type=='2'){echo 'checked="checked"';} ?> />
							<?php echo JText::_('COM_ADMINMENUMANAGER_LINE'); ?>
						</label>
					</td>					
				</tr>
				<tr>
					<td class="amm_nowrap">
						<?php
							echo JText::_('JGLOBAL_TITLE');
						?>											
					</td>
					<?php
						if($this->controller->amm_config['multilanguage']){
					?>
						<td class="amm_nowrap" style="width: 150px;">
							<label>
								<input type="radio" name="use_constant" value="0" class="radio" <?php if(!$this->menuitem->use_constant){echo $checked;} ?> /> 
								<?php echo JText::_('COM_LANGUAGES_VIEW_OVERRIDES_TEXT'); ?>
							</label>							
						</td>
					<?php
						}
					?>						
					<td colspan="<?php if($this->controller->amm_config['multilanguage']){echo '2';}else{echo '3';} ?>">
						<input type="text" name="title" id="menuitem_title" style="width: 450px;" value="<?php echo htmlspecialchars($this->menuitem->title);?>" onblur="do_preview();" />
					</td>				
				</tr>	
				<tr
				<?php
					if(!$this->controller->amm_config['multilanguage']){
				?>
					style="display: none;"
				<?php
					}
				?>
				>
					<td>																					
					</td>
					<td style="width: 150px;" class="amm_nowrap">						
						<label>
							<input type="radio" name="use_constant" value="1" class="radio" <?php if($this->menuitem->use_constant){echo $checked;} ?> /> 						
							<?php echo JText::_('COM_LANGUAGES_VIEW_OVERRIDES_KEY'); ?>
						</label>						
					</td>
					<td colspan="2">
						<input type="text" name="constant" id="menuitem_constant" style="width: 450px;" value="<?php echo htmlspecialchars($this->menuitem->constant);?>" />&nbsp;  
						<?php
							echo '<a href="http://www.pages-and-items.com/extensions/admin-menu-manager/faqs?faqitem=config_multilanguage" target="_blank">?</a> ';
							if($this->controller->get_version_type()=='free' && !$this->helper->check_if_access_via_accessmanager()){
								echo '<span class="amm_red">'.JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION').'</span>.<br />';
							}
						?>						
					</td>					
				</tr>
				<tr>
					<td class="amm_nowrap">						
						URL						
					</td>
					<td style="width: 150px;" colspan="2">											
						<input type="text" name="url" id="menuitem_url" style="width: 450px;" value="<?php echo htmlspecialchars($this->menuitem->url);?>" onblur="do_preview();" />						
					</td>
					<td>
						<?php
							echo JText::_('COM_MENUS_ITEM_FIELD_BROWSERNAV_LABEL');
						?>	
						&nbsp;<select name="target" id="target" onchange="show_hide_sizes()">	
						<?php echo JHtml::_('select.options', $this->get_target_options($this->controller), 'value', 'text', $this->menuitem->target);?>	
						</select>	
						<div id="div_sizes" <?php if($this->menuitem->target=='0' || $this->menuitem->target=='1'){echo ' style="display: none;"';} ?>>
							<input type="text" name="width" style="width: 30px;" value="<?php echo $this->menuitem->width;?>" /> px 		
							<?php echo $this->controller->amm_strtolower(JText::_('JGLOBAL_WIDTH')); ?>
							<br />
							<input type="text" name="height" style="width: 30px;" value="<?php echo $this->menuitem->height;?>" /> px 	
							<?php echo $this->controller->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_HEIGHT')); ?>
						</div>							
					</td>
				</tr>
				<tr>
					<td class="amm_nowrap">						
						<?php
							echo JText::_('COM_ADMINMENUMANAGER_ICON');
						?>						
					</td>
					<td style="width: 150px;" colspan="2">						
						<input type="text" name="icon" id="menuitem_icon" style="width: 450px;" value="<?php echo htmlspecialchars($this->menuitem->icon);?>" onblur="do_preview();" />									
					</td>
					<td>
						<!--
						<button onclick="select_icon(); return false;"><?php echo $this->controller->amm_strtolower(JText::_('JSELECT')).' '.$this->controller->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_ICON')); ?> </button>	
						-->
						<a href="#amm_all_icons" class="modal" style="text-decoration: none;"><button><?php echo $this->controller->amm_strtolower(JText::_('JSELECT')).' '.$this->controller->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_ICON')); ?></button></a>
						<button onclick="document.getElementById('menuitem_icon').value=''; do_icon(''); return false;"><?php echo $this->controller->amm_strtolower(JText::_('JCLEAR')).' '.$this->controller->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_ICON')); ?> </button>						
					</td>					
				</tr>												
			</table>
		</fieldset>
		<fieldset class="adminform pi_wrapper_nice">
			<legend class="pi_legend"><?php echo JText::_('JGLOBAL_PREVIEW'); ?></legend>
			<table class="adminlist tabletop amm_table">
				<tr>					
					<td class="amm_quick_select_buttons" style="padding-left: 8px;">
						<div id="preview_div_a"<?php if($this->menuitem->type!='0'){echo ' style="display: none;"';} ?>>					
							<a href="#" id="preview_a" style="background-image: url('<?php echo $this->menuitem->icon;?>'); width: 130px; height: 16px;">											
								<?php echo str_replace('"', '&quot;', $this->menuitem->title);?> 
							</a>
						</div>
						<div id="preview_div_separator_text" style="background-image: url('<?php echo $this->menuitem->icon;?>');<?php if($this->menuitem->type!='1'){echo 'display: none;';} ?>">	
							<?php echo str_replace('"', '&quot;', $this->menuitem->title); ?> 
						</div>	
						<div id="preview_div_separator_line"<?php if($this->menuitem->type!='2'){echo ' style="display: none;"';} ?>>
							<?php
							if($this->menuitem->level=='1'){
								echo '<img src="components/com_adminmenumanager/images/line_preview_h.png" alt="line" />';
							}else{
								echo '<img src="components/com_adminmenumanager/images/line_preview_v.png" alt="line" />';
							}
							?>	
						</div>															
					</td>										
				</tr>
			</table>
		</fieldset>
		<fieldset class="adminform pi_wrapper_nice">
			<legend class="pi_legend"><?php echo JText::_('COM_ADMINMENUMANAGER_QUICK_LINK_CREATOR'); ?></legend>
			<table class="adminlist tabletop amm_table">								
				<tr>					
					<td class="amm_quick_select_buttons">						
						<div>
							<a href="javascript:do_menuitem('amm_site', '#', '<?php 								
								if($this->helper->joomla_version >= '3.0'){
									echo 'MOD_MENU_SYSTEM';
								}else{
									echo 'JSITE';
								}
								?>');" id="amm_site">
								<?php 								
								if($this->helper->joomla_version >= '3.0'){
									echo JText::_('MOD_MENU_SYSTEM');
								}else{
									echo JText::_('JSITE');
								}
								?>
							</a>
						</div>								
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_control', 'index.php', 'MOD_MENU_CONTROL_PANEL');" id="amm_control" <?php echo $this->icon_background('icon-16-cpanel.png'); ?>>
								<?php echo JText::_('MOD_MENU_CONTROL_PANEL'); ?>
							</a>
						</div>								
						<?php if($this->helper->joomla_version < '3.0'){ ?>						
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_profile', 'my-profile', 'MOD_MENU_USER_PROFILE');" id="amm_profile" <?php echo $this->icon_background('icon-16-user.png'); ?>>
								<?php echo JText::_('MOD_MENU_USER_PROFILE'); ?>
							</a>
						</div>
						<?php } ?>						
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_global', 'index.php?option=com_config', 'MOD_MENU_CONFIGURATION');" id="amm_global" <?php echo $this->icon_background('icon-16-config.png'); ?>>
								<?php echo JText::_('MOD_MENU_CONFIGURATION'); ?>
							</a>
						</div>
						<?php if($this->helper->joomla_version < '3.0'){ ?>	
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_maintenance', 'index.php?option=com_checkin', 'MOD_MENU_MAINTENANCE');" id="amm_maintenance" <?php echo $this->icon_background('icon-16-maintenance.png'); ?>>
								<?php echo JText::_('MOD_MENU_MAINTENANCE'); ?>
							</a>
						</div>
						<?php } ?>	
						<div class="margin_<?php
						if($this->helper->joomla_version >= '3.0'){
							echo '1';
						}else{
							echo '2';
						}
						?>">
							<a href="javascript:do_menuitem('amm_checkin', 'index.php?option=com_checkin', 'MOD_MENU_GLOBAL_CHECKIN');" id="amm_checkin" <?php echo $this->icon_background('icon-16-checkin.png'); ?>>
								<?php echo JText::_('MOD_MENU_GLOBAL_CHECKIN'); ?>
							</a>
						</div>
						<div class="margin_<?php
						if($this->helper->joomla_version >= '3.0'){
							echo '1';
						}else{
							echo '2';
						}
						?>">
							<a href="javascript:do_menuitem('amm_cache', 'index.php?option=com_cache', 'MOD_MENU_CLEAR_CACHE');" id="amm_cache" <?php echo $this->icon_background('icon-16-clear.png'); ?>>
								<?php echo JText::_('MOD_MENU_CLEAR_CACHE'); ?>
							</a>
						</div>
						<div class="margin_<?php
						if($this->helper->joomla_version >= '3.0'){
							echo '1';
						}else{
							echo '2';
						}
						?>">
							<a href="javascript:do_menuitem('amm_purge', 'index.php?option=com_cache&view=purge', 'MOD_MENU_PURGE_EXPIRED_CACHE');" id="amm_purge" <?php echo $this->icon_background('icon-16-purge.png'); ?>>
								<?php echo JText::_('MOD_MENU_PURGE_EXPIRED_CACHE'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_info', 'index.php?option=com_admin&view=sysinfo', 'MOD_MENU_SYSTEM_INFORMATION');" id="amm_info" <?php echo $this->icon_background('icon-16-info.png'); ?>>
								<?php echo JText::_('MOD_MENU_SYSTEM_INFORMATION'); ?>
							</a>
						</div>												
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_logout', 'logout', 'MOD_MENU_LOGOUT');" id="amm_logout" <?php echo $this->icon_background('icon-16-logout.png'); ?>>
								<?php echo JText::_('MOD_MENU_LOGOUT'); ?>
							</a>
						</div>											
					</td>
					<td class="amm_quick_select_buttons">
						<div>
							<a href="javascript:do_menuitem('amm_users', '#', 'MOD_MENU_COM_USERS_USERS');" id="amm_users">
								<?php echo JText::_('MOD_MENU_COM_USERS_USERS'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_usermanager', 'index.php?option=com_users&view=users', 'MOD_MENU_COM_USERS_USER_MANAGER');" id="amm_usermanager" <?php echo $this->icon_background('icon-16-user.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_USERS_USER_MANAGER'); ?>
							</a>
						</div>
						<div class="margin_2">
							<a href="javascript:do_menuitem('amm_usernew', 'index.php?option=com_users&view=user&layout=edit', 'MOD_MENU_COM_USERS_ADD_USER');" id="amm_usernew" <?php echo $this->icon_background('icon-16-newarticle.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_USERS_ADD_USER'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_groups', 'index.php?option=com_users&view=groups', 'MOD_MENU_COM_USERS_GROUPS');" id="amm_groups" <?php echo $this->icon_background('icon-16-groups.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_USERS_GROUPS'); ?>
							</a>
						</div>
						<div class="margin_2">
							<a href="javascript:do_menuitem('amm_groupnew', 'index.php?option=com_users&view=group&layout=edit', 'MOD_MENU_COM_USERS_ADD_GROUP');" id="amm_groupnew" <?php echo $this->icon_background('icon-16-newarticle.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_USERS_ADD_GROUP'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_levels', 'index.php?option=com_users&view=levels', 'MOD_MENU_COM_USERS_LEVELS');" id="amm_levels" <?php echo $this->icon_background('icon-16-levels.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_USERS_LEVELS'); ?>
							</a>
						</div>
						<div class="margin_2">
							<a href="javascript:do_menuitem('amm_levelnew', 'index.php?option=com_users&view=level&layout=edit', 'MOD_MENU_COM_USERS_ADD_LEVEL');" id="amm_levelnew" <?php echo $this->icon_background('icon-16-newarticle.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_USERS_ADD_LEVEL'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_notes', 'index.php?option=com_users&view=notes', 'MOD_MENU_COM_USERS_NOTES');" id="amm_notes" <?php echo $this->icon_background('icon-16-user-note.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_USERS_NOTES'); ?>
							</a>
						</div>
						<div class="margin_2">
							<a href="javascript:do_menuitem('amm_notenew', 'index.php?option=com_users&view=note&layout=edit', 'MOD_MENU_COM_USERS_ADD_NOTE');" id="amm_notenew" <?php echo $this->icon_background('icon-16-newarticle.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_USERS_ADD_NOTE'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_notecat', 'index.php?option=com_categories&view=categories&extension=com_users.notes', 'MOD_MENU_COM_USERS_NOTE_CATEGORIES');" id="amm_notecat" <?php echo $this->icon_background('icon-16-category.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_USERS_NOTE_CATEGORIES'); ?>
							</a>
						</div>
						<div class="margin_2">
							<a href="javascript:do_menuitem('amm_notecatnew', 'index.php?option=com_categories&view=category&layout=edit&extension=com_users', 'MOD_MENU_COM_CONTENT_NEW_CATEGORY');" id="amm_notecatnew" <?php echo $this->icon_background('icon-16-newarticle.png'); ?>>

								<?php echo JText::_('MOD_MENU_COM_CONTENT_NEW_CATEGORY'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_massmail', 'index.php?option=com_users&view=mail', 'MOD_MENU_MASS_MAIL_USERS');" id="amm_massmail" <?php echo $this->icon_background('icon-16-massmail.png'); ?>>
								<?php echo JText::_('MOD_MENU_MASS_MAIL_USERS'); ?>
							</a>
						</div>
					</td>
					<td class="amm_quick_select_buttons">						
						<div>
							<a href="javascript:do_menuitem('amm_menus', '#', 'MOD_MENU_MENUS');" id="amm_menus">
								<?php echo JText::_('MOD_MENU_MENUS'); ?>
							</a>
						</div>	
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_menumanager', 'index.php?option=com_menus&view=menus', 'MOD_MENU_MENU_MANAGER');" id="amm_menumanager" <?php echo $this->icon_background('icon-16-menumgr.png'); ?>>
								<?php echo JText::_('MOD_MENU_MENU_MANAGER'); ?>
							</a>
						</div>
						<div class="margin_2">
							<a href="javascript:do_menuitem('amm_menunew', 'index.php?option=com_menus&view=menu&layout=edit', 'MOD_MENU_MENU_MANAGER_NEW_MENU');" id="amm_menunew" <?php echo $this->icon_background('icon-16-newarticle.png'); ?>>
								<?php echo JText::_('MOD_MENU_MENU_MANAGER_NEW_MENU'); ?>
							</a>
						</div>
						<?php
						foreach($this->joomla_menus as $joomla_menu){
							echo '<div class="margin_1">';
						 	echo '<a href="javascript:do_menuitem(\'amm_menu_'.$joomla_menu->menutype.'\', \'index.php?option=com_menus&view=items&menutype='.$joomla_menu->menutype.'\', \'\');" id="amm_menu_'.$joomla_menu->menutype.'"';
							echo $this->icon_background('icon-16-menu.png'); 
							echo '>';
							echo $joomla_menu->title;
							echo '</a>';
							echo '</div>';
							echo '<div class="margin_2">';
						 	echo '<a href="javascript:do_menuitem(\'amm_menu_new_'.$joomla_menu->menutype.'\', \'index.php?option=com_menus&view=item&layout=edit&menutype='.$joomla_menu->menutype.'\', \'MOD_MENU_MENU_MANAGER_NEW_MENU\');" id="amm_menu_new_'.$joomla_menu->menutype.'"';
							echo $this->icon_background('icon-16-newarticle.png'); 
							echo '>';
							echo JText::_('MOD_MENU_MENU_MANAGER_NEW_MENU');
							echo '</a>';
							echo '</div>';
						}
						?>					
					</td>
					<td class="amm_quick_select_buttons">	
						<div>
							<a href="javascript:do_menuitem('amm_content', '#', 'MOD_MENU_COM_CONTENT');" id="amm_content">
								<?php echo JText::_('MOD_MENU_COM_CONTENT'); ?>
							</a>
						</div>	
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_articlemanager', 'index.php?option=com_content', 'MOD_MENU_COM_CONTENT_ARTICLE_MANAGER');" id="amm_articlemanager" <?php echo $this->icon_background('icon-16-article.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_CONTENT_ARTICLE_MANAGER'); ?>
							</a>
						</div>
						<div class="margin_2">
							<a href="javascript:do_menuitem('amm_articlenew', 'index.php?option=com_content&view=article&layout=edit', 'MOD_MENU_COM_CONTENT_NEW_ARTICLE');" id="amm_articlenew" <?php echo $this->icon_background('icon-16-newarticle.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_CONTENT_NEW_ARTICLE'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_catmanager', 'index.php?option=com_categories&extension=com_content', 'MOD_MENU_COM_CONTENT_CATEGORY_MANAGER');" id="amm_catmanager" <?php echo $this->icon_background('icon-16-category.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_CONTENT_CATEGORY_MANAGER'); ?>
							</a>
						</div>
						<div class="margin_2">
							<a href="javascript:do_menuitem('amm_articlecatnew', 'index.php?option=com_categories&view=category&layout=edit&extension=com_content', 'MOD_MENU_COM_CONTENT_NEW_CATEGORY');" id="amm_articlecatnew" <?php echo $this->icon_background('icon-16-newarticle.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_CONTENT_NEW_CATEGORY'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_featured', 'index.php?option=com_content&view=featured', 'MOD_MENU_COM_CONTENT_FEATURED');" id="amm_featured" <?php echo $this->icon_background('icon-16-featured.png'); ?>>
								<?php echo JText::_('MOD_MENU_COM_CONTENT_FEATURED'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_media', 'index.php?option=com_media', 'MOD_MENU_MEDIA_MANAGER');" id="amm_media" <?php echo $this->icon_background('icon-16-media.png'); ?>>
								<?php echo JText::_('MOD_MENU_MEDIA_MANAGER'); ?>
							</a>
						</div>
					</td>
					<td class="amm_quick_select_buttons">	
						<div>
							<a href="javascript:do_menuitem('amm_components', '#', 'MOD_MENU_COMPONENTS');" id="amm_components">
								<?php echo JText::_('MOD_MENU_COMPONENTS'); ?>
							</a>
						</div>							
						<?php						
						for($n = 0; $n < count($this->components_array_parents); $n++){
							echo '<div class="margin_'.$this->components_array_parents[$n][1].'">';
						 	echo '<a href="javascript:do_menuitem(\'amm_component_'.$this->components_array_parents[$n][0].'\', \''.$this->components_array_parents[$n][2].'\', \''.$this->components_array_parents[$n][5].'\');" id="amm_component_'.$this->components_array_parents[$n][0].'"';
							echo $this->icon_background($this->components_array_parents[$n][3], 'component');
							echo '>';
							echo $this->components_array_parents[$n][4];
							echo '</a>';							
							echo '</div>';	
								
							for($m = 0; $m < count($this->components_array_children); $m++){
								if($this->components_array_children[$m][5]==$this->components_array_parents[$n][0]){
									echo '<div class="margin_'.$this->components_array_children[$m][1].'">';									
									echo '<a href="javascript:do_menuitem(\'amm_component_'.$this->components_array_children[$m][0].'\', \''.$this->components_array_children[$m][2].'\', \''.$this->components_array_children[$m][6].'\');" id="amm_component_'.$this->components_array_children[$m][0].'"';
									echo $this->icon_background($this->components_array_children[$m][3], 'component');
									echo '>';
									echo htmlspecialchars($this->components_array_children[$m][4]);
									echo '</a>';							
									echo '</div>';	
								}						
							}											
						}
						
						
						?>		
					</td>
					<td class="amm_quick_select_buttons">
						<div>
							<a href="javascript:do_menuitem('amm_extensions', '#', 'MOD_MENU_EXTENSIONS_EXTENSIONS');" id="amm_extensions">
								<?php echo JText::_('MOD_MENU_EXTENSIONS_EXTENSIONS'); ?>
							</a>
						</div>	
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_extmanager', 'index.php?option=com_installer', 'MOD_MENU_EXTENSIONS_EXTENSION_MANAGER');" id="amm_extmanager" <?php echo $this->icon_background('icon-16-install.png'); ?>>
								<?php echo JText::_('MOD_MENU_EXTENSIONS_EXTENSION_MANAGER'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_modulemanager', 'index.php?option=com_modules', 'MOD_MENU_EXTENSIONS_MODULE_MANAGER');" id="amm_modulemanager" <?php echo $this->icon_background('icon-16-module.png'); ?>>
								<?php echo JText::_('MOD_MENU_EXTENSIONS_MODULE_MANAGER'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_pluginmanager', 'index.php?option=com_plugins', 'MOD_MENU_EXTENSIONS_PLUGIN_MANAGER');" id="amm_pluginmanager" <?php echo $this->icon_background('icon-16-plugin.png'); ?>>
								<?php echo JText::_('MOD_MENU_EXTENSIONS_PLUGIN_MANAGER'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_templatemanager', 'index.php?option=com_templates', 'MOD_MENU_EXTENSIONS_TEMPLATE_MANAGER');" id="amm_templatemanager" <?php echo $this->icon_background('icon-16-themes.png'); ?>>
								<?php echo JText::_('MOD_MENU_EXTENSIONS_TEMPLATE_MANAGER'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_langmanager', 'index.php?option=com_languages', 'MOD_MENU_EXTENSIONS_LANGUAGE_MANAGER');" id="amm_langmanager" <?php echo $this->icon_background('icon-16-language.png'); ?>>
								<?php echo JText::_('MOD_MENU_EXTENSIONS_LANGUAGE_MANAGER'); ?>
							</a>
						</div>
					</td>
					<td class="amm_quick_select_buttons">
						<div>
							<a href="javascript:do_menuitem('amm_help', '#');" id="amm_help">
								<?php echo JText::_('MOD_MENU_HELP'); ?>
							</a>
						</div>										
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_helpjoomla', 'index.php?option=com_admin&view=help', 'MOD_MENU_HELP_JOOMLA');" id="amm_helpjoomla" <?php echo $this->icon_background('icon-16-help.png'); ?>>								
								<?php echo JText::_('MOD_MENU_HELP_JOOMLA'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_forum', 'http://forum.joomla.org', 'MOD_MENU_HELP_SUPPORT_OFFICIAL_FORUM');" id="amm_forum" <?php echo $this->icon_background('icon-16-help-forum.png'); ?>>
								<?php echo JText::_('MOD_MENU_HELP_SUPPORT_OFFICIAL_FORUM'); ?>
							</a>
						</div>
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_documentation', 'http://docs.joomla.org', 'MOD_MENU_HELP_DOCUMENTATION');" id="amm_documentation" <?php echo $this->icon_background('icon-16-help-docs.png'); ?>>
								<?php echo JText::_('MOD_MENU_HELP_DOCUMENTATION'); ?>
							</a>
						</div>
						<?php if($this->helper->joomla_version < '3.0'){ ?>	
						<div class="margin_1">
							<a href="javascript:do_menuitem('amm_helplinks', 'index.php?option=com_admin&view=help#', 'MOD_MENU_HELP_LINKS');" id="amm_helplinks" <?php echo $this->icon_background('icon-16-links.png'); ?>>
								<?php echo JText::_('MOD_MENU_HELP_LINKS'); ?>
							</a>
						</div>
						<?php } ?>	
						<div class="margin_<?php
						if($this->helper->joomla_version >= '3.0'){
							echo '1';
						}else{
							echo '2';
						}
						?>">
							<a href="javascript:do_menuitem('amm_helpext', 'http://extensions.joomla.org/', 'MOD_MENU_HELP_EXTENSIONS');" id="amm_helpext" <?php echo $this->icon_background('icon-16-help-jed.png'); ?>>
								<?php echo JText::_('MOD_MENU_HELP_EXTENSIONS'); ?>
							</a>
						</div>
						<div class="margin_<?php
						if($this->helper->joomla_version >= '3.0'){
							echo '1';
						}else{
							echo '2';
						}
						?>">
							<a href="javascript:do_menuitem('amm_helptrans', 'http://community.joomla.org/translations.html', 'MOD_MENU_HELP_TRANSLATIONS');" id="amm_helptrans" <?php echo $this->icon_background('icon-16-help-trans.png'); ?>>
								<?php echo JText::_('MOD_MENU_HELP_TRANSLATIONS'); ?>
							</a>
						</div>
						<div class="margin_<?php
						if($this->helper->joomla_version >= '3.0'){
							echo '1';
						}else{
							echo '2';
						}
						?>">
							<a href="javascript:do_menuitem('amm_helpres', 'http://resources.joomla.org/', 'MOD_MENU_HELP_RESOURCES');" id="amm_helpres" <?php echo $this->icon_background('icon-16-help-jrd.png'); ?>>
								<?php echo JText::_('MOD_MENU_HELP_RESOURCES'); ?>
							</a>
						</div>
						<div class="margin_<?php
						if($this->helper->joomla_version >= '3.0'){
							echo '1';
						}else{
							echo '2';
						}
						?>">
							<a href="javascript:do_menuitem('amm_helpcom', 'http://community.joomla.org/', 'MOD_MENU_HELP_COMMUNITY');" id="amm_helpcom" <?php echo $this->icon_background('icon-16-help-community.png'); ?>>
								<?php echo JText::_('MOD_MENU_HELP_COMMUNITY'); ?>
							</a>
						</div>
						<div class="margin_<?php
						if($this->helper->joomla_version >= '3.0'){
							echo '1';
						}else{
							echo '2';
						}
						?>">
							<a href="javascript:do_menuitem('amm_helpsec', 'http://developer.joomla.org/security.html', 'MOD_MENU_HELP_SECURITY');" id="amm_helpsec" <?php echo $this->icon_background('icon-16-help-security.png'); ?>>
								<?php echo JText::_('MOD_MENU_HELP_SECURITY'); ?>
							</a>
						</div>
						<div class="margin_<?php
						if($this->helper->joomla_version >= '3.0'){
							echo '1';
						}else{
							echo '2';
						}
						?>">
							<a href="javascript:do_menuitem('amm_helpdev', 'http://developer.joomla.org/', 'MOD_MENU_HELP_DEVELOPER');" id="amm_helpdev" <?php echo $this->icon_background('icon-16-help-dev.png'); ?>>
								<?php echo JText::_('MOD_MENU_HELP_DEVELOPER'); ?>
							</a>
						</div>
						<div class="margin_<?php
						if($this->helper->joomla_version >= '3.0'){
							echo '1';
						}else{
							echo '2';
						}
						?>">
							<a href="javascript:do_menuitem('amm_helpshop', 'http://shop.joomla.org/', 'MOD_MENU_HELP_SHOP');" id="amm_helpshop" <?php echo $this->icon_background('icon-16-help-shop.png'); ?>>
								<?php echo JText::_('MOD_MENU_HELP_SHOP'); ?>
							</a>
						</div>
					</td>					
				</tr>			
			</table>								
		</fieldset>
		<fieldset class="adminform pi_wrapper_nice">
			<legend class="pi_legend"><?php echo JText::_('COM_ADMINMENUMANAGER_CUSTOM_LINKS'); ?></legend>
			<table class="adminlist tabletop amm_table">
				<tr>
					<td style="width: 150px; white-space: nowrap;">
						<?php echo JText::_('COM_ADMINMENUMANAGER_DIRECT_LINK').' '.$this->controller->amm_strtolower(JText::_('PLG_ARTICLE_BUTTON_ARTICLE')); ?>:  
					</td>
					<td style="width: 150px;">
						<input type="text" style="width: 500px;" value="index.php?option=com_content&amp;task=article.edit&amp;id=888" />
					</td>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_REPLACE'); ?> 888 > <?php echo JText::_('PLG_ARTICLE_BUTTON_ARTICLE').' '.JText::_('JGRID_HEADING_ID'); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px; white-space: nowrap;">
						<?php echo JText::_('COM_ADMINMENUMANAGER_DIRECT_LINK').' '.$this->controller->amm_strtolower(JText::_('COM_INSTALLER_TYPE_MODULE')); ?>:  
					</td>
					<td style="width: 150px;">
						<input type="text" style="width: 500px;" value="index.php?option=com_modules&amp;task=module.edit&amp;id=888" />
					</td>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_REPLACE'); ?> 888 > <?php echo JText::_('COM_INSTALLER_TYPE_MODULE').' '.JText::_('JGRID_HEADING_ID'); ?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px; white-space: nowrap;">
						<?php echo JText::_('COM_ADMINMENUMANAGER_DIRECT_LINK').' '.$this->controller->amm_strtolower(JText::_('COM_MENUS_ITEM_FIELD_ALIAS_MENU_LABEL')); ?>:  
					</td>
					<td style="width: 150px;">
						<input type="text" style="width: 500px;" value="index.php?option=com_menus&amp;task=item.edit&amp;id=888" />
					</td>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_REPLACE'); ?> 888 > <?php echo JText::_('COM_MENUS_ITEM_FIELD_ALIAS_MENU_LABEL').' '.JText::_('JGRID_HEADING_ID'); ?>
					</td>
				</tr>	
				<tr>
					<td style="width: 150px; white-space: nowrap;">
						<?php echo JText::_('COM_ADMINMENUMANAGER_DIRECT_LINK').' '.$this->controller->amm_strtolower(JText::_('COM_INSTALLER_TYPE_PLUGIN')); ?>:  
					</td>
					<td style="width: 150px;">
						<input type="text" style="width: 500px;" value="index.php?option=com_plugins&amp;task=plugin.edit&amp;extension_id=888" />
					</td>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_REPLACE'); ?> 888 > <?php echo JText::_('COM_INSTALLER_TYPE_PLUGIN').' '.JText::_('JGRID_HEADING_ID'); ?>
					</td>
				</tr>	
				<tr>
					<td style="width: 150px; white-space: nowrap;">
						<?php echo JText::_('COM_ADMINMENUMANAGER_DIRECT_LINK').' '.$this->controller->amm_strtolower(JText::_('JCATEGORY')); ?>:  
					</td>
					<td style="width: 150px;">
						<input type="text" style="width: 500px;" value="index.php?option=com_categories&amp;task=category.edit&amp;extension=com_content&amp;id=888" />
					</td>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_REPLACE'); ?> 888 > <?php echo JText::_('JCATEGORY').' '.JText::_('JGRID_HEADING_ID'); ?>
					</td>
				</tr>				
				<tr>
					<td style="width: 150px; white-space: nowrap;">
						<?php echo JText::_('COM_ADMINMENUMANAGER_LINK_TO_NEWARTICLE'); ?>:  
					</td>
					<td>
						<input type="text" style="width: 500px;" value="index.php?option=com_content&view=article&layout=edit&category_id_override=20" />
					</td>
					<td>
						<a href="http://www.pages-and-items.com/extensions/admin-menu-manager/faqs?faqitem=config_category" target="_blank"><?php echo JText::_('COM_ADMINMENUMANAGER_READ_MORE'); ?></a>						
					</td>
				</tr>
				<tr>
					<td style="width: 150px; white-space: nowrap;">
						<?php echo JText::_('COM_ADMINMENUMANAGER_LINK_TO_FILTER'); ?>:  
					</td>
					<td>
						<input type="text" style="width: 500px;" value="index.php?option=com_modules&filter_position=position-7" />
					</td>
					<td>
						<a href="http://www.pages-and-items.com/extensions/admin-menu-manager/faqs?faqitem=config_filter" target="_blank"><?php echo JText::_('COM_ADMINMENUMANAGER_READ_MORE'); ?></a>						
					</td>
				</tr>			
				<tr>
					<td style="width: 150px; white-space: nowrap;">
						<?php echo JText::_('COM_ADMINMENUMANAGER_DYNAMIC_REDIRECT2'); ?>:  
					</td>
					<td>
						<input type="text" style="width: 500px;" value="../index.php?option=com_redirectonlogin&view=dynamicredirect&id=18" />
					</td>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_DYNAMIC_REDIRECT').' '.$this->controller->amm_strtolower(JText::_('COM_INSTALLER_TYPE_COMPONENT')); ?> 
						<a href="http://www.pages-and-items.com/extensions/redirect-on-login" target="_blank">Redirect-on-Login</a>.					
					</td>
				</tr>				
			</table>
		</fieldset>
		<table id="amm_all_icons" class="adminlist tabletop amm_table">								
			<?php
			foreach($this->icons as $icon){	
				if($icon){
					if(strpos($icon, '*')){									
						echo '<tr><td style="padding-left: 5px; white-space: nowrap;">';
						$temp = str_replace('-*', '', $icon);
						echo $temp;
						echo '</td><td colspan="6">';
					}
					
					if(file_exists(JPATH_ROOT.$ds.'administrator'.$ds.$icon)){									
						echo '<a href="javascript:do_icon(\''.$icon.'\')" class="icon_with_class">';
						echo '<img src="'.$icon.'" alt="icon" />';
						echo '</a>';										
					}	
					
					if(strpos($icon, 'end-td-tr')){	
						echo '</td></tr>';
					}							
				}
			}
			?>	
		</table>
	</div>		
</form>