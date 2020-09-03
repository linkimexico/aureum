<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnButton_Uddeim extends JPlugin
{
	
	public function renderProfileButtons($data, $config)
	{
		$userID=JFactory::getUser()->id;
		$profileID=$data->id;
		
		if($userID==0) return;

		if($userID!=$profileID) $output='<a class="btn btn-xs btn-default" href="'.JRoute::_('index.php?option=com_uddeim&task=new&recip='.$profileID,true).'"><i class="jsn-icon jsn-icon-paper-plane"></i> '.JText::_('COM_JSN_SENDMESSAGE').'</a>';
		else $output='<a class="btn btn-xs btn-default" href="'.JRoute::_('index.php?option=com_uddeim&task=inbox').'"><i class="jsn-icon jsn-icon-paper-plane"></i> '.JText::_('COM_JSN_MYMESSAGES').'</a>';

		return $output;
		
	}
}

?>