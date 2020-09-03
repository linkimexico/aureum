<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnTab_Osmembership_renew extends JPlugin
{
	
	public function renderTabs($data, $jsnconfig)
	{
		$userID=JFactory::getUser()->id;
		$profileID=$data->id;
		/* Only Owner can see this tab */
		if($userID!=$profileID) return;

		$lang = JFactory::getLanguage();
		$lang->load('com_osmembership');
		$plugin=array(JText::_($this->params->get('tabtitle','Renew,Upgrade Plans')));

		/* Include Core */
		include JPATH_ADMINISTRATOR . '/components/com_osmembership/config.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		OSMembershipHelper::prepareRequestData();

		$input = new MPFInput();

		/* Store Site Title */
		$siteTitle=JFactory::getDocument()->getTitle();
		$layout = $input->get('layout', '');

		$input->set('option', 'com_osmembership');
		$input->set('layout', 'default');

		ob_start();

		$item   = OSMembershipHelperSubscription::getMembershipProfile($userID);
		if (!$item){
			echo('<div class="alert alert-message">'.JText::_('OSM_DONOT_HAVE_SUBSCRIPTION_RECORD_TO_RENEW').'</div>');
		}
		else
		{
			/* Compatibility fix MP version >2.12.0 */
			if(!is_array($MPConfig)) $MPConfig = $config;

			// RENEW
			list($planIds, $renewOptions) = OSMembershipHelperSubscription::getRenewOptions($userID);
			if (!empty($planIds))
			{
				$input->set('view', 'renewmembership');
				MPFController::getInstance('com_osmembership', $input, $MPConfig)->execute();
			}

			// UPGRADE
			$upgradeRules = OSMembershipHelperSubscription::getUpgradeRules($item->user_id);
			if(count($upgradeRules))
			{
				$input->set('view', 'upgrademembership');
				MPFController::getInstance('com_osmembership', $input, $MPConfig)->execute();
			}
			if(empty($planIds) && !count($upgradeRules))
			{
				echo('<div class="alert alert-message">'.JText::_('OSM_NO_RENEW_OPTIONS_AVAILABLE').'</div>');
				echo('<div class="alert alert-message">'.JText::_('OSM_NO_UPGRADE_OPTIONS_AVAILABLE').'</div>');
			}
		}

		
		
		$script='';
		$css='';
		$plugin[]='<div id="osmembership-plan-wrapper">'.ob_get_clean().'</div>'.$script.$css;

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