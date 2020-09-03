<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Event extends AppModel {	

	public $belongsTo = array( 'User',
                               'Category' => array( 'counterCache' => 'item_count', 
                                                    'counterScope' => array( 'Event.type' => PRIVACY_PUBLIC,
                                                                             'Category.type' => APP_EVENT,
                                                                             'Event.to >= CURDATE()' ) ) 
                    );
	
	public $hasMany = array( 'Activity' => array( 
											'className' => 'Activity',	
											'foreignKey' => 'target_id',
											'conditions' => array('Activity.type' => APP_EVENT),						
											'dependent'=> true
										),
						  	 'EventRsvp' => array( 
						  					'className' => 'EventRsvp',												  			
						  					'dependent'=> true
										),
						); 
	
	public $order = 'Event.from asc';
	
	public $validate = array(	
							'title' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Title is required'
							),
							'category_id' =>     array(   
                                'rule' => 'notEmpty',
                                'message' => 'Category is required'
                            ),
							'location' => array( 	 
								'rule' => 'notEmpty',
								'message' => 'Location is required'
							),		
							'from' => 	array( 	 
								'rule' => array('date','ymd'),
								'message' => 'From is not a valid date format (yyyy-mm-dd)',
								'allowEmpty' => false
							),	
							'to' => 	array( 	 
								'rule' => array('date','ymd'),
								'message' => 'To is not a valid date format (yyyy-mm-dd)',
								'allowEmpty' => false
							),
							'description' => array( 	 
								'rule' => 'notEmpty',
								'message' => 'Description is required'
							),		
							'user_id' => array( 'rule' => 'notEmpty')
	);
	
	/*
	 * Get events based on type
	 * @param string $type - possible value: index (default), past
	 * @param int $page - page number
	 * @return array $events
	 */
	public function getEvents( $type = null, $param = null, $page = 1 )
	{
		$cond = array();
		
		switch ( $type )
		{
			// Get all past events that have public view access			
			case 'category':                     
                $cond = array( 'Event.category_id' => $param,
                               'Event.to >= CURDATE()', 
                               'Event.type' => PRIVACY_PUBLIC
                             );        
                break;
			
			// Get all past events that have public view access
			case 'past':						
				$cond = array( 'Event.to < CURDATE()', 
							   'Event.type' => PRIVACY_PUBLIC
							 ); 			
				break;
				
			// Get all future events that have public view access
			default:
				$cond = array( 'Event.to >= CURDATE()', 
							   'Event.type' => PRIVACY_PUBLIC
							 ); 
		}
		
		$events = $this->find( 'all', array( 'conditions' => $cond, 'limit' => RESULTS_LIMIT, 'page' => $page ) );
		
		return $events;
	}
	
	/*
	 * Get popular public events
	 * @return array $events
	 */
	public function getPopularEvents( $limit = 5, $days = null )
	{
		$cond = array( 'Event.to >= CURDATE()', 'Event.type' => PRIVACY_PUBLIC );
		
		if ( !empty( $days ) )
			$cond['DATE_ADD(CURDATE(),INTERVAL ? DAY) >= Event.to'] = intval($days);
		
		$events = $this->find( 'all', array( 'conditions' => $cond, 
											 'order' => 'Event.event_rsvp_count desc', 
											 'limit' => intval($limit)
							 ) 	);
		return $events;
	}
	
	public function deleteEvent( $event )
	{
		// delete photo
		if ( !empty( $event['Event']['photo'] ) )
			unlink( WWW_ROOT . 'uploads/events/' . $event['Event']['photo'] );
			
		$this->delete( $event['Event']['id'] );
		
		// delete activity
		JsnApp::import('Model', 'Activity');	
		$activity = new Activity();
		$activity->deleteAll( array( 'Activity.item_type' => APP_EVENT, 'item_id' => $event['Event']['id'] ), true, true );
        $activity->deleteAll( array( 'Activity.target_id' => $event['Event']['id'], 'Activity.type' => APP_EVENT ), true, true );
	}
}
