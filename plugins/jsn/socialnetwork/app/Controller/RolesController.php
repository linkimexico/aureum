<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class RolesController extends AppController 
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkPermission(array('super_admin' => 1));
    }
        
    public function admin_index()
    {
        $roles = $this->Role->find('all');
        
        $this->set('roles', $roles);
        $this->set('title_for_layout', 'Roles Manager');
    }
    
    public function admin_ajax_create($id = null)
    {
        if (!empty($id))
            $role = $this->Role->findById($id);
        else
            $role = $this->Role->initFields();
        
        $permissions = explode(',', $role['Role']['params']);
        
        // get acos
        $this->loadModel('Aco');
        $acos = $this->Aco->find('all');
        $aco_groups = array();
        
        foreach ( $acos as $aco )
            $aco_groups[$aco['Aco']['group']][] = $aco;

        $this->set('role', $role);
        $this->set('permissions', $permissions);
        $this->set('aco_groups', $aco_groups);
    }
    
    public function admin_ajax_save()
    {
        $this->autoRender = false;

        if ( !empty( $this->data['id'] ) )
            $this->Role->id = $this->request->data['id'];
        
        $params = array();
        
        foreach ( $this->request->data as $key => $val )
            if ( strpos($key, 'param_' ) !== false && !empty($val) )
                $params[] = substr($key, 6); 
            
        $this->request->data['params'] = implode(',', $params);

        $this->Role->set( $this->request->data );
        $this->_validateData( $this->Role );
        
        $this->Role->save();
        $this->Session->setFlash('Role has been successfully updated');
        
        Cache::delete('guest_role');
        
        $response['result'] = 1;
        echo json_encode($response);
    } 

	public function admin_delete()
	{
		$this->_checkPermission(array('super_admin' => 1));
        $this->loadModel('User');

		if ( !empty( $_POST['roles'] ) )
		{
			$roles = $this->Role->find( 'all', array( 'conditions' => array( 'Role.id' => $_POST['roles'] ) ) );
			
			foreach ( $roles as $role )
			{
				$users=$this->User->updateAll( array('role_id'=>2), array('role_id' => $role['Role']['id']) );
				$this->Role->delete( $role['Role']['id'] );
				$this->Session->setFlash( 'Roles deleted' );
			}	
		}
		
		$this->redirect( $this->referer() );
	}
	
}
