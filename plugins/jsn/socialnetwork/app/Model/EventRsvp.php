<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class EventRsvp extends AppModel {
	
	public $belongsTo = array( 'User', 
							   'Event'  => array( 'counterCache' => true,
											 	  'counterScope' => array('rsvp' => RSVP_ATTENDING)
							)	);
							
	public $order = 'EventRsvp.id desc';
	
	public $validate = array( 'event_id' => array( 'rule' => 'notEmpty'),
							  'user_id' => array( 'rule' => 'notEmpty')
	);
	
							
	/*
	 * Get events based on type
	 * @param string $type - possible value: home, my, friends
	 * @param int $uid - user id
	 * @param int $page - page number
	 * @return array $events
	 */
	public function getEvents( $type = null, $uid = null, $page = 1 )
	{
		$cond = array();
		
		switch ( $type )
		{			
			// Get my future events (attending and waiting response)	
			case 'home':
			case 'my':				
				if ( $uid )
					$cond = array( 'EventRsvp.user_id' => $uid, 
								   '(EventRsvp.rsvp = ' . RSVP_ATTENDING . ' OR EventRsvp.rsvp = ' . RSVP_AWAITING . ')', 
								   'Event.to >= CURDATE()' 
								);					
				break;
				
			// Get my events that friends are attending	excluding private events (type < 3)
			case 'friends':				
				if ( $uid )
				{
					JsnApp::import('Model', 'Friend');	
					$friend = new Friend();
					$friends = $friend->getFriends( $uid );	
														
					$cond = array( 'EventRsvp.user_id' => $friends, 
								   'EventRsvp.rsvp' => RSVP_ATTENDING,
								   'Event.type' => PRIVACY_PUBLIC,  
								   'Event.to >= CURDATE()'
								);
				}					
				break;
		}
		
		$events = $this->find( 'all', array( 'conditions' => $cond, 
											 'fields' => array( 'DISTINCT Event.id', 'Event.title', 'Event.location', 'Event.event_rsvp_count', 'Event.photo', 'Event.from' ),
											 'limit' => RESULTS_LIMIT, 
											 'page' => $page 
							) );
		
		return $events;
	}
	
	/*
	 * Get rsvps of an event based on $type	 
	 * @param int $event_id
	 * @param mixed $type from 0 to 3
	 * @param int $page - page number
	 * @return array $rsvps
	 */
	public function getRsvp( $event_id, $type = null, $page = null, $limit = RESULTS_LIMIT )
	{
		$cond = array( 'event_id' => $event_id );
		
		if ( $type !== null )
			$cond['rsvp'] = $type;
		
		$rsvps = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page ) );
							
		return $rsvps;
	}
	
	/*
	 * Get rsvps count of an event based on $type	 
	 * @param int $event_id
	 * @param mixed $type from 0 to 3
	 * @return int $count
	 */
	public function getRsvpCount( $event_id, $type )
	{
		$count = $this->find( 'count', array( 'conditions' => array( 'EventRsvp.event_id' => $event_id, 
																	 'EventRsvp.rsvp' => $type
							 ) ) );
							
		return $count;
	}
	
	/*
	 * Get a list of user id that rsvp	 
	 * @param int $event_id
	 * @return array $users
	 */
	public function getRsvpList( $event_id )
	{
		$users = $this->find( 'list', array( 'conditions' => array( 'EventRsvp.event_id' => $event_id ),
											 'fields' => array( 'EventRsvp.user_id' )
							) );
							
		return $users;
	}
	
	/*
	 * Get user's rsvp of an event
	 * @param int $uid
	 * @param int $event_id
	 * @return array $rsvp
	 */
	public function getMyRsvp( $uid, $event_id )
	{
		$rsvp = $this->find( 'first', array( 'conditions' => array( 'EventRsvp.event_id' => $event_id,
																  	'EventRsvp.user_id' => $uid
							) ) );
							
		return $rsvp;
	}
	
	public function getMyEventsList( $uid )
	{
		$events = $this->find('list', array( 'conditions' => array( 'EventRsvp.user_id' => $uid, 
																    'EventRsvp.rsvp' => RSVP_ATTENDING ),
										     'fields' => array('EventRsvp.event_id')  
		)	);	
		
		return $events;
	}
	
	public function getMyEventsCount( $uid )
	{
		$events = $this->find( 'count', array( 'conditions' => array( 'EventRsvp.user_id' => $uid, 
																 	   '(rsvp = ' . RSVP_ATTENDING . ' OR rsvp = ' . RSVP_AWAITING . ')', 
																 	   'Event.to >= CURDATE()' 
						) )	);
										
		return $events;
	}
}
