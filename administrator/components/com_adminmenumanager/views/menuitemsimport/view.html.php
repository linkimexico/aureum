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

class adminmenumanagerViewMenuitemsimport extends JViewLegacy{		
	
	public function display($tpl = null){	
	
		$db = JFactory::getDBO();
		$ds = DIRECTORY_SEPARATOR;
		
		$controller = new adminmenumanagerController();	
		$this->assignRef('controller', $controller);
		
		require_once(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_adminmenumanager'.$ds.'helpers'.$ds.'adminmenumanager.php');
		$helper = new adminmenumanagerHelper();
		$this->assignRef('helper', $helper);
		
		$this->assignRef('template', $helper->template);
		
		$menu = intval(JRequest::getVar('menu'));		
		
		if(!$menu){
			//get the first menu
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__adminmenumanager_menus');			
			$query->order('ordering');
			$rows = $db->setQuery($query);				
			$rows = $db->loadObjectList();				
			foreach($rows as $row){		
				$menu = $row->id;	
				break;
			}			
		}
		$this->assignRef('menu', $menu);			
		
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__adminmenumanager_menus');
		$query->where('id='.$menu);		
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();			
		foreach($rows as $row){		
			$menu_name = $row->name;	
		}
		$this->assignRef('menu_name', $menu_name);	
						
		//include languages
		$lang = JFactory::getLanguage();		
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);
		$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_media', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);
		
		$joomla_menuitems = $helper->get_joomla_menuitems();		
		$this->assignRef('joomla_menuitems', $joomla_menuitems);		
		
		$groups_levels = $helper->get_groups_levels();	
		$this->assignRef('groups_levels', $groups_levels);	
		
		$menuitems = $helper->get_menuitems($menu, 0);	
		$this->assignRef('menuitems', $menuitems);		
		
		
		$array_first_items = array();
		$array_last_items = array();
		$columns = 1;
		for($n = 1; $n <= count($this->joomla_menuitems); $n++){
			if($this->joomla_menuitems[$n][2]=='#' && $columns<=7 && $n!=5){
				$array_first_items[] = $n;
				if($n!=1){
					$array_last_items[] = $n-1;
				}
				$columns++;
			}
		}	
		$array_last_items[] = $n;	
		$this->assignRef('array_first_items', $array_first_items);
		$this->assignRef('array_last_items', $array_last_items);
		
		
		//toolbar			
		JToolBarHelper::save('menuitemsimport_save', 'JToolbar_Save');		
		JToolBarHelper::cancel('cancel', 'JToolbar_Close');								

		parent::display($tpl);
	}
	
	
	
}
?>