<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnTab_Osmembership extends JPlugin
{
	
	public function renderTabs($data, $jsnconfig)
	{
		$userID=JFactory::getUser()->id;
		$profileID=$data->id;
		/* Only Owner can see this tab */
		if($userID!=$profileID) return;

		$lang = JFactory::getLanguage();
		$lang->load('com_osmembership');
		$plugin=array(JText::_($this->params->get('tabtitle','Subscriptions')));

		/* Include Core */
		include JPATH_ADMINISTRATOR . '/components/com_osmembership/config.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		OSMembershipHelper::prepareRequestData();

		$input = new MPFInput();

		/* Store Site Title */
		$siteTitle=JFactory::getDocument()->getTitle();
		$layout = $input->get('layout', '');

		$input->set('option', 'com_osmembership');
		$input->set('view', 'subscriptions');
		$input->set('layout', 'default');

		ob_start();
		
		/* Compatibility fix MP version >2.12.0 */
		if(!is_array($MPConfig)) $MPConfig = $config;


		MPFController::getInstance('com_osmembership', $input, $MPConfig)
			->execute();

		
		
		$script='';
		$css='<style>.osm-page-title{display:none;}</style>';
		$plugin[]='<div id="osmembership-wrapper">'.ob_get_clean().'</div>'.$script.$css;

		/* Restore Site Title */
		JFactory::getDocument()->setTitle($siteTitle); 

		/* Restore Default Inputs */
		$input->set('option', 'com_jsn');
		$input->set('view', 'profile');
		$input->set('layout', $layout);

		return $plugin;
		
	}
}

?>