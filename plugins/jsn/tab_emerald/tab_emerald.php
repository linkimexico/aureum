<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnTab_Emerald extends JPlugin
{
	
	public function renderTabs($data, $config)
	{
		$userID=JFactory::getUser()->id;
		$profileID=$data->id;
		/* Only Owner can see this tab */
		if($userID!=$profileID) return;

		$lang = JFactory::getLanguage();
		$lang->load('com_emerald');
		$plugin=array(JText::_($this->params->get('tabtitle','Subscriptions')));

		/* View file */
		require_once(JPATH_SITE . '/components/com_emerald/views/emhistory/view.html.php');
		
		/* Model */
		require_once(JPATH_SITE . '/components/com_emerald/models/emhistory.php');

		/* Path */
		$view=new EmeraldViewEmHistory();
		$path=$view->get('_path');
		$path['template'][]=JPATH_SITE . '/components/com_emerald/views/emhistory/tmpl/';
		$view->set('_path',$path);

		$model=new EmeraldModelEmHistory();
		$view->setModel($model,true);

		/* Store Site Title */
		$siteTitle=JFactory::getDocument()->getTitle();
		
		ob_start();
		$view->display();
		
		$script='';
		$css='<style>
			#emerald-wrapper h1,#emerald-wrapper .page-header{display:none;}
		</style>';
		$plugin[]='<div id="emerald-wrapper">'.ob_get_clean().'</div>'.$script.$css;

		/* Restore Site Title */
		JFactory::getDocument()->setTitle($siteTitle); 
		
		return $plugin;
		
	}
}

?>