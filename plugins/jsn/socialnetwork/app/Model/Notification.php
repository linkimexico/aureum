<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Notification extends AppModel {	

	public $belongsTo = array( 'User'  => array( 'counterCache' => true,
												 'counterScope' => array( 'Notification.read' => 0 )		
							) );
	
	public $validate = array( 'user_id' => array( 'rule' => 'notEmpty'),
							  'sender_id' => array( 'rule' => 'notEmpty'),
							  'action' => array( 'rule' => 'notEmpty' ),
							  'url' => array( 'rule' => 'notEmpty' )
						 );
							  
	public $order = 'Notification.id desc';
	
	public $limit = RESULTS_LIMIT;
	
	/*
	 * Record a notification
	 * @params Array $params
	 */
	public function record( $params = array() )
	{
		if ( empty( $params['recipients'] ) )
			return;
		
		if ( empty( $params['params'] ) )
			$params['params'] = '';
		
		$data = array();
		if ( !is_array( $params['recipients'] ) )
			$params['recipients'] = array( $params['recipients'] );
		
		foreach ( $params['recipients'] as $recipient_id ) // save notification
		{
			$unread = $this->getUnreadNotification( $recipient_id, $params['url'] ); 
                
            if ( !$unread )
    			$data[] = array( 'user_id' 	 => $recipient_id,
    							 'sender_id' => $params['sender_id'],
    							 'action' 	 => $params['action'],
    							 'url' 		 => $params['url'],
    							 'params'	 => $params['params']
    					);
		}
	    
        if ( !empty( $data ) )
		    $this->saveAll($data);
	}
	
	public function getRecentNotifications()
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
		
		$notifications = $this->find( 'all', array( 'conditions' => array( 'User.notification_email' => 1,
																		   'Notification.read' => 0,
																  		   'DATE_SUB(CURDATE(),INTERVAL 1 DAY) <= Notification.created'
									) ) );
		
		return $notifications;
	}

    public function getUnreadNotification( $uid, $url )
    {
        $noti = $this->find( 'count', array( 'conditions' => array( 'Notification.user_id' => $uid, 
                                                                    'Notification.url' => $url,
                                                                    'Notification.read' => 0
                            ) ) );
        return $noti;
    }
	
}
