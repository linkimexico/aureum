<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class SpamChallengesController extends AppController {

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->_checkPermission( array('super_admin' => true) );
	}

	public function admin_index( $type = null )
	{
		$challenges = $this->SpamChallenge->find('all');
		
		$this->set('challenges', $challenges );
		$this->set('title_for_layout', 'Spam Challenges');
	}
	
	 /*
	 * Render add/edit category
	 * @param mixed $id Id of category to edit
	 */
	public function admin_ajax_create( $id = null )
	{
		if (!empty($id))
			$challenge = $this->SpamChallenge->findById($id);
		else
        {
			$challenge = $this->SpamChallenge->initFields();
            $challenge['SpamChallenge']['active'] = 1;
        }
		
		$this->set('challenge', $challenge);	
	}
	
	/*
	 * Handle add/edit category submission
	 */
	public function admin_ajax_save( )
	{
		$this->autoRender = false;

		if ( !empty( $this->data['id'] ) )
			$this->SpamChallenge->id = $this->request->data['id'];

		$this->SpamChallenge->set( $this->request->data );
		$this->_validateData( $this->SpamChallenge );
		
		$this->SpamChallenge->save();
        
        $response['result'] = 1;
        echo json_encode($response);
	}
	
	public function admin_delete( $id )
	{
		$this->autoRender = false;
		$this->SpamChallenge->delete( $id );
		
		$this->Session->setFlash('Spam challenge deleted');
		$this->redirect( $this->referer() );
	}
	
}
