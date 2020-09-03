<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class TopicsController extends AppController 
{
	public $paginate = array(        
        'order' => array(
            'Topic.id' => 'desc'
        )
    );
	
	public function index($cat_id = null)
	{ 
		$this->loadModel( 'Tag' );
		$this->loadModel( 'Category' );
		
        $cat_id = intval($cat_id);
		$jsnsocial_setting = $this->_getSettings();
	
		$categories = $this->Category->getCategories( APP_TOPIC );		
		$tags       = $this->Tag->getTags( APP_TOPIC, $jsnsocial_setting['popular_interval'] );
		
		if ( !empty( $cat_id ) )
			$topics  = $this->Topic->getTopics('category', $cat_id);
		else
			$topics  = $this->Topic->getTopics();
			
		$this->set('categories', $categories);
		$this->set('tags', $tags);
		$this->set('topics', $topics);
        $this->set('cat_id', $cat_id);
		$this->set('title_for_layout', __('Topics'));
	}
	
	/*
	 * Browse albums based on $type
	 * @param string $type - possible value: cats, my, home, friends
	 * @param mixed $param - could be catid (category), uid (user) or a query string (search)
	 */
	public function ajax_browse( $type = null, $param = null )
	{
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
		$uid = $this->Session->read('uid');
		
		if ( !empty( $this->request->named['category_id'] ) )
		{
			$type = 'category';
			$param = $this->request->named['category_id'];
		}
		
		$url = ( !empty( $param ) )	? $type . '/' . $param : $type;			
		
		switch ( $type )
		{
			case 'home': 
			case 'my':
			case 'friends':
				$this->_checkPermission();
				$param = $uid;
				break;

			case 'search':
				$jsnsocial_setting = $this->_getSettings();
                $param = urldecode( $param );
                
				if ( !$jsnsocial_setting['guest_search'] && empty( $uid ) )
					$this->_checkPermission();				
				
				break;
				
			case 'group':
				// check permission if group is private
				$this->loadModel('Group');
				$group = $this->Group->findById( $param );
				
				$this->loadModel('GroupUser');
				$is_member = $this->GroupUser->isMember( $uid, $param );
				
				if ( $group['Group']['type'] == PRIVACY_PRIVATE )
				{
					$cuser = $this->_getUser();
					
					if ( !$cuser['Role']['is_admin'] && !$is_member )
						return;
				}
				
				$this->set('is_member', $is_member);
				$this->set('ajax_view', true);
				
				break;
				
			default:
				if ( !empty( $param ) )
					$this->Session->write('cat_id', $param);
		}		
		
		$topics = $this->Topic->getTopics( $type, $param, $page );		
		$this->set('topics', $topics);
		
		$this->set('more_url', '/topics/ajax_browse/' . h($url) . '/page:' . ( $page + 1 ) );
		$this->set('page', $page);
		
		if ( $page == 1 && $type == 'home' )
			$this->render('/Elements/ajax/home_topic');
		elseif ( $page == 1 && $type == 'group' )
			$this->render('/Elements/ajax/group_topic');
		else
			$this->render('/Elements/lists/topics_list');		
	}
	
	/*
	 * Show add/edit topic form
	 * @param int $id - topic id to edit
	 */
	public function create($id = null)
	{
		$id = intval($id);		
		$this->_checkPermission( array( 'confirm' => true ) );	
        $this->_checkPermission( array('aco' => 'topic_create') );    
        	
		$this->loadModel('Category');		
        $role_id = $this->_getUserRoleId();
		
		$cats = $this->Category->getCategoriesList( APP_TOPIC, $role_id );
		$attachments_list = array();
	
		if (!empty($id)) // editing
		{
			$topic = $this->Topic->findById($id);
			$this->_checkExistence( $topic );
            $this->_checkPermission( array( 'admins' => array( $topic['User']['id'] ) ) );
			
			// if it's a group topic, redirect to group view
			if ( !empty( $topic['Topic']['group_id'] ) )
			{
				$this->redirect( '/groups/view/' . $topic['Topic']['group_id'] . '/topic_id:' . $id );
				exit;
			}
			
			$this->loadModel( 'Tag' );		
			$tags = $this->Tag->getContentTags( $id, APP_TOPIC );
			
			$this->loadModel('Attachment');
			$attachments = $this->Attachment->find('all', array('conditions' => array('plugin_id' => PLUGIN_TOPIC_ID, 'target_id' => $id)));           
            
            foreach ($attachments as $a)
                $attachments_list[] = $a['Attachment']['id'];           
				
			$this->set('tags', $tags);	
			$this->set('attachments', $attachments);  				
			$this->set( 'title_for_layout', __('Edit Topic') );
		}
		else
		{
			$topic = $this->Topic->initFields();
			
			if ( $this->Session->check('cat_id') )
			{
				$topic['Topic']['category_id'] = $this->Session->read('cat_id');
				$this->Session->delete('cat_id');
			}
			
			$this->set( 'title_for_layout', __('Create New Topic') );
		}

		$this->set('topic', $topic);
		$this->set('cats', $cats);		
		$this->set('attachments_list', implode(',', $attachments_list));	
	}
	
	/*
	 * Show add/edit group topic form
	 * @param int $id - topic id to edit
	 */
	public function ajax_group_create($id = null)
	{
		$id = intval($id);			
		$this->_checkPermission( array( 'confirm' => true ) );
        $this->_checkPermission( array('aco' => 'topic_create') );    
        
        $attachments_list = array();
	
		if (!empty($id)) // editing
		{
			$topic = $this->Topic->findById($id);
			$this->_checkExistence( $topic );			
            
            $this->loadModel('Attachment');
            $attachments = $this->Attachment->find('all', array('conditions' => array('plugin_id' => PLUGIN_TOPIC_ID, 'target_id' => $id)));           
            
            foreach ($attachments as $a)
                $attachments_list[] = $a['Attachment']['id'];    
            
            $this->set('attachments', $attachments);        
		}
		else
			$topic = $this->Topic->initFields();

		$this->set('topic', $topic);		
        $this->set('attachments_list', implode(',', $attachments_list));
	}
	
	/*
	 * Save add/edit form
	 */
	public function ajax_save()
	{
		$this->_checkPermission( array( 'confirm' => true ) );		
		$this->autoRender = false;			
		$uid = $this->Session->read('uid');
        $this->request->data['attachment'] = ( !empty($this->request->data['attachments']) ) ? 1 : 0;

		if ( !empty( $this->request->data['id'] ) ) // edit topic
		{
			// check edit permission		
			$topic = $this->Topic->findById( $this->request->data['id'] );	
			$this->_checkTopic($topic, true);
            
			$this->Topic->id = $this->request->data['id'];
		}
		else
		{
			 // if it's a group topic, check if user has permission to create topic in this group
			if ( !empty( $this->request->data['group_id'] ) )
			{
				$this->loadModel( 'GroupUser' );
				
				if ( !$this->GroupUser->isMember( $uid, $this->request->data['group_id'] ) )
					return;
			}
				
			$this->request->data['user_id'] = $uid;
			$this->request->data['lastposter_id'] = $uid;
			$this->request->data['last_post'] = date('Y-m-d H:i:s');			
		}
		
		$this->request->data['body'] = str_replace( '../', '/', $this->request->data['body'] );

		$this->Topic->set( $this->request->data );
		$this->_validateData( $this->Topic );
        
        // todo: check if user has permission to post in category
			
		if ( $this->Topic->save() )
		{		
			if ( empty( $this->request->data['id'] ) ) // add topic
			{
				$type = APP_USER;
				$target_id = 0;
				$privacy = PRIVACY_EVERYONE;
					
				if ( !empty( $this->request->data['group_id'] ) )
				{
					$type = APP_GROUP;
					$target_id = $this->request->data['group_id'];
					
					$this->loadModel('Group');
					$group = $this->Group->findById( $this->request->data['group_id'] );
					
					if ( $group['Group']['type'] == PRIVACY_PRIVATE )
						$privacy = PRIVACY_ME;
				}
				
				// insert into activity feed
				$this->loadModel( 'Activity' );
				$this->Activity->save( array( 'type' 	  => $type,
											  'target_id' => $target_id,
											  'action'    => 'topic_create',
											  'user_id'   => $uid,
											  'item_type' => APP_TOPIC,
											  'item_id'   => $this->Topic->id,
											  'privacy'	  => $privacy,
											  'query'	  => 1,
											  'params'	  => 'item'
									) );				
			}	
            
            $this->loadModel( 'Tag' );
            $this->Tag->saveTags( $this->request->data['tags'], $this->Topic->id, APP_TOPIC );  
			
			if ( !empty( $this->request->data['attachments'] ))
			{
				$this->loadModel('Attachment');
				$this->Attachment->updateAll( array( 'Attachment.target_id' => $this->Topic->id ), array( 'Attachment.id' => explode(',', $this->request->data['attachments']) ) );
			}
            
			$response['result'] = 1;
			$response['id'] = $this->Topic->id;
			echo json_encode($response);
		}
	}

	public function view($id = null)
	{
		$id = intval($id);	
		$topic = $this->Topic->findById($id);
		$this->_checkExistence($topic);	
        $this->_checkPermission( array('aco' => 'topic_view') );    
        
		$uid = $this->Session->read('uid');
		
		// if it's a group topic, redirect to group view
		if ( !empty( $topic['Topic']['group_id'] ) )
		{
			$this->redirect( '/groups/view/' . $topic['Topic']['group_id'] . '/topic_id:' . $id );
			exit;
		}
	
		$this->_getTopicDetail( $topic );
	
		$this->loadModel( 'Tag' );
		$tags = $this->Tag->getContentTags( $id, APP_TOPIC );
		
		$areFriends = false;
		if ( !empty($uid) ) //  check if user is a friend
		{
			$this->loadModel( 'Friend' );
			$areFriends = $this->Friend->areFriends( $uid, $topic['User']['id'] );
		}
		
		$likes = $this->Like->getLikes( $id, APP_TOPIC, 5 );
		
		$this->set('areFriends', $areFriends);
		$this->set('tags', $tags);
		$this->set('likes', $likes);	
		$this->set('title_for_layout', $topic['Topic']['title']);
		$this->set('desc_for_layout', $topic['Topic']['body']);
		$this->set('og', DS.$topic['User']['photo']);	
	}
	
	public function ajax_view($id = null)
	{
		$id = intval($id);	
		$topic = $this->Topic->findById($id);
		$this->_checkExistence($topic);	
        $this->_checkPermission( array('aco' => 'topic_view') );    
		
		$this->_getTopicDetail( $topic );	
	}
	
	private function _getTopicDetail( $topic )
	{
		$uid = $this->Session->read('uid');
		
		// if topic belongs to a group, check permission
		if ( !empty( $topic['Topic']['group_id'] ) )
		{		
			$this->loadModel('GroupUser');	
			
			$is_member = $this->GroupUser->isMember( $uid, $topic['Topic']['group_id'] );	
			$this->set('is_member', $is_member);
							
			if ( $topic['Group']['type'] == PRIVACY_PRIVATE )
			{
				$cuser = $this->_getUser();				
				
				if ( !$cuser['Role']['is_admin'] && !$is_member )
				{
					$this->Session->setFlash( __("This is a private group topic and can only be viewed by the group's members"), 'default', array( 'class' => 'error-message') );
					$this->redirect( '/pages/no-permission' );
					
					exit;
				}
			}
			
			$admins = $this->GroupUser->getUsersList( $topic['Topic']['group_id'], GROUP_USER_ADMIN );
			$this->set('admins', $admins);
		}

		$this->loadModel( 'Like' );
		$this->loadModel( 'Comment' );
		
		$comments = $this->Comment->getComments( $topic['Topic']['id'], APP_TOPIC );
		
		// get comment likes
		if ( !empty( $uid ) )
		{								
			$comment_likes = $this->Like->getCommentLikes( $comments, $uid );
			$this->set('comment_likes', $comment_likes);
            
            $like = $this->Like->getUserLike( $topic['Topic']['id'], $uid, APP_TOPIC );
            $this->set('like', $like);
		}
		
		$this->loadModel('Attachment');
		$attachments = $this->Attachment->getAttachments( PLUGIN_TOPIC_ID, $topic['Topic']['id']);
        
        $files = array();
        $pictures = array();
        
        foreach ($attachments as $a)
            if ( in_array(strtolower($a['Attachment']['extension']), array('jpg', 'jpeg', 'png', 'gif') ) )
                $pictures[] = $a;
            else
                $files[] = $a;           
					
		$this->set('files', $files);
        $this->set('pictures', $pictures);
		$this->set('comments', $comments);
		$this->set('topic', $topic);
		$this->set('more_comments', '/comments/ajax_browse/topic/' . $topic['Topic']['id'] . '/page:2');		
	}
	
	/*
	 * Delete topic
	 * @param int $id - topic id to delete
	 */
	public function do_delete($id = null)
	{
		$id = intval($id);	
		$this->ajax_delete( $id );
		
		$this->Session->setFlash( __('Topic has been deleted') );
		$this->redirect( '/topics' );
	} 
	
	public function ajax_delete($id = null)
	{
		$id = intval($id);	
		$this->autoRender = false;
	
		$topic = $this->Topic->findById($id);
		$this->_checkTopic($topic, true);
        	
		$this->Topic->deleteTopic( $id );
	}
	
	public function do_pin( $id = null )
	{
		$id = intval($id);	
		$topic = $this->Topic->findById($id);
        $this->_checkTopic($topic);
		
		$this->Topic->id = $id;
		$this->Topic->save( array( 'pinned' => 1 ) );
        
        $this->Session->setFlash( __('Topic has been pinned') );
        
        if ( !empty( $topic['Topic']['group_id'] ) )
            $this->redirect( '/groups/view/' . $topic['Topic']['group_id'] . '/topic_id:' . $id );
        else
            $this->redirect( '/topics/view/' . $id );
	}
	
	public function do_unpin( $id = null )
	{
		$id = intval($id);	
		$topic = $this->Topic->findById($id);
        $this->_checkTopic($topic);
		
		$this->Topic->id = $id;
		$this->Topic->save( array( 'pinned' => 0 ) );
        
        $this->Session->setFlash( __('Topic has been unpinned') );
        
        if ( !empty( $topic['Topic']['group_id'] ) )
            $this->redirect( '/groups/view/' . $topic['Topic']['group_id'] . '/topic_id:' . $id );
        else
            $this->redirect( '/topics/view/' . $id );
	}
    
    public function do_lock( $id = null )
    {
        $id = intval($id);	
        $topic = $this->Topic->findById($id);
        $this->_checkTopic($topic);
        
        $this->Topic->id = $id;
        $this->Topic->save( array( 'locked' => 1 ) );
        
        $this->Session->setFlash( __('Topic has been locked') );
        
        if ( !empty( $topic['Topic']['group_id'] ) )
            $this->redirect( '/groups/view/' . $topic['Topic']['group_id'] . '/topic_id:' . $id );
        else
            $this->redirect( '/topics/view/' . $id );
    }
    
    public function do_unlock( $id = null )
    {
        $id = intval($id);	
        $topic = $this->Topic->findById($id);
        $this->_checkTopic($topic);
        
        $this->Topic->id = $id;
        $this->Topic->save( array( 'locked' => 0 ) );
        
        $this->Session->setFlash( __('Topic has been unlocked') );
        
        if ( !empty( $topic['Topic']['group_id'] ) )
            $this->redirect( '/groups/view/' . $topic['Topic']['group_id'] . '/topic_id:' . $id );
        else
            $this->redirect( '/topics/view/' . $id );
    }
    
    private function _checkTopic($topic, $allow_author = false)
    {
        $this->_checkExistence( $topic );
        $admins = array();
        
        if ( $allow_author )
            $admins = array( $topic['User']['id'] ); // topic creator
        
        // if it's a group topic then group admins can do it
        if ( !empty( $topic['Topic']['group_id'] ) )
        {
            $this->loadModel( 'GroupUser' );            

            $group_admins = $this->GroupUser->getUsersList( $topic['Topic']['group_id'], GROUP_USER_ADMIN );
            $admins = array_merge( $admins, $group_admins );
        }
        
        $this->_checkPermission( array( 'admins' => $admins ) );
    }

	public function admin_index()
	{
		if ( !empty( $this->request->data['keyword'] ) )
			$this->redirect( '/admin/topics/index/keyword:' . $this->request->data['keyword'] );
			
		$cond = array();
		if ( !empty( $this->request->named['keyword'] ) )
			$cond['MATCH(Topic.title) AGAINST(? IN BOOLEAN MODE)'] = $this->request->named['keyword'];
			
		$topics = $this->paginate( 'Topic', $cond );
        
        $this->loadModel('Category');
        $categories = $this->Category->getCategoriesList( APP_TOPIC );
		
		$this->set('topics', $topics);
        $this->set('categories', $categories);
		$this->set('title_for_layout', 'Topics Manager');
	}
	
	public function admin_delete()
	{
		$this->_checkPermission(array('super_admin' => 1));
		
		if ( !empty( $_POST['topics'] ) )
        {                   
            foreach ( $_POST['topics'] as $topic_id )
                $this->Topic->deleteTopic( $topic_id ); 

            $this->Session->setFlash( 'Topics deleted' );               
        }
		
		$this->redirect( $this->referer() );
	}
    
    public function admin_move()
    {
        if ( !empty( $_POST['topics'] ) && !empty( $this->request->data['category_id'] ) )
        {                   
            foreach ( $_POST['topics'] as $topic_id )
            {
                $this->Topic->id = $topic_id;
                $this->Topic->save( array( 'category_id' => $this->request->data['category_id'] ) );
            }

            $this->Session->setFlash( 'Topics moved' );               
        }
        
        $this->redirect( $this->referer() );
    }
	
}
