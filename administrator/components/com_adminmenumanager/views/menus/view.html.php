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

class adminmenumanagerViewMenus extends JViewLegacy{
	
	protected $items;	
	protected $state;
	protected $pagination;		
	
	public function display($tpl = null){	
		
		$controller = new adminmenumanagerController();	
		$this->assignRef('controller', $controller);
		
		$helper = $controller->get_helper();
		$this->assignRef('helper', $helper);
				
		$this->state = $this->get('State');	
		$this->items = $this->get('Items');			
		$this->pagination = $this->get('Pagination');
		
		//include mod_menu language. Reuse or die ;-)#
		$lang = JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);			
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);
		
		//toolbar	
		JToolBarHelper::custom('menu','new.png','new_f2.png',JText::_('JTOOLBAR_NEW'),false,false);	
		JToolBarHelper::divider();
		JToolBarHelper::custom('menus_copy','copy.png','copy_f2.png',JText::_('JLIB_HTML_BATCH_COPY'),false,false);	
		JToolBarHelper::divider();
		JToolBarHelper::custom('menu_delete','delete.png','delete_f2.png',JText::_('JTOOLBAR_DELETE'),false,false);		
		
		if($helper->joomla_version >= '3.0'){
			//sidebar
			$this->add_sidebar($controller);	
		}					

		parent::display($tpl);
	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_adminmenumanager&view=menus');			
				
		$helper->add_submenu();	
				
		$this->sidebar = JHtmlSidebar::render();
	}
	
	protected function getSortFields(){
	
		return array(
			'a.name' => JText::_('COM_USERS_HEADING_NAME'),
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
	
}
?>