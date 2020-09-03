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

class JFormFieldAdminMenuManagerMenu extends JFormField{

	var $type = 'adminmenumanagermenu';	
	
	protected function getInput() {		
				 
		$db = JFactory::getDBO();
		
		$array_menus = array();
		
		//$array_menus[0]->id = '';
		//$array_menus[0]->name = JText::_("JOPTION_SELECT_MENU");
		
		$menu_item = new StdClass;
		$menu_item->id  = '';
		$menu_item->name  = JText::_("JOPTION_SELECT_MENU");
		
		$array_menus[] = $menu_item;


		$query = $db->getQuery(true);
		$query->select('id, name');
		$query->from('#__adminmenumanager_menus');		
		
		$menus = $db->setQuery((string)$query);				
		$menus = $db->loadObjectList();			
		
		/*
		$n = 1;
		foreach($menus as $menu){			
			$array_menus[$n]->id = $menu->id;
			$array_menus[$n]->name = htmlspecialchars($menu->name);	
			$n++;
		}	
		*/
		
		foreach($menus as $menu){
			$menu_item = new StdClass;
			$menu_item->id  = $menu->id;
			$menu_item->name  = htmlspecialchars($menu->name);
			$array_menus[] = $menu_item;
		}
		
		$html = JHTML::_('select.genericlist', $array_menus, $this->name, '', 'id', 'name', $this->value);
		$html .= '<div style="clear: both;"></div>';		
		
		$html .= '<br />';
		$html .= '<br />';
		$html .= '<div style="clear: both;"></div>';
		
		return 	$html;	
	}	

}

?>