<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class CategoriesController extends AppController {

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->_checkPermission( array('super_admin' => true) );
	}

	public function admin_index( $type = null )
	{
		$this->Category->bindModel(
            array('belongsTo' => array('Parent' => array(
                                            'className' => 'Category', 
                                            'foreignKey' => 'parent_id'
        ))));
            
		$cond = array();
		
		if ( !empty($type) )
			$cond = array('Category.type' => $type);
	
		$categories = $this->Category->find('all', array( 'conditions' => $cond, 'order' => 'Category.type asc, Category.weight asc' ) );
		
		$this->set('categories', $categories );
		$this->set('type', $type );
		$this->set('title_for_layout', 'Categories Manager');
	}
	
	 /*
	 * Render add/edit category
	 * @param mixed $id Id of category to edit
	 */
	public function admin_ajax_create( $id = null )
	{
		if (!empty($id))
			$category = $this->Category->findById($id);
		else
        {
			$category = $this->Category->initFields();
            $category['Category']['active'] = 1;
        }
		
		$headers = $this->Category->find( 'list', array( 'conditions' => array( 'header' => 1 ), 'fields' => 'Category.name' ) );
        $headers[0] = '';
        
        // get all roles
        $this->loadModel('Role');
        $roles = $this->Role->find('all');
        
        $this->set('roles', $roles);        
		$this->set('category', $category);	
        $this->set('headers', $headers); 
	}
	
	/*
	 * Handle add/edit category submission
	 */
	public function admin_ajax_save( )
	{
		$this->autoRender = false;

		if ( !empty( $this->data['id'] ) )
			$this->Category->id = $this->request->data['id'];
        
        if ( $this->request->data['header'] )
            $this->request->data['parent_id'] = 0;

        $this->request->data['create_permission'] = (empty( $this->request->data['everyone'] )) ? implode(',', $_POST['permissions']) : '';
		
		$this->Category->set( $this->request->data );
		$this->_validateData( $this->Category );        
		
		$this->Category->save();
        $this->Session->setFlash('Category has been successfully saved');
        
        $response['result'] = 1;
        echo json_encode($response);
	}
	
	public function admin_ajax_reorder()
	{
		$this->autoRender = false;
		
		$i = 1;
		foreach ($this->request->data['cats'] as $cat_id)
		{
			$this->Category->updateAll( array( 'weight' => $i ), array( 'id' => $cat_id ) );
			$i++;
		}
	}
	
	public function admin_delete( $id )
	{
		$this->autoRender = false;
		
		$category = $this->Category->findById( $id );
		
		switch( $category['Category']['type'] )
		{
			case APP_GROUP:
				$this->loadModel('Group');
				$groups = $this->Group->findAllByCategoryId( $id );
				foreach ( $groups as $group )
					$this->Group->deleteGroup( $group );
				
				break;
				
			case APP_TOPIC:
				$this->loadModel('Topic');
				$topics = $this->Topic->findAllByCategoryId( $id );
				foreach ( $topics as $topic )
					$this->Topic->deleteTopic( $topic['Topic']['id'] );
					
				break;
				
			case APP_VIDEO:
				$this->loadModel('Video');
				$videos = $this->Video->findAllByCategoryId( $id );
				foreach ( $videos as $video )
					$this->Video->deleteVideo( $video );
					
				break;
				
			case APP_ALBUM:
				$this->loadModel('Album');
				$albums = $this->Album->findAllByCategoryId( $id );
				foreach ( $albums as $album )
					$this->Album->deleteAlbum( $album['Album']['id'] );
					
				break;
                
            case APP_EVENT:
                $this->loadModel('Event');
                $events = $this->Event->findAllByCategoryId( $id );
                foreach ( $events as $event )
                    $this->Event->deleteEvent( $event );
                    
                break;
		}
		
		$this->Category->delete( $id );
		
		$this->Session->setFlash('Category deleted');
		$this->redirect( $this->referer() );
	}
	
}
