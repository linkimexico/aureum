<?php
/**
* @package Admin-Menu-Manager (com_adminmenumanager)
* @version 2.3.0
* @copyright Copyright (C) 2012 - 2018 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/



// no direct access
defined('_JEXEC') or die;

if(!defined('DSPI')){//joomla 3 undefined DS workaround
	$temp = DIRECTORY_SEPARATOR;//separate because Ioncube can't handle it otherwise
	define('DSPI', $temp);
}

class JFormFieldAdminMenuManagerModpos extends JFormField{

	var $type = 'adminmenumanagermodpos';	
	
	protected function getInput() {		
				 
		$db = JFactory::getDBO();	
		
		$return = '<div style="clear: both; font-size: 1.2em; font-weight: bold;">';
		$return .= JText::_('MOD_ADMINMENUMANAGER_PUBLISH_TO');		
		$return .= ' \'menu\'.<br /><br /><br /></div>';
		
		if(file_exists(JPATH_ROOT.DSPI.'administrator'.DSPI.'templates'.DSPI.'rt_missioncontrol'.DSPI.'index.php')){
			$return .= '<div style="clear: both; font-size: 1.2em; font-weight: bold;">';			
			if(file_exists(JPATH_ROOT.DSPI.'administrator'.DSPI.'components'.DSPI.'com_adminmenumanager'.DSPI.'images'.DSPI.'note.png')){
				$return .= '<img src="components/com_adminmenumanager/images/note.png" alt="note" />';
			}elseif(file_exists(JPATH_ROOT.DSPI.'administrator'.DSPI.'templates'.DSPI.'bluestork'.DSPI.'images'.DSPI.'notice-note.png')){
				$return .= '<img src="templates/bluestork/images/notice-note.png" alt="note" />';
			}	
			$lang = JFactory::getLanguage();			
			$lang->load('com_content', JPATH_ROOT, null, false);
			$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);
			$return .= JText::_('JADMINISTRATOR').' '.$this->amm_strtolower(JText::_('COM_INSTALLER_TYPE_TEMPLATE'));			
			$return .= ' \'RocketTheme Mission control\' '.JText::_('MOD_ADMINMENUMANAGER_IS_INSTALLED').'. ';	
			$return .= '<br /><a href="http://www.pages-and-items.com/extensions/admin-menu-manager/faqs?faqitem=config_mission_control" target="_blank">';			
			$return .= JText::_('COM_CONTENT_READ_MORE_TITLE');
			$return .= '</a>';	
			$return .= '<br /><br /><br /></div>';
		}	
		
		return $return;		
	}	
	
	protected function getLabel() {	
		return '';
	}
	
	function amm_strtolower($string){
		if(function_exists('mb_strtolower')){			
			$string = mb_strtolower($string, 'UTF-8');
		}
		return $string;
	}
	
	

}

?>