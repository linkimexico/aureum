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

jimport( 'joomla.application.component.view');

class adminmenumanagerViewInfo extends JViewLegacy{

	function display($tpl = null){
	
		$ds = DIRECTORY_SEPARATOR;
	
		$controller = new adminmenumanagerController();	
		$this->assignRef('controller', $controller);
		
		require_once(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_adminmenumanager'.$ds.'helpers'.$ds.'adminmenumanager.php');
		$helper = new adminmenumanagerHelper();
		
		//include mod_menu language. Reuse or die ;-)#
		$lang = JFactory::getLanguage();
		$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_modules', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_login', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('mod_stats_admin', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_contact', JPATH_ADMINISTRATOR, null, false);	
		
		if($helper->joomla_version >= '3.0'){
			//sidebar
			$this->add_sidebar($controller);	
		}		
		
		parent::display($tpl);
	}
	
	function add_sidebar($controller){
	
		JHtmlSidebar::setAction('index.php?option=com_adminmenumanager&view=info');	
				
		$controller->add_submenu();			
		
		$this->sidebar = JHtmlSidebar::render();
	}
	
}
?>