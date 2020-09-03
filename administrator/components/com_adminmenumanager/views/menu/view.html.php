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

class adminmenumanagerViewMenu extends JViewLegacy{
	
	protected $items;	
	protected $state;
	protected $pagination;		
	
	public function display($tpl = null){	
	
		$db = JFactory::getDBO();
		
		$controller = new adminmenumanagerController();	
		$this->assignRef('controller', $controller);
		
		$helper = $controller->get_helper();
		
		//get menu id
		$id = intval(JRequest::getVar('id', ''));		
				
		//get data
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__adminmenumanager_menus');
		$query->where('id='.$id);			
		$menus = $db->setQuery($query, 0, 1);				
		$menus = $db->loadObjectList();
		
		//set defaults for new
		$menu = (object)'';			
		$menu->id = 0;
		$menu->name = '';
		$menu->description = '';	
			
		foreach($menus as $temp){
			$menu = $temp;	
			$menu->id = $temp->id;
			$menu->name = $temp->name;
			$menu->description = $temp->description;
		}	
		$this->assignRef('menu', $menu);
		
		//include languages
		$lang = JFactory::getLanguage();		
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);
		
		//toolbar		
		JToolBarHelper::apply('menu_apply', 'JToolbar_Apply');
		JToolBarHelper::save('menu_save', 'JToolbar_Save');
		if($menu->id){
			JToolBarHelper::save2copy('menu_save_as_copy');
		}
		JToolBarHelper::cancel('cancel', 'JToolbar_Close');	
		
		if($helper->joomla_version >= '3.0'){
			//sidebar
			$this->add_sidebar($controller);	
		}						

		parent::display($tpl);
	}
	
	function add_sidebar($controller){
	
		JHtmlSidebar::setAction('index.php?option=com_adminmenumanager&view=menu');	
				
		$controller->add_submenu();			
		
		$this->sidebar = JHtmlSidebar::render();
	}
	
}
?>