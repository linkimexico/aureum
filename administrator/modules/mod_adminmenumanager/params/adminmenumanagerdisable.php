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

class JFormFieldAdminMenuManagerDisable extends JFormField{

	var $type = 'adminmenumanagerdisable';	
	
	protected function getInput() {	
		
		$options_array = array();
		
		/*
		$options_array[0]->value = '1';
		$options_array[0]->label = $this->amm_strtolower(JText::_('JTOOLBAR_DISABLE')).' '.JText::_('MOD_ADMINMENUMANAGER_MENU');
		$options_array[1]->value = '0';
		$options_array[1]->label = $this->amm_strtolower(JText::_('JTOOLBAR_ENABLE')).' '.JText::_('MOD_ADMINMENUMANAGER_MENU');
		*/
		$temp = new StdClass;
		$temp->value  = '1';
		$temp->label  = $this->amm_strtolower(JText::_('JTOOLBAR_DISABLE')).' '.JText::_('MOD_ADMINMENUMANAGER_MENU');
		$options_array[] = $temp;
		
		$temp = new StdClass;
		$temp->value  = '0';
		$temp->label  = $this->amm_strtolower(JText::_('JTOOLBAR_ENABLE')).' '.JText::_('MOD_ADMINMENUMANAGER_MENU');
		$options_array[] = $temp;
		
				
	
		
		return JHTML::_('select.genericlist', $options_array, $this->name, '', 'value', 'label', $this->value );				
	}	

	function amm_strtolower($string){
		if(function_exists('mb_strtolower')){			
			$string = mb_strtolower($string, 'UTF-8');
		}
		return $string;
	}
}

?>