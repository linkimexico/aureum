<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class FriendsController extends AppController
{
	public function ajax_sendRequest()
	{			
		$this->autoRender = false;
		$this->_checkPermission( array( 'confirm' => true ) );
		
		$uid = $this->Session->read('uid');
		$cuser = $this->_getUser();
		$requestdata = $this->request->data;
		
		if ( $uid == $requestdata['user_id'] )
		{
			echo __('You cannot send friend request to yourself');
			return;
		}
		
		// check if users are already friends
		if ( $this->Friend->areFriends( $uid, $requestdata['user_id'] ) )
		{
			echo __('You are already a friend of this user');
			return;
		}

		$this->loadModel( 'FriendRequest' );
		if ( $this->FriendRequest->existRequest( $uid, $requestdata['user_id'] ) )
		{
			echo __('You have already sent a friend request to this user');
			return;
		}

		$requestdata['sender_id'] = $uid;

		if ( $this->FriendRequest->save($requestdata) )
		{
			echo __('Your request has been successfully sent');

			// add notification
			$this->loadModel( 'Notification' );
			$this->Notification->record( array( 'recipients'  => $requestdata['user_id'],
												'sender_id'   => $uid,
												'action'	  => 'friend_add',
												'url' 		  => '/home/index/tab:friend-requests'
										) );
										
			$this->loadModel( 'User' );
			$user = $this->User->findById( $requestdata['user_id'] );
										
			if ( $user['User']['notification_email'] )
    			$this->_sendEmail( $user['User']['email'], 
    							   h($cuser['name']) . ' ' . __('wants to be friends with you'), 
    							   'friend_request', 
    							   array( 'user'    => $cuser,
    							   		  'message' => h($requestdata['message'])
    							)	);
		}
	}

	public function ajax_add($id = null)
	{
		$id = intval($id);
		$this->_checkPermission( array( 'confirm' => true ) );		
		$uid = $this->Session->read('uid');
		
		if ( $uid == $id )
		{
			$this->autoRender = false;
			echo __('You cannot send friend request to yourself');
			return;
		}

		// check if users are already friends
		if ( $this->Friend->areFriends( $uid, $id ) )
		{
			$this->autoRender = false;
			echo __('You are already a friend of this user');
			return;
		}

		// check if this user has already sent a request
		$this->loadModel( 'FriendRequest' );
		if ( $this->FriendRequest->existRequest( $uid, $id ) )
		{
			$this->autoRender = false;
			echo __('You have already sent this user a friend request');
			return;
		}
		
		// check if the other user has already sent a request
		if ( $this->FriendRequest->existRequest( $id, $uid ) )
		{
			$this->autoRender = false;
			echo __('This user has already sent you a friend request');
			echo '<br /><br /><a class="button button-action" href="'.$this->request->base.'/home/index/tab:friend-requests">';
			echo __('Friend Requests');
			echo '</a>';
			return;
		}
		
		// nothing? display the form
		$this->loadModel( 'User' );				
		$user = $this->User->findById($id);
		$this->set('user', $user);
	}
	
	public function ajax_requests()
	{
		$this->_checkPermission();		
		$uid = $this->Session->read('uid');
		
		$this->loadModel( 'FriendRequest' );
		$requests = $this->FriendRequest->getRequests( $uid );

		$this->set('requests', $requests);
	}

	public function ajax_respond()
	{
		$this->autoRender = false;
		$this->loadModel( 'FriendRequest' );
		
		$requestdata = $this->request->data;
		$uid = $this->Session->read('uid');
		$cuser = $this->_getUser();

		$request = $this->FriendRequest->getRequest( $requestdata['id'] );

		if ( !empty($request) )
		{
			$status = $requestdata['status'];
			$this->FriendRequest->id = $requestdata['id'];			

			if ( !empty($status) )
			{
				// insert to friends table
				$this->Friend->save( array('user_id' => $uid, 'friend_id' => $request['Sender']['id']) );
				$this->Friend->create();
				$this->Friend->save( array('user_id' => $request['Sender']['id'], 'friend_id' => $uid) );
				
				// insert into activity feed
				$this->loadModel( 'Activity' );		
				$activity = $this->Activity->getRecentActivity( 'friend_add', $uid );				
												 
				if ( !empty( $activity ) )
				{
					// aggregate activities
					$user_ids = explode( ',', $activity['Activity']['items'] );
                    
                    if ( !in_array($request['Sender']['id'], $user_ids) )
					   $user_ids[] = $request['Sender']['id'];
					
					$this->Activity->id = $activity['Activity']['id'];
					$this->Activity->save( array( 'items'   => implode( ',', $user_ids ),
												  'params'	=> '',
												  'privacy'	=> 1,
												  'query'	=> 1
										) );
				}
				else
				{
					$this->Activity->save( array( 'type'      => 'user',
												  'action'    => 'friend_add',
												  'user_id'   => $uid,
												  'item_type' => APP_USER,
												  'params' 	  => '<a href="' . $this->request->base . '/users/view/' . $request['Sender']['id'] . '">' . h($request['Sender']['name']) . '</a>',
												  'items'	  => $request['Sender']['id']					  
										) );
				}
				
				// send a notification to the sender				
				$this->loadModel( 'Notification' );
				$this->Notification->record( array( 'recipients'  => $request['Sender']['id'],
													'sender_id'   => $uid,
													'action'	  => 'friend_accept',
													'url' 		  => '/users/view/' . $uid
											) );
											
				// add private activity to sender's wall
				$this->Activity->create();
				$this->Activity->save( array( 'type'      => 'user',
											  'action'    => 'friend_add',
											  'user_id'   => $request['Sender']['id'],
											  'item_type' => APP_USER,
											  'params' 	  => '<a href="' . $this->request->base . '/users/view/' . $uid . '">' . h($cuser['name']) . '</a>',
											  'items'	  => $uid,											 
											  'privacy'	  => 3
									) );

				echo __('You and %s are now friends', '<a href="' . $this->request->base . '/users/view/' . $request['Sender']['id'] . '">' . h($request['Sender']['name']) . '</a>');
			}
			else
				echo __('You have deleted the request. The sender will not be notified');
			
			$this->FriendRequest->delete( $requestdata['id'] );
		}
	}

	public function ajax_remove()
	{
		$this->autoRender = false;
		$this->_checkPermission();
		
		$uid = $this->Session->read('uid');
		$friend_id = $this->request->data['id'];
		
		$this->Friend->deleteAll(array('Friend.user_id' => $uid, 'Friend.friend_id' => $friend_id), true, true);
		$this->Friend->deleteAll(array('Friend.user_id' => $friend_id, 'Friend.friend_id' => $uid), true, true);
	}
	
	public function ajax_invite()
	{
		if ( !empty( $this->request->data['to'] ) )
		{
			$this->autoRender = false;			
			$cuser = $this->_getUser();
			$jsnsocial_settings = $this->_getSettings();
			
			$emails = explode( ',', $this->request->data['to'] );
			
			$i = 1;
			foreach ($emails as $email)
			{
				if ( $i <= 10 )
				{
					if ( Validation::email( trim($email) ) )
					{
						$this->_sendEmail( trim($email),
										   $cuser['name'] . ' ' . __('invited you to join %s', $jsnsocial_settings['site_name']),
										   'invite',
										   array( 'user' => $cuser, 'message' => $this->request->data['message'] )
										 );
					}
				}
				$i++;
			}			
		}
	}
	
	public function ajax_suggestions()
	{
		$this->_checkPermission();
		$uid = $this->Session->read('uid');
		
		$suggestions = $this->Friend->getFriendSuggestions( $uid, true );
		$this->set('suggestions', $suggestions);
	}
	
	public function ajax_show_mutual( $user_id )
	{
		$user_id = intval($user_id);
		$this->_checkPermission();
		$uid = $this->Session->read('uid');
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
		
		$users = $this->Friend->getMutualFriends( $user_id, $uid, RESULTS_LIMIT, $page );
		
		$this->set('users', $users);
		$this->set('page', $page);
		$this->set('more_url', '/friends/ajax_show_mutual/' . $user_id . '/page:' . ( $page + 1 ) );
		
		$this->render('/Elements/ajax/user_overlay');	
	}
    
    public function do_get_json()
    {
        $this->_checkPermission();
        $uid = $this->Session->read('uid');
        
        $friends = $this->Friend->searchFriends( $uid, $this->request->query['q'] );
        
        return json_encode( $friends );
    }
}

