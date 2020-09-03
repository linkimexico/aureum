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

class adminmenumanagerViewCategorymanager extends JViewLegacy{

	function display($tpl = null){	
	
		$app = JFactory::getApplication();			
		
		$return = JRequest::getVar('url', '');
		$return = base64_decode($return);
		if($return){
			$url = $return;
		}else{
			$url = 'index.php';
		}
		
		$app->redirect($url);	
		
	}
	
}
?>