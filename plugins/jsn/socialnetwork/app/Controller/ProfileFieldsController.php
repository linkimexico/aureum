<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class ProfileFieldsController extends AppController
{
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->_checkPermission(array('super_admin' => 1));
	}
	
	/*
	 * Render listing fields
	 */
	public function admin_index()
	{
		$fields = $this->ProfileField->find( 'all' );
		$this->set('fields', $fields);
		$this->set('title_for_layout', 'Custom Profile Fields');
	}
	
	/*
	 * Render add/edit field
	 * @param mixed $id Id of field to edit
	 */
	public function admin_ajax_create( $id = null )
	{
		if (!empty($id))
			$field = $this->ProfileField->findById($id);
		else
			$field = $this->ProfileField->initFields();
		
		$this->set('field', $field);
	}
	
	/*
	 * Handle add/edit field submission
	 */
	public function admin_ajax_save( )
	{
		$this->autoRender = false;

		if ( !empty( $this->data['id'] ) )
			$this->ProfileField->id = $this->request->data['id'];

		$this->ProfileField->set( $this->request->data );
		$this->_validateData( $this->ProfileField );
		
		$this->ProfileField->save( $this->request->data );
        
        if ( $this->request->data['type'] == 'heading' && empty( $this->request->data['id'] ) ) // insert dummy value
        {
            $this->loadModel('ProfileFieldValue');
            $this->ProfileFieldValue->save( array( 'profile_field_id' => $this->ProfileField->id ) );
        }

        $this->Session->setFlash('Profile field has been successfully saved');

        $response['result'] = 1;
        echo json_encode($response);
	}
	
	public function admin_ajax_reorder()
	{
		$this->autoRender = false;
		
		$i = 1;
		foreach ($this->request->data['fields'] as $field_id)
		{
			$this->ProfileField->updateAll( array( 'weight' => $i ), array( 'id' => $field_id ) );
			$i++;
		}
	}
	
	public function admin_delete( $id )
	{
		$this->autoRender = false;
		
		$this->ProfileField->delete( $id );
		
		$this->Session->setFlash('Field deleted');
		$this->redirect( $this->referer() );
	}
}