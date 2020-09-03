<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class AdminNotificationsController extends AppController
{
	public function admin_ajax_view($id = null)
	{	
		$this->_checkPermission();		

		$this->AdminNotification->id = $id;
		$notification = $this->AdminNotification->read();
		$this->AdminNotification->save( array( 'read' => 1 ) );
		
		if ( !empty( $notification['AdminNotification']['message'] ) )
			$this->set('notification', $notification);
		else
			$this->redirect( $notification['AdminNotification']['url'] );
	}

	public function admin_ajax_clear()
	{
		$this->autoRender = false;
		$this->_checkPermission(array('super_admin' => 1));
		
		$this->AdminNotification->deleteAll( array('AdminNotification.id > 0') );
	}
}

