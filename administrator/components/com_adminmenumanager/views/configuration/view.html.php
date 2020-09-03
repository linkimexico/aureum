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

class adminmenumanagerViewConfiguration extends JViewLegacy{

	function display($tpl = null){
	
		$ds = DIRECTORY_SEPARATOR;
		
		$controller = new adminmenumanagerController();	
		$this->assignRef('controller', $controller);
		
		$helper = $controller->get_helper();
		
		$version_type = $controller->get_version_type();
		$this->assignRef('version_type', $version_type);
		
		//include mod_menu language. Reuse or die ;-)#
		$lang = JFactory::getLanguage();
		$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_config', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_content', JPATH_ROOT, null, false);	
		$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);
		
		$groups_levels = $helper->get_groups_levels();	
		$this->assignRef('groups_levels', $groups_levels);	
		
		$default_access_type = 'default_access_'.$controller->amm_config['based_on'];
		$this->assignRef('default_access_type', $default_access_type);		
		
		$access_via_accessmanager = $helper->check_if_access_via_accessmanager();
		$this->assignRef('access_via_accessmanager', $access_via_accessmanager);
		
		//toolbar	
		JToolBarHelper::custom( 'config_apply', 'apply.png', 'apply_f2.png', JText::_('JTOOLBAR_APPLY'), false, false );
		if (JFactory::getUser()->authorise('core.admin', 'com_adminmenumanager')) {
			JToolBarHelper::preferences('com_adminmenumanager');
		}		
		
		if($helper->joomla_version >= '3.0'){
			//sidebar
			$this->add_sidebar($controller);	
		}		
		
		parent::display($tpl);
	}	
	
	function add_sidebar($controller){
	
		JHtmlSidebar::setAction('index.php?option=com_adminmenumanager&view=configuration');	
				
		$controller->add_submenu();			
		
		$this->sidebar = JHtmlSidebar::render();
	}
	
	
}
?>