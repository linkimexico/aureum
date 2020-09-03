<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class EventsController extends AppController
{	
	public $paginate = array(        
        'order' => array(
            'Event.id' => 'desc'
        ) 
    );
	
	public function index($cat_id = null)
	{		
		$jsnsocial_setting = $this->_getSettings();
        $cat_id = intval($cat_id);     		
        
        $this->loadModel('Category');
        $categories = $this->Category->getCategories( APP_EVENT );
        
        if ( !empty( $cat_id ) )
            $events  = $this->Event->getEvents('category', $cat_id);
        else
            $events  = $this->Event->getEvents();

		$this->set('events', $events);
        $this->set('categories', $categories);
        $this->set('cat_id', $cat_id);
		$this->set('title_for_layout', __('Events'));
	}

	/*
	 * Browse events based on $type
	 * @param string $type - possible value: all (default), my, home, friends, past
	 */
	public function ajax_browse( $type = null, $param = null )
	{		
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $url = ( !empty( $param ) ) ? $type . '/' . $param : $type;
		
		switch ( $type )
		{
			case 'home': 
			case 'my':
			case 'friends':
				$this->_checkPermission();
				$uid = $this->Session->read('uid');	
				
				$this->loadModel( 'EventRsvp' );
				$events = $this->EventRsvp->getEvents( $type, $uid, $page );
									
				break;
				
			default: // all, past, category
				$events = $this->Event->getEvents( $type, $param, $page );
		}
		
		$this->set('events', $events);
		$this->set('more_url', '/events/ajax_browse/' . h($url) . '/page:' . ( $page + 1 ) ) ;
		
		if ( $page == 1 && $type == 'home' )
			$this->render('/Elements/ajax/home_event');
		else
			$this->render('/Elements/lists/events_list');		
	}
	
	/*
	 * Show add/edit event form
	 * @param int $id - event id to edit
	 */
	public function create($id = null)
	{
		$id = intval($id);	
		$this->_checkPermission( array( 'confirm' => true ) );
        $this->_checkPermission( array('aco' => 'event_create') );    
        
        $this->loadModel('Category');  
        $role_id = $this->_getUserRoleId();      
        $categories = $this->Category->getCategoriesList( APP_EVENT, $role_id );

		if (!empty($id)) // editing
		{
			$event = $this->Event->findById($id);
			$this->_checkExistence( $event );
			$this->_checkPermission( array( 'admins' => array( $event['User']['id'] ) ) );
		
			$this->set( 'title_for_layout', __('Edit Event') );
		}
		else // adding new event
		{
			$event = $this->Event->initFields();				
			$this->set( 'title_for_layout', __('Add New Event') );
		}
		
		$this->set('event', $event);
        $this->set('categories', $categories);  
	}
	
	/*
	 * Save add/edit form
	 */
	public function ajax_save()
	{
		$this->_checkPermission( array( 'confirm' => true ) );
			
		$this->autoRender = false;		
		$uid = $this->Session->read('uid');
		
		if ( !empty( $this->request->data['id'] ) )
		{
			// check edit permission
			$event = $this->Event->findById( $this->request->data['id'] );
			$this->_checkPermission( array( 'admins' => array( $event['User']['id'] ) ) );
			$this->Event->id = $this->request->data['id'];
		}
		else
			$this->request->data['user_id'] = $uid;
		
		$this->Event->set( $this->request->data );
		$this->_validateData( $this->Event );
			
		if ( $this->Event->save() ) // successfully saved	
		{				
			if ( empty( $this->request->data['id'] ) ) // add event
			{
				// rsvp the creator
				$this->loadModel( 'EventRsvp' );
				$this->EventRsvp->save( array( 'user_id' => $uid, 'event_id' => $this->Event->id, 'rsvp' => RSVP_ATTENDING ) );
				
				// insert into activity feed if it's a public event			
				if ( $this->request->data['type'] == PRIVACY_PUBLIC )
				{
					$this->loadModel( 'Activity' );
					$this->Activity->save( array( 'type' 	  => 'user',
												  'action'    => 'event_create',
												  'user_id'   => $uid,
												  'item_type' => 'event',
												  'item_id'   => $this->Event->id,
												  'query'	  => 1
										) );
				}
				
				if ( !empty($this->request->data['photo']) ) // move photo to uploads folder (new event)
				{
					$newpath = WWW_ROOT . 'uploads' . DS . 'events';
					
					if ( !file_exists( $newpath ) )
						mkdir( $newpath, 0777, true );
	
					copy(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $this->request->data['photo'], $newpath . DS . $this->request->data['photo']);
					copy(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . 't_' . $this->request->data['photo'], $newpath . DS . 't_' . $this->request->data['photo']);
	
					unlink(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $this->request->data['photo']);
					unlink(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . 't_' . $this->request->data['photo']);
				}
			}
            
            $response['result'] = 1;
            $response['id'] = $this->Event->id;
            
            echo json_encode($response);
		}
	}
	
	/*
	 * View Event
	 * @param int $id - event id to view
	 */
	public function view($id = null)
	{
		$id = intval($id);		
		$uid  = $this->Session->read('uid');
		$event = $this->Event->findById($id);
		$this->_checkExistence( $event );
        $this->_checkPermission( array('aco' => 'event_view') );    
        
		$this->loadModel('EventRsvp');	
		
		if ( $uid )
		{
			$my_rsvp = $this->EventRsvp->getMyRsvp( $uid, $id );
			$this->set('my_rsvp', $my_rsvp);			
		}
		
		// check if user can view this event
		if ( empty( $my_rsvp ) && $event['Event']['type'] == PRIVACY_PRIVATE )
			$this->redirect( '/pages/no-permission' );

		$attending 	   = array();
		$maybe 		   = array();
		$not_attending = array();
		$awaiting 	   = array();		
		
		// get rsvp
		$awaiting 		= $this->EventRsvp->getRsvp( $id, RSVP_AWAITING, null, 6 );
		$attending 		= $this->EventRsvp->getRsvp( $id, RSVP_ATTENDING, null, 5 );
		$not_attending  = $this->EventRsvp->getRsvp( $id, RSVP_NOT_ATTENDING, null, 6 );
		$maybe 			= $this->EventRsvp->getRsvp( $id, RSVP_MAYBE, null, 6 );
		
		$maybe_count 		 = $this->EventRsvp->getRsvpCount( $id, RSVP_MAYBE );
		$not_attending_count = $this->EventRsvp->getRsvpCount( $id, RSVP_NOT_ATTENDING );
		$awaiting_count		 = $this->EventRsvp->getRsvpCount( $id, RSVP_AWAITING );
		
		// get activities
		$this->loadModel('Activity');
		$activities = $this->Activity->getActivities( 'event', $id );
		
		// get activity likes
		if ( !empty( $uid ) )
		{					
			$this->loadModel('Like');				
			$activity_likes = $this->Like->getActivityLikes( $activities, $uid );
			$this->set('activity_likes', $activity_likes);
		}
		
		$this->set('attending', $attending);
		$this->set('maybe', $maybe);
		$this->set('not_attending', $not_attending);
		$this->set('awaiting', $awaiting);
		
		$this->set('maybe_count', $maybe_count);
		$this->set('not_attending_count', $not_attending_count);
		$this->set('awaiting_count', $awaiting_count);
		
		$this->set('event', $event);
		$this->set('activities', $activities);
		$this->set('title_for_layout', $event['Event']['title']);
		$this->set('desc_for_layout', $event['Event']['description']);
		if(!empty($event['Event']['photo'])) $this->set('og', $this->request->webroot.'uploads/events/'.$event['Event']['photo']);
		else $this->set('og', $this->request->webroot.'img/no-image-events.jpg');
	}

	/*
	 * RSVP event
	 */
	public function do_rsvp()
	{		
		$this->_checkPermission( array( 'confirm' => true ) );		
					
		$uid = $this->Session->read('uid');
		$this->request->data['user_id'] = $uid;		
		$event = $this->Event->findById( $this->request->data['event_id'] );

		// find existing and update if necessary
		$this->loadModel( 'EventRsvp' );
		$my_rsvp = $this->EventRsvp->getMyRsvp( $uid, $this->request->data['event_id'] );
		
		// check if user was invited
		if ( empty( $my_rsvp ) && $event['Event']['type'] == PRIVACY_PRIVATE )
			$this->redirect( '/pages/no-permission' );

		if ( !empty($my_rsvp) )
		{
			$this->EventRsvp->id = $my_rsvp['EventRsvp']['id'];
			
			// user changed rsvp from attending to something else
			if ( $my_rsvp['Event']['type'] != PRIVACY_PRIVATE && $my_rsvp['EventRsvp']['rsvp'] == RSVP_ATTENDING && $this->request->data['rsvp'] != RSVP_ATTENDING )
			{
				// remove associated activity
				$this->loadModel('Activity');
				$this->Activity->deleteAll( array( 'user_id'   => $uid,
												   'action'    => 'event_attend',
												   'item_type' => 'event',
												   'item_id'   => $this->request->data['event_id']
				 ), true, true );
			}
		}
		else
		{
			// first time rsvp			
			if ( $event['Event']['type'] == PRIVACY_PUBLIC && $this->request->data['rsvp'] == RSVP_ATTENDING ) // attending
			{
				$this->loadModel( 'Activity' );		
				$activity = $this->Activity->getRecentActivity( 'event_attend', $uid );
				
				// insert into activity feed if it's a public event
				if ( !empty( $activity ) )
				{
					// aggregate activities
					$event_ids = explode( ',', $activity['Activity']['items'] );
                    
                    if ( !in_array($event['Event']['id'], $event_ids) )
					   $event_ids[] = $event['Event']['id'];
					
					$this->Activity->id = $activity['Activity']['id'];
					$this->Activity->save( array( 'items'   => implode( ',', $event_ids ),	
												  'item_id' => 0,			
												  'params'	=> '',								  
												  'query'	=> 1
										) );
				}
				else
				{
					$this->Activity->save( array( 'type'      => 'user',
												  'action'    => 'event_attend',
												  'user_id'   => $uid,
												  'item_type' => APP_EVENT,
												  'item_id'   => $event['Event']['id'],													  
												  'params' 	  => '<a href="' . $this->request->base . '/events/view/' . $event['Event']['id'] . '">' . h($event['Event']['title']) . '</a>',
												  'items'	  => $event['Event']['id']										  
										) );
				}
			}
		}

		$this->EventRsvp->save( $this->request->data );

		$this->redirect( '/events/view/'.$this->request->data['event_id'] );
	}
	
	/*
	 * Show invite form
	 * @param int $event_id
	 */
	public function ajax_invite( $event_id = null )
	{
		$event_id = intval($event_id);
		$this->_checkPermission( array( 'confirm' => true ) );	

		$this->set('event_id', $event_id);
	}
	
	/*
	 * Handle invite submission
	 */
	public function ajax_sendInvite()
	{
		$this->autoRender = false;
		$this->_checkPermission( array( 'confirm' => true ) );
		$cuser = $this->_getUser();
		
		if ( !empty( $this->request->data['friends'] ) || !empty( $this->request->data['emails'] ) )
		{		
			$event = $this->Event->findById( $this->request->data['event_id'] );
			
			// check if user can invite
			if ( $event['Event']['type'] == PRIVACY_PRIVATE && ( $cuser['id'] != $event['User']['id'] ) )
				return;
			
			if ( !empty( $this->request->data['friends'] ) )
			{
				$this->loadModel( 'EventRsvp' );				
				$data = array();	
                $friends = explode(',', $this->request->data['friends']);
                $rsvp_list = $this->EventRsvp->getRsvpList($this->request->data['event_id']);
				
				foreach ($friends as $friend_id)		
                    if ( !in_array($friend_id, $rsvp_list) )	
					   $data[] = array('event_id' => $this->request->data['event_id'], 'user_id' => $friend_id);
	           
                if ( !empty($data) )
                {
    				$this->EventRsvp->saveAll($data);
    				
    				$this->loadModel( 'Notification' );
    				$this->Notification->record( array( 'recipients' => $friends,
    													'sender_id' => $cuser['id'],
    													'action' => 'event_invite',
    													'url' => '/events/view/'.$this->request->data['event_id'],
    													'params' => h($event['Event']['title'])
    											) );
                }
			}
			
			if ( !empty( $this->request->data['emails'] ) )
			{
				$emails = explode( ',', $this->request->data['emails'] );
				
				$i = 1;
				foreach ( $emails as $email )
				{
					if ( $i <= 10 )
					{
						if ( Validation::email( trim($email) ) )
						{
							$text = h($cuser['name']) . ' ' . __('invited you to "%s"', h($event['Event']['title']) );
							$this->_sendEmail( trim($email),
											   $text,
											   'general',
											   array( 
											   		'text' => $text, 
											   		'url' => $this->request->base . '/events/view/'.$this->request->data['event_id'] )
											 );
						}
					}
					$i++;
				}
			}
			
			echo __('Your invitations have been sent.') . ' <a href="javascript:void(0)" onclick="inviteMore()">' . __('Invite more friends') . '</a>';
		} 
	}
	
	/*
	 * Show RSVP list
	 * @param int $event_id
	 */
	public function ajax_showRsvp( $event_id = null, $type = RSVP_ATTENDING )
	{
		$event_id = intval($event_id);
		$this->loadModel('EventRsvp');
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
		
		$users = $this->EventRsvp->getRsvp( $event_id, $type, $page );
		
		$this->set('users', $users);
		$this->set('page', $page);
		$this->set('more_url', '/events/ajax_showRsvp/' . $event_id . '/' . $type . '/page:' . ( $page + 1 ) );		
		
		$this->render('/Elements/ajax/user_overlay');	
	}
	
	/*
	 * Delete event
	 * @param int $id - event id to delete
	 */
	public function do_delete($id = null)
	{
		$id = intval($id);
		$event = $this->Event->findById($id);
		$this->_checkExistence( $event );
		$this->_checkPermission( array( 'admins' => array( $event['User']['id'] ) ) );
		
		$this->Event->deleteEvent( $event );
		
		$this->Session->setFlash( __('Event has been deleted') );
		$this->redirect( '/events' );
	} 
	
	public function admin_index()
	{
		if ( !empty( $this->request->data['keyword'] ) )
			$this->redirect( '/admin/events/index/keyword:' . $this->request->data['keyword'] );
			
		$cond = array();
		if ( !empty( $this->request->named['keyword'] ) )
			$cond['MATCH(Event.title) AGAINST(? IN BOOLEAN MODE)'] = $this->request->named['keyword'];
			
		$events = $this->paginate( 'Event', $cond );	
        
        $this->loadModel('Category');
        $categories = $this->Category->getCategoriesList( APP_EVENT );
		
		$this->set('events', $events);
        $this->set('categories', $categories);
		$this->set('title_for_layout', 'Events Manager');
	}
	
	public function admin_delete()
	{
		$this->_checkPermission(array('super_admin' => 1));
		
		if ( !empty( $_POST['events'] ) )
		{					
			$events = $this->Event->findAllById( $_POST['events'] );
			
			foreach ( $events as $event )
				$this->Event->deleteEvent( $event );	

			$this->Session->setFlash( 'Events deleted' );				
		}
		
		$this->redirect( $this->referer() );
	}
    
    public function admin_move()
    {
        if ( !empty( $_POST['events'] ) && !empty( $this->request->data['category_id'] ) )
        {                   
            foreach ( $_POST['events'] as $event_id )
            {
                $this->Event->id = $event_id;
                $this->Event->save( array( 'category_id' => $this->request->data['category_id'] ) );
            }

            $this->Session->setFlash( 'Events moved' );               
        }
        
        $this->redirect( $this->referer() );
    }
}

?>
