<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class ToolsController extends AppController {	

	public function beforeFilter()
	{
		parent::beforeFilter();
        $this->_checkPermission(array('super_admin' => 1));
	}

	public function admin_bulkmail() 
	{
		$this->set('title_for_layout', 'Bulk Mail');	
	}
	
	public function admin_ajax_bulkmail_start() 
	{
		$this->autoRender = false;
		
		if ( !empty( $this->request->data['subject'] ) && !empty( $this->request->data['body'] ) && !empty( $this->request->data['cycle'] ) )
		{
			$this->Session->write('bulkmail_subject', $this->request->data['subject']);
			$this->Session->write('bulkmail_body', $this->request->data['body']);
			$this->Session->write('bulkmail_cycle', $this->request->data['cycle']);
		}
		else
			echo 'All fields are required';
	}
	
	public function admin_ajax_bulkmail_test()
	{
		$this->autoRender = false;
		
		if ( !empty( $this->request->data['subject'] ) && !empty( $this->request->data['body'] ) )
		{
			$setting = $this->_getSettings();
			
			$this->_sendEmail( $setting['site_email'], $this->request->data['subject'], null, null, null, null, $this->request->data['body'] );
		}
		else
			echo 'All fields are required';
	}
	
	public function admin_ajax_bulkmail_send( $page = 1 )
	{
		$subject = $this->Session->read('bulkmail_subject');
		$body	 = $this->Session->read('bulkmail_body');
		$cycle	 = $this->Session->read('bulkmail_cycle');
				
		if ( !empty( $subject ) && !empty( $body ) && !empty( $cycle ) )
		{
			$this->layout = '';
			$this->loadModel('User');
			
			$users = $this->User->find('all', array( 'conditions' => array( 'User.active' => 1, 
																			'User.notification_email' => 1 
																		  ), 
											   		 'limit' 	  => $cycle,
											   		 'page'  	  => $page										   
										)	);
										
			foreach ( $users as $user )
				$this->_sendEmail( $user['User']['email'], $subject, null, null, null, null, $body );
			
			$this->set('users', $users);
			$this->set('page', $page + 1);
		}
	}

	public function admin_clean_tmp()
	{
		$this->autoRender = false;
		$path = JPATH_SITE . '' . DS . 'media' . DS . 'socialnetwork' . DS . 'uploads' . DS . 'tmp';
		
		$files  = scandir( $path );
		$oneday = time() - 60 * 60 * 24; 

		foreach ( $files as $file )
		{
			if ( !is_dir( $file ) && $file != 'index.html' )
			{
				$created = filemtime( $path . DS . $file );
				if ( $oneday > $created )
				{
					echo 'Removing ' . $file . '...<br />';
					unlink( $path . DS . $file );					
				}
			}
		}

		echo 'Done!';
	}
    
    public function admin_clear_cache()
    {
		$path = JPATH_SITE . DS . 'plugins' . DS . 'jsn' . DS . 'socialnetwork' . DS . 'app' . DS . 'tmp' . DS . 'cache' . DS . 'persistent';
		
		$files  = scandir( $path );

		foreach ( $files as $file )
		{
			if ( !is_dir( $file ) && $file != 'index.html' )
			{
				unlink( $path . DS . $file );
			}
		}
        Cache::clear();
        
        $this->Session->setFlash( 'All caches have been cleared' );
        $this->redirect( '/admin' );
    }
    
    public function admin_remove_notifications()
    {
        $this->loadModel('Notification');
        
        $this->Notification->deleteAll( array( 'Notification.read' => 1, 'DATE_SUB(CURDATE(),INTERVAL 30 DAY) >= Notification.created' ) );
        
        $this->Session->setFlash( 'Read notifications older than 30 days have been deleted' );
        $this->redirect( '/admin' );
    }
}