<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class GroupsController extends AppController 
{
	public $paginate = array(        
        'order' => array(
            'Group.id' => 'desc'
        )
    );	
		
	public function index($cat_id = null)
	{
		$jsnsocial_setting = $this->_getSettings();						
		$cat_id = intval($cat_id);		
        
		$this->loadModel('Category');
		$categories = $this->Category->getCategories( APP_GROUP );
        
        if ( !empty( $cat_id ) )
            $groups  = $this->Group->getGroups('category', $cat_id);
        else
            $groups = $this->Group->getGroups();

		$this->set('groups', $groups);        
		$this->set('categories', $categories);
        $this->set('cat_id', $cat_id);
		$this->set('title_for_layout', __('Groups'));
	}
	
/*
	 * Browse events based on $type
	 * @param string $type - possible value: all (default), my, home, friends, category
	 */
	public function ajax_browse( $type = null, $param = null )
	{			
		$page = (!empty($this->params['named']['page'])) ? $this->params['named']['page'] : 1;
		$url = ( !empty( $param ) )	? $type . '/' . $param : $type;
		$uid = $this->Session->read('uid');	
		
		switch ( $type )
		{
			case 'home': 
			case 'my':
			case 'friends':
				$this->_checkPermission();	
				$this->loadModel( 'GroupUser' );				
				$groups = $this->GroupUser->getGroups( $type, $uid, $page );
				break;

			case 'search':
				$jsnsocial_setting = $this->_getSettings();
                $param = urldecode( $param );
                
				if ( !$jsnsocial_setting['guest_search'] && empty( $uid ) )
					$this->_checkPermission();
				else
					$groups = $this->Group->getGroups( 'search', $param, $page );
				
				break;
				
			default: // all, category
				$groups = $this->Group->getGroups( $type, $param, $page );
		}
		
		$this->set('groups', $groups);
		$this->set('more_url', '/groups/ajax_browse/' . h($url) . '/page:' . ( $page + 1 ) );
		
		if ( $page == 1 && $type == 'home' )
			$this->render('/Elements/ajax/home_group');
		else
			$this->render('/Elements/lists/groups_list');		
	}
	
	/*
	 * Show add/edit group form
	 * @param int $id - group id to edit
	 */
	public function create($id = null)
	{
		$id = intval($id);		
		$this->_checkPermission( array( 'confirm' => true ) );
        $this->_checkPermission( array('aco' => 'group_create') );    
        
		$this->loadModel('Category');	
        $role_id = $this->_getUserRoleId(); 	
		$categories = $this->Category->getCategoriesList( APP_GROUP, $role_id );
	
		if (!empty($id)) // editing
		{
			$group = $this->Group->findById($id);
			$this->_checkExistence( $group );
            
            // check edit permission
            $this->loadModel( 'GroupUser' );
            $admins_list = $this->GroupUser->getUsersList( $id, GROUP_USER_ADMIN );
            $this->_checkPermission( array( 'admins' => $admins_list ) );
			
			$this->set( 'title_for_layout', __('Edit Group') );
		}
		else
		{
			$group = $this->Group->initFields();
			$this->set( 'title_for_layout', __('Add New Group') );
		}

		$this->set('group', $group);
		$this->set('categories', $categories);		
	}
	
	/*
	 * Save add/edit form
	 */
	public function ajax_save()
	{
		$this->_checkPermission( array( 'confirm' => true ) );
		$this->loadModel( 'GroupUser' );
		
		$this->autoRender = false;	
		$uid = $this->Session->read('uid');
			
		if ( !empty( $this->request->data['id'] ) ) // edit group
		{
			// check edit permission			
			$admins_list = $this->GroupUser->getUsersList( $this->request->data['id'], GROUP_USER_ADMIN );
			$this->_checkPermission( array( 'admins' => $admins_list ) );
			$this->Group->id = $this->request->data['id'];
		}
		else
			$this->request->data['user_id'] = $uid;

		$this->Group->set( $this->request->data );
		$this->_validateData( $this->Group );
			
		if ( $this->Group->save() )
		{		
			if ( empty( $this->request->data['id'] ) ) // add group
			{
				// make the group creator admin
				$this->GroupUser->save( array( 'group_id' => $this->Group->id, 
											   'user_id' => $uid, 
											   'status' => GROUP_USER_ADMIN
									) );

				// insert into activity feed if it's a public group			
				if ( $this->request->data['type'] != PRIVACY_PRIVATE )
				{
					$this->loadModel( 'Activity' );
					$this->Activity->save( array( 'type' 	  => 'user',
												  'action'    => 'group_create',
												  'user_id'   => $uid,												  
												  'item_type' => 'group',
												  'item_id'   => $this->Group->id,
												  'query'	  => 1
										) );
				}

				if ( !empty( $this->request->data['photo'] ) )
				{
					$newpath = WWW_ROOT . 'uploads' . DS . 'groups';
					
					if ( !file_exists( $newpath ) )
						mkdir( $newpath, 0777, true );

					copy(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $this->request->data['photo'], $newpath . DS . $this->request->data['photo']);
					copy(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . 't_' . $this->request->data['photo'], $newpath . DS . 't_' . $this->request->data['photo']);

					unlink(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $this->request->data['photo']);
					unlink(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . 't_' . $this->request->data['photo']);
				}
			}						
			
			$response['result'] = 1;
            $response['id'] = $this->Group->id;
            
            echo json_encode($response);
		}
	}
	
	public function view($id = null)
	{
		$id = intval($id);	
		$group = $this->Group->findById($id);
		$this->_checkExistence( $group );		
        $this->_checkPermission( array('aco' => 'group_view') );    

		$members = array();
		$admins = array();

		// get group users
		$this->loadModel('GroupUser');
		$members = $this->GroupUser->getUsers( $id, GROUP_USER_MEMBER, null, 10 );
		$group_admins	 = $this->GroupUser->getUsers( $id, GROUP_USER_ADMIN, null, 5 );
		
		$admin_count  = $this->GroupUser->getUserCount( $id, GROUP_USER_ADMIN );
		$member_count = $group['Group']['group_user_count'] - $admin_count;

		$this->_getGroupDetail( $group );

		$this->set('members', $members);
		$this->set('group_admins', $group_admins);		
		$this->set('member_count', $member_count);
		$this->set('admin_count', $admin_count);
		
		$this->set('group', $group);
		$this->set('title_for_layout', $group['Group']['name']);
		$this->set('desc_for_layout', $group['Group']['description']);
		if(!empty($group['Group']['photo'])) $this->set('og', $this->request->webroot.'uploads/groups/'.$group['Group']['photo']);
		else $this->set('og', $this->request->webroot.'img/no-image-groups.jpg');
	}
	
	public function ajax_details($id = null)
	{
		$id = intval($id);		
		$this->loadModel( 'GroupUser' );	
		
		$group = $this->Group->findById($id);
		$this->_getGroupDetail( $group );

		$this->set('group', $group);		
		$this->render('/Elements/ajax/group_detail');
	}
	
	private function _getGroupDetail( $group )
	{
		$uid = $this->Session->read('uid');

		if ($uid)
		{	
			$my_status = $this->GroupUser->getMyStatus( $uid, $group['Group']['id'] );
			$is_member = $this->GroupUser->isMember( $uid, $group['Group']['id'] );
			
			$this->set('my_status', $my_status);
			$this->set('is_member', $is_member);
			
			if ( !empty($my_status) && $my_status['GroupUser']['status'] == GROUP_USER_ADMIN )
			{
				$request_count = $this->GroupUser->find( 'count', array( 'conditions' => array( 'group_id' => $group['Group']['id'], 
																								'status' => GROUP_USER_REQUESTED ) 
				) );
				
				$this->set('request_count', $request_count);
			}
		}

		$this->loadModel( 'Photo' );
		$photos = $this->Photo->getPhotos( APP_GROUP, $group['Group']['id'], null, 4 );
		
		// get activities
		$this->loadModel( 'Activity' );
		$activities = $this->Activity->getActivities( 'group', $group['Group']['id'] );
		
		// get activity likes
		if ( !empty( $uid ) )
		{					
			$this->loadModel('Like');				
			$activity_likes = $this->Like->getActivityLikes( $activities, $uid );
			$this->set('activity_likes', $activity_likes);
		}
		
		$admins = $this->GroupUser->getUsersList( $group['Group']['id'], GROUP_USER_ADMIN );

		$this->set('admins', $admins);
		$this->set('photos', $photos);
		$this->set('activities', $activities);
	}

	public function do_request($id = null)
	{
		$id = intval($id);
		$this->_checkPermission( array( 'confirm' => true ) );		
		$uid = $this->Session->read('uid');		 
		$this->loadModel( 'GroupUser' );		

		$data['user_id'] = $uid;
		$data['group_id'] = $id;
		
		// check if user has a group_user record
		$my_status = $this->GroupUser->getMyStatus( $uid, $id );
		$group = $this->Group->findById($id);

		if ( !empty( $my_status ) ) // user has a record in group_user table
		{
			if ( $my_status['GroupUser']['status'] == GROUP_USER_INVITED ) // user was invited
			{			
				$data['status'] = GROUP_USER_MEMBER;
				$this->GroupUser->id = $my_status['GroupUser']['id'];
			}
			else
				$this->redirect( '/pages/error' );
		}
		else
		{
			switch ( $group['Group']['type'] )
			{
				case PRIVACY_RESTRICTED:
					$data['status'] = GROUP_USER_REQUESTED;
					break;
					
				case PRIVACY_PUBLIC:
					$data['status'] = GROUP_USER_MEMBER;
					break;
					
				case PRIVACY_PRIVATE:
                    $this->Session->setFlash( __('This is a private group. You must be invited by a group admin in order to join'), 'default', array( 'class' => 'error-message') );
					$this->redirect( '/pages/error' ); // make sure that user is not trying to join a private group if he was not invited
					break;
			}
		}
		
		$this->GroupUser->save($data);

		if ( $data['status'] == GROUP_USER_REQUESTED ) // requested
		{
			$this->Session->setFlash( __('Your join request has been sent') );
									
			$this->loadModel( 'Notification' );
			$this->Notification->record( array( 'recipients' => $group['Group']['user_id'],
												'sender_id' => $uid,
												'action' => 'group_request',
												'url' => '/groups/view/'.$id,
												'params' => h($group['Group']['name'])
										) );
		}
		else // joined		
			$this->_updateActivity( $group['Group'], $uid );		
		
		$this->redirect( '/groups/view/' . $id );
	}
	
	private function _updateActivity( $group, $uid )
	{
		if ( $group['type'] != PRIVACY_PRIVATE )
		{
			$this->loadModel( 'Activity' );		
			$activity = $this->Activity->getRecentActivity( 'group_join', $uid );				
											 
			if ( !empty( $activity ) )
			{
				// aggregate activities
				$group_ids = explode( ',', $activity['Activity']['items'] );
                
                if ( !in_array($group['id'], $group_ids) )
				    $group_ids[] = $group['id'];
				
				$this->Activity->id = $activity['Activity']['id'];
				$this->Activity->save( array( 'items'   => implode( ',', $group_ids ),	
											  'item_id' => 0,
											  'params'	=> '',									  
											  'query'	=> 1
									) );
			}
			else
			{
				$this->Activity->save( array( 'type'      => 'user',
											  'action'    => 'group_join',
											  'user_id'   => $uid,
											  'item_type' => APP_GROUP,
											  'item_id'   => $group['id'],													  
											  'params' 	  => '<a href="' . $this->request->base . '/groups/view/' . $group['id'] . '">' . h($group['name']) . '</a>',
											  'items'	  => $group['id']											  
									) );
			}
		}
	}

	public function ajax_members($id = null)
	{
		$id = intval($id);
		$this->loadModel( 'GroupUser' );			
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;	
		
		$members = $this->GroupUser->getUsers( $id, array( GROUP_USER_MEMBER, GROUP_USER_ADMIN ), $page );
		$group = $this->Group->findById( $id );
		$admins = $this->GroupUser->getUsersList( $id, GROUP_USER_ADMIN );

		$this->set('users', $members);
		$this->set('group', $group);
		$this->set('admins', $admins);
		$this->set('more_url', '/groups/ajax_members/' . $id . '/page:' . ( $page + 1 ) );	
		
		if ($page == 1 )
			$this->render('ajax_members');
		else
			$this->render('/Elements/lists/users_list');
	}
	
	/*
	 * Show invite form
	 * @param int $group_id
	 */
	public function ajax_invite( $group_id = null )
	{
		$group_id = intval($group_id);
		$this->_checkPermission( array( 'confirm' => true ) );	

		$this->set('group_id', $group_id);
	}
	
	/*
	 * Handle invite submission
	 */
	public function ajax_sendInvite()
	{
		$this->autoRender = false;
		$this->_checkPermission( array( 'confirm' => true ) );
		$cuser = $this->_getUser();
		$this->loadModel( 'GroupUser' );
		
		if ( !empty( $this->request->data['friends'] ) || !empty( $this->request->data['emails'] ) )
		{		
			$group = $this->Group->findById( $this->request->data['group_id'] );
			$admins_list = $this->GroupUser->getUsersList( $this->request->data['group_id'], GROUP_USER_ADMIN );
			
			// check if user can invite
			if ( $group['Group']['type'] == PRIVACY_PRIVATE && ( !in_array( $cuser['id'], $admins_list) ) )
				return;
			
			if ( !empty( $this->request->data['friends'] ) )
			{							
				$data = array();	
                $friends = explode(',', $this->request->data['friends']);
                $group_users = $this->GroupUser->getUsersList($this->request->data['group_id']);
				
				foreach ($friends as $friend_id)	
                    if ( !in_array($friend_id, $group_users) )    		
					   $data[] = array('group_id' => $this->request->data['group_id'], 'user_id' => $friend_id, 'status' => GROUP_USER_INVITED);
	            
                if ( !empty($data) )
                {
    				$this->GroupUser->saveAll($data);
    				
    				$this->loadModel( 'Notification' );
    				$this->Notification->record( array( 'recipients' => $friends,
    													'sender_id' => $cuser['id'],
    													'action' => 'group_invite',
    													'url' => '/groups/view/'.$this->request->data['group_id'],
    													'params' => h($group['Group']['name'])
    											) );
                }
			}
			
			if ( !empty( $this->request->data['emails'] ) )
			{
				$emails = explode( ',', $this->request->data['emails'] );
				
				$i = 1;
				foreach ( $emails as $email )
				{
					if ( $i <= 10 )
					{
						if ( Validation::email( trim($email) ) )
						{
							$text = h($cuser['name']) . ' ' . __('invited you to join "%s"', h($group['Group']['name']) );
							$this->_sendEmail( trim($email),
											   $text,
											   'general',
											   array(
												   'text' => $text, 
												   'url' => $this->request->base . '/groups/view/'.$this->request->data['group_id'] )
											 );
						}
					}
					$i++;
				}
			}
			
			echo __('Your invitations have been sent.') . ' <a href="javascript:void(0)" onclick="inviteMore()">' . __('Invite more friends') . '</a>';
		} 
	}
	
	/*
	 * Remove member from group
	 * @param int $id - id of group_user record
	 */
	
	public function ajax_remove_member()
	{
		$this->autoRender = false;
		$this->loadModel( 'GroupUser' );
		
		if ( empty( $this->request->data['id'] ) )
			return;
		
		$group_user  = $this->GroupUser->findById( $this->request->data['id'] );		
		$admins_list = $this->GroupUser->getUsersList( $group_user['GroupUser']['group_id'], GROUP_USER_ADMIN );
		
		$this->_checkPermission( array( 'admins' => $admins_list ) );		
		$this->GroupUser->delete( $this->request->data['id'] );
	}
	/*
	 * Promote/demote group admin
	 * @param int $id - id of group_user record
	 * @param string $type - make => make admin
	 */
	
	public function ajax_change_admin()
	{
		$this->autoRender = false;
		$this->loadModel( 'GroupUser' );
		
		if ( empty( $this->request->data['id'] ) || empty( $this->request->data['type'] ) )
			return;
		
		$this->GroupUser->id = $id;
		$group_user  		 = $this->GroupUser->findById( $this->request->data['id'] );	
		$admins_list 		 = $this->GroupUser->getUsersList( $group_user['GroupUser']['group_id'], GROUP_USER_ADMIN );
		
		$this->_checkPermission( array( 'admins' => $admins_list ) );
		$group_user['GroupUser']['status'] = ( $this->request->data['type'] == 'make' ) ? GROUP_USER_ADMIN : GROUP_USER_MEMBER;
		
		$this->GroupUser->save( $group_user['GroupUser'] );
	}
	
	public function ajax_requests( $group_id = null )
	{
		$this->loadModel( 'GroupUser' );
		
		$admins_list = $this->GroupUser->getUsersList( $group_id, GROUP_USER_ADMIN );		
		$this->_checkPermission( array( 'admins' => $admins_list ) );
		
		$requests = $this->GroupUser->getUsers( $group_id, GROUP_USER_REQUESTED );		
		$this->set('requests', $requests);
	}
	
	public function ajax_respond()
	{
		$this->autoRender = false;
		$this->loadModel( 'GroupUser' );
		
		$this->GroupUser->id = $this->request->data['id'];
		$group_user  		 = $this->GroupUser->read();		
		$admins_list 		 = $this->GroupUser->getUsersList( $group_user['GroupUser']['group_id'], GROUP_USER_ADMIN );
		
		$this->_checkPermission( array( 'admins' => $admins_list ) );
		
		if ( !empty( $this->request->data['status'] ) ) // accept
		{
			$this->GroupUser->save( array( 'status' => GROUP_USER_MEMBER ) );
			
			$this->_updateActivity( $group_user['Group'], $group_user['GroupUser']['user_id'] );
			echo ' <a href="' . $this->request->base . '/users/view/' . $group_user['GroupUser']['user_id'] . '">' . $group_user['User']['name'] . '</a> ' . __('is now a member of this group');
		}
		else
		{
			$this->GroupUser->delete( $this->request->data['id'] );
			
			echo __('You have deleted the request. The sender will not be notified');
		}
	}
	
	/*
	 * Leave group
	 * @param int $id - id of group
	 */
	
	public function do_leave( $id )
	{
		$id = intval($id);
		$this->_checkPermission();
		$uid = $this->Session->read('uid');
		
		$this->loadModel( 'GroupUser' );
		$my_status = $this->GroupUser->getMyStatus( $uid, $id );
		
		if ( !empty( $my_status ) && ( $uid != $my_status['Group']['user_id'] ) )
		{
			$this->GroupUser->delete( $my_status['GroupUser']['id'] );
			
			// remove associated activity
			if ( $my_status['Group']['type'] != PRIVACY_PRIVATE )
			{
				$this->loadModel('Activity');
				$this->Activity->deleteAll( array( 'user_id'   => $uid,
												   'action'    => 'group_join',
												   'item_type' => 'group',
												   'item_id'   => $id
				 ), true, true );
			}
			
			$this->Session->setFlash( __('You have successfully left this group') );
		}
		
		$this->redirect( '/groups/view/' . $id );
	}
    
    public function do_feature( $id = null )
    {
        $id = intval($id);
        $group = $this->Group->findById($id);
        $this->_checkExistence( $group );
        
        $this->_checkPermission( array( 'is_admin' => true ) );
        
        $this->Group->id = $id;
        $this->Group->save( array( 'featured' => 1 ) );
        
        $this->Session->setFlash( __('Group has been featured') );
        $this->redirect( $this->referer() );
        
    }
    
    public function do_unfeature( $id = null )
    {
        $group = $this->Group->findById($id);
        $this->_checkExistence( $group );
        
        $this->_checkPermission( array( 'is_admin' => true ) );
        
        $this->Group->id = $id;
        $this->Group->save( array( 'featured' => 0 ) );
        
        $this->Session->setFlash( __('Group has been unfeatured') );
        $this->redirect( $this->referer() );
    }
	
	/*
	 * Delete group
	 * @param int $id - group id to delete
	 */
	public function do_delete($id = null)
	{
		$id = intval($id);
		$group = $this->Group->findById($id);
		$this->_checkExistence( $group );
        
		$this->_checkPermission( array('aco' => 'group_delete') ); 
		$this->_checkPermission( array( 'admins' => array( $group['Group']['user_id'] ) ) );
		
		$this->Group->deleteGroup( $group );
		
		$this->Session->setFlash( __('Group has been deleted') );
		$this->redirect( '/groups' );
	} 
	
	public function admin_index()
	{
		if ( !empty( $this->request->data['keyword'] ) )
			$this->redirect( '/admin/groups/index/keyword:' . $this->request->data['keyword'] );
			
		$cond = array();
		if ( !empty( $this->request->named['keyword'] ) )
			$cond['MATCH(Group.name) AGAINST(? IN BOOLEAN MODE)'] = $this->request->named['keyword'];
			
		$groups = $this->paginate( 'Group', $cond );	
        
        $this->loadModel('Category');
        $categories = $this->Category->getCategoriesList( APP_GROUP );
		
		$this->set('groups', $groups);
        $this->set('categories', $categories);
		$this->set('title_for_layout', 'Groups Manager');
	}
	
	public function admin_delete()
	{
		$this->_checkPermission(array('super_admin' => 1));
		
		if ( !empty( $_POST['groups'] ) )
		{
			$groups = $this->Group->findAllById( $_POST['groups'] );
			
			foreach ( $groups as $group )
				$this->Group->deleteGroup( $group );
			
			$this->Session->setFlash( 'Groups deleted' );	
		}
		
		$this->redirect( $this->referer() );
	}
    
    public function admin_move()
    {
        if ( !empty( $_POST['groups'] ) && !empty( $this->request->data['category_id'] ) )
        {                   
            foreach ( $_POST['groups'] as $group_id )
            {
                $this->Group->id = $group_id;
                $this->Group->save( array( 'category_id' => $this->request->data['category_id'] ) );
            }

            $this->Session->setFlash( 'Groups moved' );               
        }
        
        $this->redirect( $this->referer() );
    }
}

?>
