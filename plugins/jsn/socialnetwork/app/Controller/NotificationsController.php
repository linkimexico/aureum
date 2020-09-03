<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class NotificationsController extends AppController
{

	public function ajax_show( $type = null )
	{
		$this->_checkPermission();		
		$uid = $this->Session->read('uid');		

		$this->Notification->bindModel(
			array('belongsTo' => array(
					'Sender' => array(
						'className' => 'User',
						'foreignKey' => 'sender_id'
					)
				)
			)
		);
		$notifications = $this->Notification->findAllByUserId($uid);
		
		$this->set('notifications', $notifications);
		$this->set('type', $type);
	}
	
	public function ajax_count( $type = null )
	{
		$this->_checkPermission();		
		$uid = $this->Session->read('uid');		

		$notifications = $this->Notification->find( 'count', array( 'conditions' => array( 'Notification.user_id' => $uid, 
                                                                    'Notification.read' => 0
                            ) ) );
		
		echo $notifications;
		exit;
	}

	public function ajax_view($id = null)
	{
		$id = intval($id);	
		$this->_checkPermission();		

		$this->Notification->id = $id;
		$notification = $this->Notification->read();
		$this->_checkPermission( array( 'admins' => array( $notification['Notification']['user_id'] ) ) );

		$this->Notification->save( array( 'read' => 1 ) );
		$this->redirect( $notification['Notification']['url'] );
	}

	public function ajax_remove($id = null)
	{
		$id = intval($id);
		$this->autoRender = false;
		$this->_checkPermission();	

		$notification = $this->Notification->findById($id);
		$this->_checkPermission( array( 'admins' => array( $notification['Notification']['user_id'] ) ) );
		
		$this->Notification->delete($id);
	}
	
	public function ajax_clear()
	{
		$this->autoRender = false;	
		$this->_checkPermission();
		$uid = $this->Session->read('uid');		
		
		$this->Notification->deleteAll( array( 'user_id' => $uid ), true, true );
	}
}

