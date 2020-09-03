<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class ReportsController extends AppController 
{
	public function ajax_create( $type = null, $target_id = null )
	{
		$target_id = intval($target_id);
		$this->_checkPermission();
		$this->set( 'type', $type );
		$this->set( 'target_id', $target_id );
	}
		
	public function ajax_save()
	{
		$this->_checkPermission();
		$uid = $this->Session->read('uid');
		
		if ( !empty( $this->request->data ) )
		{
			$this->autoRender = false;
			$uid = $this->Session->read('uid');
			
			$this->request->data['user_id'] = $uid;
			$this->Report->set( $this->request->data );
			$this->_validateData( $this->Report );
			
			$count = $this->Report->find( 'count', array( 'conditions' => array( 'type' => $this->request->data['type'],
																				 'target_id' => $this->request->data['target_id'],
																				 'user_id' => $uid )
										 ) 	);
			if ( $count > 0 )
			{
				$response['result'] = 0;
                $response['message'] = __('Duplicated report');
                echo json_encode($response);
				return;
			}
				
			if ( $this->Report->save() ) // successfully saved	
			{
				$this->loadModel('AdminNotification');	
				
				$this->AdminNotification->save( array( 'user_id' => $uid,
													   'message' => $this->request->data['reason'],
													   'text' => __('reported a %s', $this->request->data['type']),
													   'url' => $this->request->base . '/' . $this->request->data['type'] . 's/view/' . $this->request->data['target_id']
											) );
											
                $response['result'] = 1;
                $response['message'] = __('Thank you! Your report has been submitted');
                echo json_encode($response);
			}
		}
	}
}

