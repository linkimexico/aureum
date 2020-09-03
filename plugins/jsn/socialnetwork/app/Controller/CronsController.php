<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class CronsController extends AppController
{
	public function beforeFilter() {}	
	
	public function run()
	{
		$this->autoRender = false;
		$this->response->type('image/jpg');	
			
		$this->loadModel('MailQueue');
	
		$mails = $this->MailQueue->findAllByStatus(0);
		
		foreach ( $mails as $mail)
		{
			$this->_sendEmail( $mail['MailQueue']['email'], 
							   $mail['MailQueue']['subject'], 
							   'notification', 
							   array( 'text' 	  => $mail['MailQueue']['subject'],
							   		  'comment'   => $mail['MailQueue']['comment'],
									  'url' 	  => $mail['MailQueue']['url'],
							   		  'view_text' => $mail['MailQueue']['view_text']
							)	);
							
			$this->MailQueue->id = $mail['MailQueue']['id'];
			$this->MailQueue->saveField( 'status', 1 );
		}
	}

}

