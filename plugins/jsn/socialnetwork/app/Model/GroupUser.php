<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class GroupUser extends AppModel {
	
	public $belongsTo = array( 'User', 
							   'Group'  => array( 'counterCache' => true,
											   	  'counterScope' => array('(GroupUser.status = 1 OR GroupUser.status = 3)')
							)	);
	
	/*
	 * Get groups based on type
	 * @param string $type - possible value: home, my, friends, user
	 * @param int $uid - user id
	 * @param int $page - page number
	 * @return array $groups
	 */
	public function getGroups( $type = null, $uid = null, $page = 1 )
	{
		$cond = array();
		
		switch ( $type )
		{			
			// Get my groups (admin and member)
			case 'home':
			case 'my':				
				if ( $uid )
					$cond = array( 'GroupUser.user_id' => $uid, 
								   '(GroupUser.status = ' . GROUP_USER_MEMBER . ' OR GroupUser.status = ' . GROUP_USER_ADMIN . ')' 
								);			
				break;
                
            case 'user':              
                if ( $uid )
                    $cond = array( 'GroupUser.user_id' => $uid, 
                                   '(GroupUser.status = ' . GROUP_USER_MEMBER . ' OR GroupUser.status = ' . GROUP_USER_ADMIN . ')',
                                   'Group.type <> ?' => PRIVACY_PRIVATE
                                );          
                break;
				
			// Get friends' groups
			case 'friends':				
				if ( $uid )
				{
					JsnApp::import('Model', 'Friend');	
					$friend = new Friend();
					$friends = $friend->getFriends( $uid );	
														
					$cond = array( 'GroupUser.user_id' => $friends, 
								   '(GroupUser.status = ' . GROUP_USER_MEMBER . ' OR GroupUser.status = ' . GROUP_USER_ADMIN . ')',
								   'Group.type <> ?' => PRIVACY_PRIVATE
								);
				}					
				break;
		}
		
		$groups = $this->find( 'all', array( 'conditions' => $cond, 
											 'fields' 	  => array( 'DISTINCT Group.id', 'Group.name', 'Group.description', 'Group.group_user_count', 'Group.photo', 'Group.type' ),
											 'limit' 	  => RESULTS_LIMIT, 
											 'page' 	  => $page,
											 //'order' 	  => 'GroupUser.id desc'
							) );
		
		return $groups;
	}
	
	/*
	 * Get users of a group based on $type	 
	 * @param int $group_id
	 * @param mixed $status from 0 to 5
	 * @param int $page - page number
	 * @return array $users
	 */
	public function getUsers( $group_id, $status = null, $page = null, $limit = RESULTS_LIMIT )
	{
		$cond = array( 'group_id' => $group_id );
		
		if ( $status !== null )
			$cond['status'] = $status;
		
		$users = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page, 'order' => 'GroupUser.id desc' ) );
							
		return $users;
	}
	
	/*
	 * Get user count of a group based on $status	 
	 * @param int $group_id
	 * @param mixed $status from 1 to 2
	 * @return int $count
	 */
	public function getUserCount( $group_id, $status )
	{
		$count = $this->find( 'count', array( 'conditions' => array( 'GroupUser.group_id' => $group_id, 
																	 'GroupUser.status' => $status
							 ) ) );
							
		return $count;
	}
	
	/*
	 * Get a list of user id based on status
	 * @param int $group_id
	 * @param int $status
	 * @return array $users
	 */
	public function getUsersList( $group_id, $status = null )
	{
		$cond = array( 'group_id' => $group_id );
		
		if ( !empty( $status ) )
			$cond['status'] = $status;
		
		$users = $this->find( 'list', array( 'conditions' => $cond,
											 'fields' => array( 'GroupUser.user_id' )
							) );
							
		return $users;
	}
	
	/*
	 * Get user's status of a group
	 * @param int $uid
	 * @param int $group_id
	 * @return array $status
	 */
	public function getMyStatus( $uid, $group_id )
	{
		$this->cacheQueries = true;
		
		$status = $this->find( 'first', array( 'conditions' => array( 'GroupUser.group_id' => $group_id,
																  	  'GroupUser.user_id' => $uid
							) ) );
							
		return $status;
	}

	/*
	 * Check if a user is a member of a group
	 * @param int $uid
	 * @param int $group_id
	 * @return boolean
	 */
	public function isMember( $uid, $group_id )
	{
		$my_status = $this->getMyStatus( $uid, $group_id );
		
		if ( empty( $my_status ) || !in_array( $my_status['GroupUser']['status'], array( GROUP_USER_MEMBER, GROUP_USER_ADMIN ) ) )
			return false;
			
		return true;
	}
	
	public function getMyGroupsList( $uid )
	{
		$groups = $this->find('list', array( 'conditions' => array( 'GroupUser.user_id' => $uid, 
																	'(GroupUser.status = ' . GROUP_USER_MEMBER . ' OR GroupUser.status = ' . GROUP_USER_ADMIN . ')' ),
											 'fields' => array('GroupUser.group_id') 
		)	);
		
		return $groups;
	}
}
