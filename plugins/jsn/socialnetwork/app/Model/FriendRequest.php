<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class FriendRequest extends AppModel {
		
	public $belongsTo = array( 'User'  => array( 'counterCache' => true ) );
							
	/*
	 * Check if there's already a friend request between $uid1 and $uid2
	 */
	public function existRequest( $uid1, $uid2 )
	{
		$count = $this->find( 'count', array( 'conditions' => array( 'FriendRequest.sender_id' => $uid1, 
																	 'FriendRequest.user_id'   => $uid2
											)	)	);
											
		return $count > 0;
	}
	
	/*
	 * Get friend requests of $uid
	 */
	public function getRequests( $uid = null )
	{
		$this->unbindModel(
			array('belongsTo' => array('User'))
		);

		$this->bindModel(
			array('belongsTo' => array(
					'Sender' => array(
						'className' => 'User',
						'foreignKey' => 'sender_id'
					)
				)
			)
		);

		$requests = $this->findAllByUserId( $uid );
		
		return $requests;
	}
	
	public function getRequestsList( $uid )
	{
		$requests = $this->find( 'list' , array( 'conditions' => array( 'FriendRequest.sender_id' => $uid ), 
												 'fields' => array( 'user_id' ) 
							) );	
		return $requests;
	}
	
	/*
	 * Get friend request details
	 */
	public function getRequest( $request_id )
	{
		$this->bindModel(
			array('belongsTo' => array(
					'Sender' => array(
						'className' => 'User',
						'foreignKey' => 'sender_id'
					)
				)
			)
		);

		$request = $this->findById( $request_id );
		
		return $request;
	}
}
 