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

?>
<script language="JavaScript" type="text/javascript">

Joomla.submitbutton = function(task){		
	if(task=='download_csv' || task=='download_txt'){	
		if (document.adminForm.textarea_csv.value == '') {						
			alert('<?php echo addslashes(JText::_('COM_MENUS_NO_ITEM_SELECTED')); ?>');
			return;
		}
	}
	if(task=='download_csv'){			
		//document.location.href = 'index.php?option=com_adminmenumanager&view=menuitemsexport&layout=csv';								
		Joomla.submitform('download_csv');		
	}	
	if(task=='download_txt'){					
		Joomla.submitform('download_txt');
	}
	//this needs to be here as the normal back button does not display icon in Joomla 3
	//so I needed to make this a custom button, therefore script needs to be here		
	if(task=='back_to_menuitems'){					
		document.location.href = 'index.php?option=com_adminmenumanager&view=menuitems';
	}	
}

</script>
<form action="" method="post" name="adminForm" id="adminForm">
	<div class="width-100 fltlft">	
		<div class="clr"> </div><!-- needed for some admin templates -->				
		<fieldset class="adminform pi_wrapper_nice">	
			<legend class="pi_legend"><?php echo JText::_('COM_MENUS_SUBMENU_ITEMS').' '.$this->controller->amm_strtolower(JText::_('JTOOLBAR_EXPORT')); ?></legend>	
			<table class="adminlist tabletop amm_table">							
				<tr>
					<td>
						<textarea name="textarea_csv" style="width: 800px; height: 200px;"><?php echo $this->csv_string; ?></textarea>										
					</td>
				</tr>	
				<tr>
					<td>						
						<button onclick="Joomla.submitbutton('download_csv');"><?php echo JText::_('COM_ADMINMENUMANAGER_DOWNLOAD').' '.JText::_('COM_ADMINMENUMANAGER_AS').' .csv '.$this->controller->amm_strtolower(JText::_('JLIB_FORM_VALUE_CACHE_FILE')); ?></button>	
						<?php
						if($this->controller->get_version_type()=='free'){
							echo '<span class="red">';
							echo JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION');
							echo '</span>';
						}
						?>									
					</td>
				</tr>
				<tr>
					<td>
						<button onclick="Joomla.submitbutton('download_txt');"><?php echo JText::_('COM_ADMINMENUMANAGER_DOWNLOAD').' '.JText::_('COM_ADMINMENUMANAGER_AS').' .txt '.$this->controller->amm_strtolower(JText::_('JLIB_FORM_VALUE_CACHE_FILE')); ?></button>	
						<?php
						if($this->controller->get_version_type()=='free'){
							echo '<span class="red">';
							echo JText::_('COM_ADMINMENUMANAGER_NOT_IN_FREE_VERSION');
							echo '</span>';
						}
						?>										
					</td>
				</tr>											
			</table>						
		</fieldset>
	</div>	
	<input type="hidden" name="task" value="" />		
	<?php echo JHtml::_('form.token'); ?>
</form>
