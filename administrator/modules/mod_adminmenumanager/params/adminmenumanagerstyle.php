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

class JFormFieldAdminMenuManagerStyle extends JFormField{

	var $type = 'adminmenumanagerstyle';	
	
	protected function getInput() {		
				 
		$db = JFactory::getDBO();
		
		//include mod_menu language. Reuse or die ;-)#
		$lang = JFactory::getLanguage();		
		$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);
		
		//get templates
		$query = $db->getQuery(true);
		$query->select('template, home');
		$query->from('#__template_styles');
		$query->where('client_id=1');			
		$templates = $db->setQuery($query);		
		$templates = $db->loadObjectList();
			
		//make array
		$templates_array = array();	
		$css_files_array = array('bluestork', 'hathor', 'rt_missioncontrol', 'isis');	
		
		/*
		$templates_array[0]->element = 'auto';
		$templates_array[0]->name = $this->amm_strtolower(JText::_('JGLOBAL_AUTO'));
		*/
		$temp = new StdClass;
		$temp->element  = 'auto';
		$temp->name  = $this->amm_strtolower(JText::_('JGLOBAL_AUTO'));
		$templates_array[] = $temp;
		
		/*
		$n = 1;
		$default_template = '';
		foreach($templates as $template){
			$template_name = $template->template;
			if($template->home){
				//default template
				$template_name .= ' ('.$this->amm_strtolower(JText::_('JDEFAULT')).' '.JText::_('COM_INSTALLER_TYPE_TYPE_TEMPLATE').')';
				$default_template = $template->template;		
			}
			if(in_array($template->template, $css_files_array)){
				$templates_array[$n]->element = $template->template;
				$templates_array[$n]->name = htmlspecialchars($template_name);
				$n++;
			}							
		}
		*/
		
		$default_template = '';
		foreach($templates as $template){
			$template_name = $template->template;
			if($template->home){
				//default template
				$template_name .= ' ('.$this->amm_strtolower(JText::_('JDEFAULT')).' '.JText::_('COM_INSTALLER_TYPE_TYPE_TEMPLATE').')';
				$default_template = $template->template;		
			}
			if(in_array($template->template, $css_files_array)){				
				$temp = new StdClass;
				$temp->element  = $template->template;
				$temp->name  =  htmlspecialchars($template_name);
				$templates_array[] = $temp;
			}							
		}
						
		//set default to current backend template
		if($this->value=='' && $default_template!=''){
			$this->value = $default_template;
		}		
		
		return JHTML::_('select.genericlist', $templates_array, $this->name, '', 'element', 'name', $this->value );			
	}	
	
	function amm_strtolower($string){
		if(function_exists('mb_strtolower')){			
			$string = mb_strtolower($string, 'UTF-8');
		}
		return $string;
	}

}

?>