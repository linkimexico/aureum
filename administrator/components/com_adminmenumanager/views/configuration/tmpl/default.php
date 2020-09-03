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

$checked = ' checked="checked"';
$selected = ' selected="selected"';

?>

<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){
	if (task == 'config_save') {
		submitform('config_save');
	}
	if (task == 'config_apply') {	
		document.getElementById('sub_task').value = 'apply';
		submitform('config_save');
	}		
	if (task == 'cancel') {
		document.location.href = 'index.php?option=com_adminmenumanager';		
	}	
}

function check_latest_version(){
	document.getElementById('version_checker_target').innerHTML = document.getElementById('version_checker_spinner').innerHTML;
	ajax_url = 'index.php?option=com_adminmenumanager&task=ajax_version_checker&format=raw';
	var req = new Request.HTML({url:ajax_url, update:'version_checker_target' });	
	req.send();
}

</script>

<form name="adminForm" id="adminForm" method="post" action="">
	<input type="hidden" name="option" value="com_adminmenumanager" />
	<input type="hidden" name="task" value="" />	
	<input type="hidden" name="sub_task" id="sub_task" value="" />	
	<?php echo JHTML::_( 'form.token' ); ?>	
	<?php if (!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>	
	<div id="j-main-container"<?php echo empty($this->sidebar) ? '' : ' class="span10"'; ?>>
		<div class="clr"> </div><!-- needed for some admin templates -->		
		<fieldset class="adminform pi_wrapper_nice">	
			<legend class="pi_legend"><?php echo JText::_('JFIELD_ACCESS_LABEL'); ?></legend>
			<table class="adminlist amm_table">	
				<tr>
					<td colspan="3">
						<?php 
						if($this->controller->get_version_type()=='free'){
							echo '<span class="amm_red">'.JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION').'</span>.<br />';
						}
						if(file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'controller.php')){
							//am is installed
							$link_to_am = '<a href="index.php?option=com_accessmanager">';
						}else{
							//am is not installed
							$link_to_am = '<a href="http://www.pages-and-items.com/extensions/access-manager" target="_blank">';
						}
						echo JText::_('COM_ADMINMENUMANAGER_ACCESS_INFO').'. ('.JText::_('COM_ADMINMENUMANAGER_TRY_ACCESSMANAGER').' '.$this->controller->amm_strtolower(JText::_('COM_CONFIG_COMPONENT_FIELDSET_LABEL')).' '.$link_to_am.' Access-Manager</a>)';
						?>.
					</td>
				</tr>
				<?php
				if($this->access_via_accessmanager){
					?>
						<tr>
							<td colspan="3">
								<table>
									<tr>
										<td>
											<img src="components/com_adminmenumanager/images/note.png" alt="note" />
										</td>
										<td style="vertical-align: middle;">
											<?php 								
												echo $this->controller->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_MENUITEMS')).' '. $this->controller->amm_strtolower(JText::_('JFIELD_ACCESS_LABEL')).' '.JText::_('COM_ADMINMENUMANAGER_VIA').' <a href="index.php?option=com_accessmanager&view=adminmenumanager">Access-Manager</a>';
											?>.
										</td>
									</tr>
								</table>
							</td>
						</tr>
					<?php
				}
				?>				
				<tr>		
					<td>
						<?php							
							echo JText::_('COM_MENUS_ITEM_FIELD_ALIAS_MENU_LABEL').' '.$this->controller->amm_strtolower(JText::_('JFIELD_ACCESS_LABEL'));	
						?>
					</td>
					<td class="nowrap" style="width: 230px;">
						<label style="white-space: nowrap;"><input type="radio" name="access_enabled" value="1" class="radio" <?php if($this->controller->amm_config['access_enabled']=='1'){echo 'checked="checked"';} ?> /> <?php echo $this->controller->amm_strtolower(JText::_('JENABLED')); ?></label>																
					</td>
					<td>
											
					</td>
				</tr>	
				<tr>		
					<td>
						
					</td>
					<td class="nowrap">										
					<label style="white-space: nowrap;"><input type="radio" name="access_enabled" value="0" class="radio" <?php if(!$this->controller->amm_config['access_enabled']){echo 'checked="checked"';} ?> /> <?php echo $this->controller->amm_strtolower(JText::_('JDISABLED')); ?></label>																	
					</td>
					<td>
						<?php
							echo JText::_('COM_ADMINMENUMANAGER_IF').' '.$this->controller->amm_strtolower(JText::_('JDISABLED')).' '.JText::_('COM_ADMINMENUMANAGER_ACCESS_DISABLED');	
						?>.						
					</td>
				</tr>	
				<tr>		
					<td style="width: 230px;">
						<?php 
						echo JText::_('COM_ADMINMENUMANAGER_ACCESS_BASED_ON');
						?>	
					</td>
					<td colspan="2">					
						<?php 						
						echo JText::_('COM_ADMINMENUMANAGER_ACCESS_BASED_ON_INFO');					
						?>.															
					</td>
				</tr>	
				<tr>		
					<td>
							
					</td>
					<td class="nowrap" style="width: 230px;">
						<label style="white-space: nowrap;"><input type="radio" name="based_on" value="group" class="radio" <?php if($this->controller->amm_config['based_on']=='group'){echo 'checked="checked"';} ?> /> <?php echo $this->controller->amm_strtolower(JText::_('MOD_MENU_COM_USERS_GROUPS')); ?></label>																		
					</td>
					<td>
						<?php
							echo JText::_('COM_ADMINMENUMANAGER_ONLY_BACKEND_GROUPS').'. <br />';
							echo JText::_('COM_ADMINMENUMANAGER_GROUPS_INHERIT');	
						?>:<br />
						<label style="white-space: nowrap;"><input type="radio" name="group_inheritance" value="1" class="radio" <?php if($this->controller->amm_config['group_inheritance']){echo 'checked="checked"';} ?> /> <?php echo $this->controller->amm_strtolower(JText::_('JYES')); ?></label><br />	
						<label style="white-space: nowrap;"><input type="radio" name="group_inheritance" value="0" class="radio" <?php if(!$this->controller->amm_config['group_inheritance']){echo 'checked="checked"';} ?> /> <?php echo $this->controller->amm_strtolower(JText::_('JNO')); ?></label>									
					</td>
				</tr>
				<tr>		
					<td>
							
					</td>
					<td class="nowrap">					
						<label style="white-space: nowrap;"><input type="radio" name="based_on" value="level" class="radio" <?php if($this->controller->amm_config['based_on']=='level'){echo 'checked="checked"';} ?> /> <?php echo $this->controller->amm_strtolower(JText::_('MOD_MENU_COM_USERS_LEVELS')); ?></label>														
					</td>
					<td>
						<?php 
							echo JText::_('MOD_MENU_COM_USERS_LEVELS').' '.JText::_('JGLOBAL_FIELD_FIELD_ORDERING_LABEL');
						?>:
						<select name="level_sort">
							<option value="ordering" <?php
							if($this->controller->amm_config['level_sort']=='ordering'){
								echo $selected;
							}
							?>><?php echo $this->controller->amm_strtolower(JText::_('MOD_MENU_COM_USERS_LEVELS').' '.JText::_('JGRID_HEADING_ORDERING')); ?></option>
							<option value="title"<?php
							if($this->controller->amm_config['level_sort']=='title'){
								echo $selected;
							}
							?>><?php echo $this->controller->amm_strtolower(JText::_('MOD_MENU_COM_USERS_LEVELS').' '.JText::_('JFIELD_TITLE_DESC')); ?></option>
						</select>
						
						<?php echo JText::_('COM_ADMINMENUMANAGER_LEVELORDER'); ?>.																								
					</td>
				</tr>
				<tr>		
					<td>
						'Super Users' <?php echo $this->controller->amm_strtolower(JText::_('JFIELD_ACCESS_LABEL')); ?>
					</td>
					<td colspan="2">
						<label>				
							<input type="checkbox" class="checkbox" name="super_user_sees_all" value="true" <?php if($this->controller->amm_config['super_user_sees_all']){echo 'checked="checked"';} ?> />
							'Super Users' <?php echo JText::_('COM_ADMINMENUMANAGER_SEE_ALL_MENUITEMS'); ?>.
						</label>	
						<a href="http://www.pages-and-items.com/extensions/admin-menu-manager/faqs?faqitem=config_joomla_menu" target="_blank"><?php echo JText::_('COM_ADMINMENUMANAGER_READ_MORE'); ?></a>																							
					</td>
				</tr>
				<tr>
					<td>
						<?php echo JText::_('JDEFAULT').' '.$this->controller->amm_strtolower(JText::_('JFIELD_ACCESS_LABEL')); ?>	
					</td>
					<td>
						<select name="default_access">	
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
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_DEFAULT_ACCESS_DESC'); ?>.	
					</td>
				</tr>
				<?php
				if(!$this->access_via_accessmanager){
					?>						
					<tr>
						<td colspan="3">
							<?php echo JText::_('COM_ADMINMENUMANAGER_ACCESSMANAGER'); ?> 
							<a href="http://www.pages-and-items.com/extensions/access-manager" target="_blank">Access-Manager</a>.
						</td>
					</tr>
				<?php
				}
				?>	
				<tr>		
					<td colspan="3">&nbsp;
						
					</td>
				</tr>
			</table>
		</fieldset>	
		<?php if(file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'templates'.$ds.'rt_missioncontrol'.$ds.'index.php')){ ?>
		<fieldset class="adminform pi_wrapper_nice">			
			<table class="adminlist amm_table">																						
				<tr>
					<td style="width: 20px;">
						<img src="components/com_adminmenumanager/images/note.png" alt="note" />
					</td>		
					<td style="vertical-align: middle;">						
						<?php 
						
						echo JText::_('JADMINISTRATOR').' '.$this->controller->amm_strtolower(JText::_('COM_INSTALLER_TYPE_TEMPLATE'));
						echo ' \'RocketTheme Mission control\' '.JText::_('COM_ADMINMENUMANAGER_IS_INSTALLED');
						
						?>.
						<a href="http://www.pages-and-items.com/extensions/admin-menu-manager/faqs?faqitem=config_mission_control" target="_blank"><?php echo $this->controller->amm_strtolower(JText::_('COM_CONTENT_READ_MORE_TITLE')); ?></a>
					</td>					
				</tr>
				<tr>		
					<td colspan="3">&nbsp;
						
					</td>
				</tr>
			</table>
		</fieldset>	
		<?php } ?>
		<fieldset class="adminform pi_wrapper_nice">
			<legend class="pi_legend"><?php echo JText::_('COM_ADMINMENUMANAGER_MULTI').' '.JText::_('COM_INSTALLER_TYPE_TYPE_LANGUAGE'); ?></legend>	
			<table class="adminlist amm_table">																						
				<tr>		
					<td style="width: 230px;">
						<?php echo JText::_('JSHOW').' '.$this->controller->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_MULTI')).' '.JText::_('COM_INSTALLER_TYPE_TYPE_LANGUAGE').' '.$this->controller->amm_strtolower(JText::_('JOPTIONS')); ?>	
					</td>
					<td colspan="2">
						<?php echo JText::_('JSHOW').' '.$this->controller->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_MULTI')).' '.JText::_('COM_INSTALLER_TYPE_TYPE_LANGUAGE').' '.$this->controller->amm_strtolower(JText::_('JOPTIONS')).' '.$this->controller->amm_strtolower(JText::_('JON')).' '.$this->controller->amm_strtolower(JText::_('COM_MENUS_ITEM_FIELD_ALIAS_MENU_LABEL')).' '.$this->controller->amm_strtolower(JText::_('JACTION_EDIT')).' '.JText::_('COM_ADMINMENUMANAGER_PAGE').' '.JText::_('COM_ADMINMENUMANAGER_AND').' '.$this->controller->amm_strtolower(JText::_('COM_MENUS_SUBMENU_ITEMS')).' '.JText::_('COM_ADMINMENUMANAGER_PAGE'); ?>.		
					</td>					
				</tr>
				<tr>		
					<td>						
					</td>
					<td style="white-space: nowrap; width: 230px;">						
						<label style="white-space: nowrap;"><input type="radio" name="multilanguage" value="1" class="radio" <?php if($this->controller->amm_config['multilanguage']){echo 'checked="checked"';} ?> /> <?php echo $this->controller->amm_strtolower(JText::_('Jshow')); ?></label>	
					</td>
					<td>
									
					</td>
				</tr>
				<tr>		
					<td>						
					</td>
					<td style="white-space: nowrap; width: 230px;">						
						<label style="white-space: nowrap;"><input type="radio" name="multilanguage" value="0" class="radio" <?php if(!$this->controller->amm_config['multilanguage']){echo 'checked="checked"';} ?> /> <?php echo $this->controller->amm_strtolower(JText::_('JHIDE')); ?></label>	
					</td>
					<td>
									
					</td>
				</tr>
				<tr>		
					<td colspan="3">&nbsp;
						
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="adminform pi_wrapper_nice">
			<legend class="pi_legend"><?php echo JText::_('JVERSION'); ?></legend>	
			<table class="adminlist amm_table">																						
				<tr>		
					<td style="width: 230px;">
						<?php echo JText::_('JVERSION'); ?>	
					</td>
					<td style="white-space: nowrap; width: 230px;">
						<?php echo $this->controller->version.' ('.$this->version_type.' '.$this->controller->amm_strtolower(JText::_('JVERSION')).')'; ?>
					</td>
					<td>
						<input type="button" value="<?php echo JText::_('COM_ADMINMENUMANAGER_CHECK_LATEST_VERSION'); ?>" onclick="check_latest_version();" />					
						<div id="version_checker_target"></div>	
						<span id="version_checker_spinner"><img src="components/com_adminmenumanager/images/processing.gif" alt="processing" /></span>				
					</td>
				</tr>	
				<tr>		
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_VERSION_CHECKER'); ?>	
					</td>
					<td>
						<label><input type="checkbox" class="checkbox" name="version_checker" value="true" <?php if($this->controller->amm_config['version_checker']){echo 'checked="checked"';} ?> /> <?php echo $this->controller->amm_strtolower(JText::_('JTOOLBAR_ENABLE')); ?></label>
					</td>
					<td>
						<?php echo JText::_('COM_ADMINMENUMANAGER_VERSION_CHECKER_INFO'); ?>.				
					</td>
				</tr>
				<tr>		
					<td>
						<?php echo 'Joomla '.$this->controller->amm_strtolower(JText::_('JLIB_INSTALLER_UPDATE')); ?>	
					</td>
					<td colspan="2">				
						<?php echo JText::_('COM_ADMINMENUMANAGER_UPDATER'); ?>.				
					</td>
				</tr>			
				<tr>		
					<td colspan="3">&nbsp;
						
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
</form>