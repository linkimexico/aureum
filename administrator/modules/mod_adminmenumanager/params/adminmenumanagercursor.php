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

class JFormFieldAdminMenuManagerCursor extends JFormField{

	var $type = 'adminmenumanagercursor';	
	
	protected function getInput() {
	
		$cursor_array = array();
		
		/*
		$templates_array[0]->element = 'arrow';
		$templates_array[0]->name = htmlspecialchars(JText::_('MOD_ADMINMENUMANAGER_ARROW'));
		$templates_array[1]->element = 'hand';
		$templates_array[1]->name = htmlspecialchars(JText::_('MOD_ADMINMENUMANAGER_HAND'));
		*/
		
		$temp = new StdClass;
		$temp->element  = 'arrow';
		$temp->name  = htmlspecialchars(JText::_('MOD_ADMINMENUMANAGER_ARROW'));
		$cursor_array[] = $temp;
		
		$temp = new StdClass;
		$temp->element  = 'hand';
		$temp->name  = htmlspecialchars(JText::_('MOD_ADMINMENUMANAGER_HAND'));
		$cursor_array[] = $temp;
		
		return JHTML::_('select.genericlist', $cursor_array, $this->name, '', 'element', 'name', $this->value );			
	}	
	
	
}

?>