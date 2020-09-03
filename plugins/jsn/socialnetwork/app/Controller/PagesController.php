<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
JsnApp::uses('AppController', 'Controller');

class PagesController extends AppController 
{
    public $paginate = array( 'limit' => RESULTS_LIMIT );
           
    public function display() 
    {			
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            $this->redirect('/');
        }
		
		if ( file_exists(APP . 'View' . DS . 'Pages' . DS . $path[0] . '.ctp') )
		{
	        $page = $subpage = $title_for_layout = null;
	        if (!empty($path[0])) {
	            $page = $path[0];
	        }
	        if (!empty($path[1])) {
	            $subpage = $path[1];
	        }
	        if (!empty($path[$count - 1])) {
	            $title_for_layout = Inflector::humanize($path[$count - 1]);
	        }
	        $this->set(compact('page', 'subpage', 'title_for_layout'));
			
			//$this->render(implode('/', $path));
			$this->render($path[0]);
		}
		else 
		{
			$alias = $path[$count - 1];
			$page = $this->Page->findByAlias($alias);
			$this->_checkExistence($page);
			
			// check permission
			if ( $page['Page']['permission'] !== '' )
			{
				$permissions = explode(',', $page['Page']['permission']);
				$cuser = $this->_getUser();
				$role_id = $this->_getUserRoleId();
				
				if ( !in_array( $role_id, $permissions ) )
				{
					$this->redirect('/pages/no-permission');
					exit;
				}
			} 
			
			$params = unserialize($page['Page']['params']);
			
			if ( !empty($params['comments']) )
			{
				$this->loadModel('Comment');
				$comments = $this->Comment->getComments($page['Page']['id'], APP_PAGE);
				$this->set('comments', $comments);
			}
			
			
	        
			$this->set('page', $page);
			$this->set('params', $params);
			$this->set('title_for_layout', $page['Page']['title']);
			
			$this->render('view');
		}
    }
        
    public function admin_index()
    {
        $pages = $this->paginate( 'Page' );
        
        $this->set('pages', $pages );
        $this->set('title_for_layout', 'Pages Manager');
    }
    
    public function admin_create($id = null)
    {
        $this->_checkPermission( array( 'super_admin' => true ) );
            
        if ( !empty( $id ) )
        {
            $page = $this->Page->findById($id);
            $this->_checkExistence($page);
            
            $params = unserialize($page['Page']['params']);

            $this->set('title_for_layout', $page['Page']['title']);
        }
        else {
            $page = $this->Page->initFields();
            $params = array('comments' => 1);
            
            $this->set('title_for_layout', 'Create New Page' );
        }
        
        // get all roles
        $this->loadModel('Role');
        $roles = $this->Role->find('all');
        
        $this->set('page', $page);
        $this->set('params', $params);  
		$this->set('roles', $roles);
    }
	
	public function admin_ajax_save( )
	{
		$this->_checkPermission( array( 'super_admin' => true ) );
		$this->autoRender = false;

		if ( !empty( $this->data['id'] ) )
			$this->Page->id = $this->request->data['id'];
        
        $this->request->data['params'] = serialize( array('comments' => $this->request->data['comments']) );
        
        if ( empty( $this->request->data['alias'] ) )
            $this->request->data['alias'] = seoUrl( strtolower($this->request->data['title']) );

		$this->request->data['alias']=str_replace('-','_',$this->request->data['alias']);

        $this->request->data['permission'] = (empty( $this->request->data['everyone'] )) ? implode(',', $_POST['permissions']) : '';
        
		$this->Page->set( $this->request->data );
		$this->_validateData( $this->Page );
		
		$this->Page->save();        
        $this->Session->setFlash('Page has been successfully saved');
        
        Cache::clearGroup('cache_group', '_cache_group_');
        
        $response['result'] = 1;
        $response['page_id'] = $this->Page->id;
        
        echo json_encode($response);
	}

    public function admin_ajax_reorder()
    {
        $this->autoRender = false;
        
        $i = 1;
        foreach ($this->request->data['pages'] as $page_id)
        {
            $this->Page->updateAll( array( 'weight' => $i ), array( 'id' => $page_id ) );
            $i++;
        }
        
        Cache::clearGroup('cache_group', '_cache_group_');
    }
    
    public function admin_delete( $id )
    {
        $this->autoRender = false;
        
        $page = $this->Page->findById( $id );        
        $this->Page->delete( $id );
        
        $this->Session->setFlash('Page deleted');
        $this->redirect( $this->referer() );
        
        Cache::clearGroup('cache_group', '_cache_group_');
    }
}
