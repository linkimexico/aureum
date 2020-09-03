<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnSkeleton extends JPlugin
{
	

	
	
	public function triggerFieldAvatarUpdate($user,&$data,$changed,$isNew)
	{
		echo $user->avatar; // Old avatar value
		echo $data['avatar']; // New avatar value
	}
	



	public function triggerProfileUpdate($user,&$data,$changed,$isNew)
	{
		if(in_array('avatar',$changed))
		{
			echo $user->avatar; // Old avatar value
			echo $data['avatar']; // New avatar value
		}
	}
	
	
	

}

?>