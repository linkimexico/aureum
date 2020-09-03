<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class ConversationUser extends AppModel 
{
	public $belongsTo = array( 'Conversation',
							   'User'  => array(
							   		'counterCache' => true, 
							   		'counterScope' => array( 'unread' => 1 )
							 )	);
							 
	/*
	 * Get participants list of $msg_id
	 * @param int $msg_id
	 * @return array $users
	 */

	public function getUsersList( $msg_id )
	{
		$users = $this->find( 'list', array( 'conditions' => array( 'ConversationUser.conversation_id' => $msg_id ),
											 'fields' 	  => array( 'user_id' )
							) 	);
		return $users;
	}
	
	public function afterFind($results,$primary){
		require_once(JPATH_SITE.'/components/com_jsn/helpers/helper.php');
		foreach($results as &$result){
			if((isset($result['Conversation']['LastPoster']) && isset($result['Conversation']['LastPoster']['id'])))
			{
				$jsnUser=JsnHelper::getUser($result['Conversation']['LastPoster']['id']);
				$result['Conversation']['LastPoster']['active']=!$jsnUser->block;
				$result['Conversation']['LastPoster']['name']=$jsnUser->getFormatName();
				$result['Conversation']['LastPoster']['photo']=$jsnUser->avatar;
				$result['Conversation']['LastPoster']['avatar']=$jsnUser->avatar_mini;
				$result['Conversation']['LastPoster']['email']=$jsnUser->email;
				if(isset($jsnUser->params['timezone'])) $result['Conversation']['LastPoster']['timezone']=$jsnUser->params['timezone'];
			}
		}
		return $results;
	}
}
 