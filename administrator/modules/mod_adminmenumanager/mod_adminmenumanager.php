<?php
/**
* @package Admin-Menu-Manager (com_adminmenumanager)
* @version 2.3.0
* @copyright Copyright (C) 2012 - 2018 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/



// no direct access.
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$ds = DIRECTORY_SEPARATOR;

//silly workaround for developers who install the trail version while totally ignoring 
//all warnings about that you need Ioncube installed or else it will criple the site
$am_trial_version = 0;

if($am_trial_version && !extension_loaded('ionCube Loader')){
	echo '<span style="color: red;">this trial version of Admin-Menu-Manager needs the PHP plugin \'Ioncube\' enabled on your server.</span>';
}else{

	if($params->get('adminmenumanagermenu', '')==''){
		echo JText::_('MOD_ADMINMENUMANAGER_NO_MENU_SELECTED').'.';
		echo ' <a href="index.php?option=com_modules&task=module.edit&id='.$module->id.'">select menu type</a>';
	}elseif(!file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_adminmenumanager'.$ds.'controller.php')){
		echo JText::_('MOD_ADMINMENUMANAGER_NOT_INSTALLED').'.';
	}else{	
			
		if(!class_exists('ModAdminMenuManagerHelper')){		
			require_once(JPATH_ROOT.$ds.'administrator'.$ds.'modules'.$ds.'mod_adminmenumanager'.$ds.'helper.php');
		}
		
		$adminmenumanagermenuhelper = new ModAdminMenuManagerHelper();
		$amm_menuitems = $adminmenumanagermenuhelper->get_menu_items($params);

		foreach($amm_menuitems as $key => $row){
			if(strpos($row->url, 'com_categories')){
				$encoded_url = base64_encode($row->url);
				$amm_menuitems[$key]->url = 'index.php?option=com_adminmenumanager&view=categorymanager&url='.$encoded_url;
			}
		}		
		
		$class_sfx = htmlspecialchars($params->get('class_sfx'));	
		$adminmenumanagerdisable = $params->get('adminmenumanagerdisable');
		
		require JModuleHelper::getLayoutPath('mod_adminmenumanager', 'default');
	}

}

?>