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

$checked = 'checked="checked"';
?>
<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){		
	if (task=='cancel'){			
		document.location.href = 'index.php?option=com_adminmenumanager&view=menus';		
	} else {
		if (document.getElementById('menu_name').value == '') {			
			alert('<?php echo addslashes(JText::_('COM_ADMINMENUMANAGER_NONAMEENTERED')); ?>');
			return;
		}		
		if (task=='menu_apply'){	
			document.adminForm.apply.value = '1';
		}
		if(task=='menu_save_as_copy'){
			<?php
			if($this->controller->get_version_type()=='free'){
				echo 'alert(\''.addslashes(JText::_('COM_ADMINMENUMANAGER_LIMITED_MENUS')).'\');';
				echo 'return;';
			}else{
			?>
				submitform('menu_save_as_copy');
				return false;
			<?php
			}
			?>
		}
		submitform('menu_save');
	}	
}

</script>
<form action="" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="apply" value="" />
	<input type="hidden" name="menu_id" value="<?php echo $this->menu->id; ?>" />			
	<?php echo JHtml::_('form.token'); ?>
	<?php if (!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>	
	<div id="j-main-container"<?php echo empty($this->sidebar) ? '' : ' class="span10"'; ?>>
		<div class="clr"> </div><!-- needed for some admin templates -->		
		<h2 class="amm_page_title"><?php 		
			if($this->menu->id==0){
				echo JText::_('JTOOLBAR_NEW').' ';
			}
			echo JText::_('COM_ADMINMENUMANAGER_MENU');
			if($this->menu->id){
				echo ': '.htmlspecialchars($this->menu->name); 
			}			
			?></h2>				
		<fieldset class="adminform pi_wrapper_nice">										
			<table class="adminlist tabletop amm_table">							
				<tr>
					<td class="amm_nowrap" style="width: 150px;">
						<?php
							echo JText::_('COM_USERS_HEADING_NAME');
						?>											
					</td>
					<td style="width: 150px;">
						<input type="text" name="name" id="menu_name" style="width: 450px;" value="<?php echo str_replace('"', '&quot;', $this->menu->name);?>" />
					</td>
					<td>&nbsp;
																		
					</td>
				</tr>	
				<tr>
					<td class="amm_nowrap">						
						<?php
							echo JText::_('JGLOBAL_DESCRIPTION');
						?>						
					</td>
					<td>						
						<input type="text" name="description" id="menu_description" style="width: 450px;" value="<?php echo str_replace('"', '&quot;', $this->menu->description);?>" />						
					</td>
					<td>
										
					</td>
				</tr>									
			</table>				
		</fieldset>
	</div>	
</form>