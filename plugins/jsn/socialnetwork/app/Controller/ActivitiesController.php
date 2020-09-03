<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class ActivitiesController extends AppController {
		
	/*
	 * Show activities based on $type
	 * @param string $type - possible value: friends, everyone, profile, event, group
	 */
	public function ajax_browse( $type = null, $param = null )
	{
		$uid = $this->Session->read('uid');
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
		$admins = array();
		
		if ( in_array( $type, array('home', 'everyone', 'friends') ) )
		{
			$param = $this->Session->read('uid');
			if ( $type == 'friends' && !$param ) 
				$this->_checkPermission();
            
            // save to cookie
            if ( in_array( $type, array('everyone', 'friends') ) )
                $this->Cookie->write('activity_feed', $type);
		}
		
		switch ( $type )
		{
			case 'group':		
				$uid = $this->Session->read('uid');
				
				$this->loadModel('Group');
				$this->loadModel('GroupUser');
				
				$group = $this->Group->findById( $param );
				$is_member = $this->GroupUser->isMember( $uid, $param );
				
				if ( $group['Group']['type'] == PRIVACY_PRIVATE )
				{
					$cuser = $this->_getUser();				
					
					if ( !$cuser['Role']['is_admin'] && !$is_member )				
						return;				
				}
				
				$admins = $this->GroupUser->getUsersList( $param, GROUP_USER_ADMIN );
	
				$this->set('is_member', $is_member);
				
				break;
				
			case 'event':
				$this->loadModel('Event');
				
				// add event creator to the admins array
				$event = $this->Event->findById( $param );
				$admins = array( $event['Event']['user_id'] );
				
				break;
				
			case 'profile':
				$admins = array( $param );
				break;
		}
		
		$this->set('admins', $admins);
			
		$activity_feed = $type;
        if ( $type == 'home' )
        {
            $setting = $this->_getSettings();
            $activity_feed = $setting['default_feed'];
                
            // save activity feed that you selected
            if ( !empty( $uid ) && $setting['feed_selection'] && $this->Cookie->read('activity_feed') )
                $activity_feed = $this->Cookie->read('activity_feed');
            
            $this->set('activity_feed', $activity_feed);
        }
		
		$activities = $this->Activity->getActivities( $activity_feed, $param, $uid, $page );
		$this->set( 'activities', $activities );		
		
		// get activity likes
		if ( !empty( $uid ) )
		{
			$this->loadModel( 'Like' );			
										
			$activity_likes = $this->Like->getActivityLikes( $activities, $uid );
			$this->set('activity_likes', $activity_likes);
		}
		
		$url = ( !empty( $param ) )	? $type . '/' . $param : $type;
		$this->set('more_url', '/activities/ajax_browse/' . h($url) . '/page:' . ( $page + 1 ) );
		
		if ( $page == 1 && $type == 'home' )
			$this->render('/Elements/ajax/home_activity');
		else
			$this->render('/Elements/activities');
	}
		
	public function ajax_share()
	{
		$this->_checkPermission( array( 'confirm' => true ) );			
		$uid = $this->Session->read('uid');

		$this->request->data['user_id'] = $uid;
		$this->request->data['content'] = $this->request->data['message'];
		$this->request->data['privacy'] = ( !empty( $this->request->data['privacy'] ) ) ? $this->request->data['privacy'] : PRIVACY_ME;
        
        if ( $this->request->is('mobile') )
            $this->request->data['params'] = 'mobile';
        
        $this->Activity->parseLink( $this->request->data );
        
        if ( $this->request->data['wall_photo_id'] )
		{
			$this->request->data['item_id'] = $this->request->data['wall_photo_id'];
			$this->request->data['item_type'] = APP_PHOTO;
			$this->request->data['query'] = 1;
		}
            
		if ( $this->Activity->save( $this->request->data ) )
		{
			$activity = $this->Activity->read();
			
			if ( !empty($this->request->data['wall_photo_id']) )
			{
				$this->loadModel('Photo');
				$activity['Content'] = $this->Photo->findById($this->request->data['wall_photo_id']);
			}
			else 
			{
				$this->loadModel('Video');
				$this->Video->parseStatus( $activity );
			}
			
			
			$this->set('activity', $activity);
			
			switch ( $this->request->data['type'] )
			{
				case APP_USER:					
					if ( !empty( $this->request->data['target_id'] ) && $this->request->data['target_id'] != $uid ) // post on other user's profile
					{
						$this->loadModel('Notification');						
						$this->Notification->record( array( 'recipients' => $this->request->data['target_id'],
															'sender_id' => $uid,
															'action' => 'profile_comment',
															'url' => '/users/view/'.$this->request->data['target_id'].'/activity_id:'.$activity['Activity']['id']													
													) );
					}
				break;
			}
		}
	}

	public function ajax_comment()
	{
		$this->_checkPermission( array( 'confirm' => true ) );			
		$this->loadModel('ActivityComment');
		$this->loadModel('Notification');
		
		$uid = $this->Session->read('uid');
		$cuser = $this->_getUser();
		$commentdata = $this->request->data;

		$commentdata['user_id'] = $uid;

		if ( $this->ActivityComment->save( $commentdata ) )
		{
			$comment = $this->ActivityComment->read();
			$this->set('comment', $comment);
			
			// send notifications to commenters
			$activity = $this->Activity->findById( $commentdata['activity_id'] );
			
			$this->Activity->id = $commentdata['activity_id'];
			$this->Activity->save( array( 'modified' => date('Y-m-d H:i:s') ) );

			$params = array( 'actor' => $cuser, 'owner' => $activity['User'] );
			//$profile_id = ( !empty( $activity['Activity']['target_id'] ) ) ? $activity['Activity']['target_id'] : $activity['Activity']['user_id'];
			$profile_id = $activity['Activity']['user_id']; 
			$url = '/users/view/' . $profile_id . '/activity_id:' . $activity['Activity']['id'];

			$users = $this->ActivityComment->find('list', array( 'conditions' => array( 'activity_id' => $commentdata['activity_id'], 
																						'ActivityComment.user_id <> ' . $uid . ' AND ActivityComment.user_id <> ' . $activity['User']['id']), 
																 'fields' => array('ActivityComment.user_id'), 
																 'group' => 'ActivityComment.user_id'
													)	);
			
			if ( !empty( $users ) )
			{
				$this->Notification->record( array( 'recipients'  => $users,
													'sender_id'   => $uid,
													'action'	  => 'status_comment',
													'url' 		  => $url,
													'params'	  => serialize($params)
										) );
			}

			// send notification and email to wall author
			if ( $uid != $activity['User']['id'] )
			{				
				$this->Notification->record( array( 'recipients'  => $activity['User']['id'],
													'sender_id'   => $uid,
													'action'	  => 'own_status_comment',
													'url' 		  => $url
											) );
			}
			
			// insert this comment to the item page
			if ( !empty( $activity['Activity']['item_type'] ) && !empty( $activity['Activity']['item_id'] ) )
			{
				$item_type = ( $activity['Activity']['item_type'] == APP_PHOTO ) ? APP_ALBUM : $activity['Activity']['item_type'];					
					
				$this->loadModel('Comment');
				$this->Comment->save( array( 'user_id' => $uid, 
											 'type' => $item_type, 
											 'target_id' => $activity['Activity']['item_id'],
											 'message' => $commentdata['comment']
									) );
			}
		}
	}

	public function ajax_remove()
	{
		$this->autoRender = false;		
		$this->_checkPermission( array( 'confirm' => true ) );
		
		$activity = $this->Activity->findById( $this->request->data['id'] );
		$this->_checkExistence( $activity );
		
		$admins = array( $activity['Activity']['user_id'] ); // activity poster
		
		switch ( $activity['Activity']['type'] )
		{
			case APP_USER:
				$admins[] = $activity['Activity']['target_id']; // user can delete status posted by other users on their profile
				break;
				
			case APP_GROUP:
				$this->loadModel('GroupUser');
				
				// add group admins to the admins array
				$group_admins = $this->GroupUser->getUsersList( $activity['Activity']['target_id'], GROUP_USER_ADMIN );
				$admins = array_merge( $admins, $group_admins );
				
				break;
				
			case APP_EVENT:
				$this->loadModel('Event');
				
				// add event creator to the admins array
				$event = $this->Event->findById( $activity['Activity']['target_id'] );
				$admins[] = $event['Event']['user_id']; 
				
				break;
		}
		
		$this->_checkPermission( array( 'admins' => $admins ) );
		$this->Activity->delete( $this->request->data['id'] );
	}
	
	public function ajax_removeComment()
	{
		$this->autoRender = false;		
		$this->_checkPermission( array( 'confirm' => true ) );
		
		$this->loadModel('ActivityComment');
		$comment = $this->ActivityComment->findById( $this->request->data['id'] );
		$this->_checkExistence( $comment );
		
		$admins = array( $comment['ActivityComment']['user_id'] ); // comment poster
		
		switch ( $comment['Activity']['type'] )
		{
			case APP_USER:
				$admins[] = $comment['Activity']['target_id']; // user can delete comment posted by other users on their profile
				break;
				
			case APP_GROUP:
				$this->loadModel('GroupUser');
				
				// add group admins to the admins array
				$group_admins = $this->GroupUser->getUsersList( $comment['Activity']['target_id'], GROUP_USER_ADMIN );
				$admins = array_merge( $admins, $group_admins );
				
				break;
				
			case APP_EVENT:
				$this->loadModel('Event');
				
				// add event creator to the admins array
				$event = $this->Event->findById( $comment['Activity']['target_id'] );
				$admins[] = $event['Event']['user_id']; 
				
				break;
		}
		
		$this->_checkPermission( array( 'admins' => $admins ) );
		$this->ActivityComment->delete( $this->request->data['id'] );
	}
	
}
