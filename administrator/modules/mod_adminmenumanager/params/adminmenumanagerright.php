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

class JFormFieldAdminMenuManagerRight extends JFormField{

	var $type = 'adminmenumanagerright';	
	
	protected function getInput() {
		
		$current_value = $this->value;
		$checked = ' checked="checked"';
		
		$html = '<div style="clear: both;"></div>';
		$html .= '<table>';
			$html .= '<tr>';
				$html .= '<td>';
					$html .= '<input type="radio" id="allow_right_yes" name="jform[params][allow_right]" value="1" style="margin-top: 0;"';
					if($current_value=='1'){
						$html .= $checked;
					}
					$html .= '/>';	
				$html .= '</td>';
				$html .= '<td>';
					$html .= '<label for="allow_right_yes" style="float: none; display: inline;">';
					$html .= JText::_('JYes');
					$html .= '</label>';
				$html .= '</td>';
				$html .= '<td>';
					$html .= '<label for="allow_right_yes">';
					$html .= '<img src="modules/mod_adminmenumanager/images/right_yes.png" alt="example menu layout" />';
					$html .= '</label>';
				$html .= '</td>';
				$html .= '<td>';
				$html .= '</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td>';
					$html .= '<input type="radio" id="allow_right_no" name="jform[params][allow_right]" value="2" style="margin-top: 0;"';
					if($current_value=='2'){
						$html .= $checked;
					}
					$html .= '/>';	
				$html .= '</td>';
				$html .= '<td>';
					$html .= '<label for="allow_right_no" style="float: none;  display: inline;">';
					$html .= JText::_('JNo');
					$html .= '</label>';
				$html .= '</td>';
				$html .= '<td>';
					$html .= '<label for="allow_right_no">';
					$html .= '<img src="modules/mod_adminmenumanager/images/right_no.png" alt="example menu layout" />';
					$html .= '</label>';
				$html .= '</td>';
				$html .= '<td>';
				$html .= '</td>';
			$html .= '</tr>';
		$html .= '</table>';
		$html .= '<div style="clear: both;"></div>';		
		
		$html .= '<br />';
		$html .= '<br />';
		$html .= '<div style="clear: both;"></div>';		
		
		return $html;		
	}	
	
	

}

?>