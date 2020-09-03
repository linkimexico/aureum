<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class VideosController extends AppController 
{
	public function index($cat_id = null)
	{
		$this->loadModel( 'Tag' );
		$this->loadModel( 'Category' );
		
        $cat_id = intval($cat_id);   
		$jsnsocial_setting = $this->_getSettings();
        
		$categories    = $this->Category->getCategories( APP_VIDEO );		
		$tags 	 = $this->Tag->getTags( APP_VIDEO, $jsnsocial_setting['popular_interval'] );
		
		if ( !empty( $cat_id ) )
			$videos  = $this->Video->getVideos('category', $cat_id);
		else
			$videos  = $this->Video->getVideos();
		
		$this->set('tags', $tags);
		$this->set('videos', $videos);
		$this->set('categories', $categories);
        $this->set('cat_id', $cat_id);
		$this->set('title_for_layout', __('Videos'));
	}
	
	/********************
	* Ajax Add New Video
	********************/
	public function ajax_save()
	{			
		$this->_checkPermission( array( 'confirm' => true ) );		
		$this->autoRender = false;	
		$uid = $this->Session->read('uid');

		if ( !empty($this->request->data['id']) ) // edit video
		{			
			// check edit permission			
			$video = $this->Video->findById( $this->request->data['id'] );
			$admins = array( $video['User']['id'] ); // video creator
			
			 // if it's a group video, add group admins to the admins array for permission checking
			if ( !empty( $video['Video']['group_id'] ) )
			{
				$this->loadModel( 'GroupUser' );
				
				$group_admins = $this->GroupUser->getUsersList( $video['Video']['group_id'], GROUP_USER_ADMIN );
				$admins = array_merge( $admins, $group_admins );
			}
			
			$this->_checkPermission( array( 'admins' => $admins ) );
			$this->Video->id = $this->request->data['id'];
		}
		else
		{
			// if it's a group video, check if user has permission to create topic in this group
			if ( !empty( $this->request->data['group_id'] ) )
			{
				$this->loadModel( 'GroupUser' );
				
				if ( !$this->GroupUser->isMember( $uid, $this->request->data['group_id'] ) )
					return;
			}
				
			$this->request->data['user_id'] = $uid;
			$this->request->data['thumb'] = md5( time() ) . '.jpg';
		}
		
		$this->Video->set( $this->request->data );
		$this->_validateData( $this->Video );

		if ( $this->Video->save() ) // successfully saved	
		{
			if ( empty($this->request->data['id']) ) // add video
			{
				// copy thumb
				//copy( $this->request->data['thumb_url'], WWW_ROOT . 'uploads/videos/' . $this->request->data['thumb'] );
				//$thumb = file_get_contents( $this->request->data['thumb_url'] );
				//file_put_contents( WWW_ROOT . 'uploads/videos/' . $this->request->data['thumb'], $thumb );
                
                //JsnApp::uses('HttpSocket', 'Network/Http');
                //$HttpSocket = new HttpSocket();
                
                //$thumb = $HttpSocket->get( $this->request->data['thumb_url'] );
				$thumb = file_get_contents( $this->request->data['thumb_url'] );
                file_put_contents( WWW_ROOT . 'uploads/videos/' . $this->request->data['thumb'], $thumb );
				
				$type = APP_USER;
				$target_id = 0;
				$privacy = $this->request->data['privacy'];
					
				if ( !empty( $this->request->data['group_id'] ) )
				{
					$type = APP_GROUP;
					$target_id = $this->request->data['group_id'];
					
					$this->loadModel('Group');
					$group = $this->Group->findById( $this->request->data['group_id'] );
					
					if ( $group['Group']['type'] == PRIVACY_PRIVATE )
						$privacy = PRIVACY_ME;
				}
				
				// insert activity
				$this->loadModel( 'Activity' );
				$this->Activity->save( array( 'type' 	  => $type,
											  'target_id' => $target_id,
											  'action'    => 'video_add',
											  'user_id'   => $uid,
											  'item_type' => APP_VIDEO,
											  'item_id'   => $this->Video->id,
											  'query'	  => 1,
											  'privacy'	  => $privacy,
											  'params'	  => 'item'
									) );
			}

            // save tags
            $this->loadModel( 'Tag' );
            $this->Tag->saveTags( $this->request->data['tags'], $this->Video->id, APP_VIDEO );  
			
			$response['result'] = 1;
            $response['id'] = $this->Video->id;
			echo json_encode($response);
		}
	}

	/*
	 * Browse videos based on $type
	 * @param string $type - possible value: all (default), my, home friends, user, search
	 * @param mixed $param - could be uid (user) or a query string (search)
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
		}	
		
		$videos = $this->Video->getVideos( $type, $param, $page );		
		$this->set('videos', $videos);
		$this->set('more_url', '/videos/ajax_browse/' . h($url) . '/page:' . ( $page + 1 ) );
		
		if ( $page == 1 && $type == 'home' )
			$this->render('/Elements/ajax/home_video');
		elseif ( $page == 1 && $type == 'group' )
			$this->render('/Elements/ajax/group_video');
		else
			$this->render('/Elements/lists/videos_list');		
	}

	public function ajax_create($vid = 0)
	{
		$vid = intval($vid);
		$this->_checkPermission( array( 'confirm' => true ) );
        $this->_checkPermission( array('aco' => 'video_share') );    
		
		if ( !empty( $vid ) )
		{
			$video = $this->Video->findById($vid);
			$this->_checkExistence( $video );
            $this->_checkPermission( array( 'admins' => array( $video['User']['id'] ) ) );

			$this->loadModel( 'Tag' );			
			$tags = $this->Tag->getContentTags( $vid, APP_VIDEO );
			
			$this->loadModel('Category');
            $role_id = $this->_getUserRoleId(); 		
			$categories = $this->Category->getCategoriesList( APP_VIDEO, $role_id );
			
			$this->set('tags', $tags);
			$this->set('video', $video);
			$this->set('categories', $categories);
			
			$this->render('ajax_fetch');
		}
	}
	
	/*
	 * Show add/edit group topic form
	 * @param int $id - topic id to edit
	 */
	public function ajax_group_create($id = null)
	{
		$id = intval($id);		
		$this->_checkPermission( array( 'confirm' => true ) );
        $this->_checkPermission( array('aco' => 'video_share') );    
	
		if (!empty($id)) // editing
		{
			$video = $this->Video->findById($id);
			$this->_checkExistence( $video );
			
			$this->set('video', $video);			
			$this->render('ajax_group_fetch');		
		}
		else
			$this->render('ajax_create');			
	}
	
	public function ajax_fetch()
	{
		$this->_checkPermission( array( 'confirm' => true ) );		
        
        $jsnsocial_setting = $this->_getSettings();		
		$video = $this->Video->fetchVideo( $this->request->data['source'], $this->request->data['url'], $jsnsocial_setting );
		
		if ( !empty( $video ) )
		{
		    $this->set('video', $video);	
			
		    if ( empty( $this->request->data['group_id'] ) ) // public video
			{	
			    $this->loadModel('Category');		
                $role_id = $this->_getUserRoleId(); 
				$categories = $this->Category->getCategoriesList( APP_VIDEO, $role_id );
				
				$this->set('categories', $categories);
			}
			else // group video
				$this->render('ajax_group_fetch');			
		    
		    $this->set('video', $video);		    
		}
		else
		{
			$this->autoRender = false;
			echo '<span style="color:red">' . __('Invalid URL. Please try again') . '</span>';
		}
	}
	
	public function ajax_embed()
	{
		$this->autoRender = false;
        
        $w = ( $this->request->is('mobile') ) ? 300 : 400;
        $h = ( $this->request->is('mobile') ) ? 225 : 300;
		
		switch ( $this->request->data['source'] )
		{
			case 'youtube':
				echo '<iframe width="' . $w . '" height="' . $h . '" src="http://www.youtube.com/embed/' . h($this->request->data['source_id']) . '?wmode=opaque" frameborder="0" allowfullscreen></iframe>';
				break;
				
			case 'vimeo':				
				echo '<iframe src="http://player.vimeo.com/video/' . h($this->request->data['source_id']) . '" width="' . $w . '" height="' . $h . '" frameborder="0"></iframe>';
				break;
		}
	}
	
	public function view( $id = null )
	{
		$id = intval($id);
		$video = $this->Video->findById($id);		
		$this->_checkExistence( $video );
        $this->_checkPermission( array('aco' => 'video_view') );    
        
		$uid = $this->Session->read('uid');		
		
		// if it's a group video, redirect to group view
		if ( !empty( $video['Video']['group_id'] ) )
		{
			$this->redirect( '/groups/view/' . $video['Video']['group_id'] . '/video_id:' . $id );
			exit;
		}
		
		$this->_getVideoDetail( $video );
				
		$this->loadModel( 'Tag' );
		
		$tags = $this->Tag->getContentTags( $id, APP_VIDEO );			
		$similar_videos = $this->Tag->getSimilarVideos( $id, $tags );
		$likes = $this->Like->getLikes( $id, APP_VIDEO, 5 );
		
		$this->set('tags', $tags);
		$this->set('similar_videos', $similar_videos);	
		$this->set('likes', $likes);	
		$this->set('title_for_layout', $video['Video']['title']);
		$this->set('desc_for_layout', $video['Video']['description']);	
		if(!empty($video['Video']['thumb'])) $this->set('og', $this->request->webroot.'uploads/videos/'.$video['Video']['thumb']);
	}
	
	public function ajax_view( $id = null )
	{
		$id = intval($id);
		$video = $this->Video->findById($id);		
		$this->_checkExistence( $video );
        $this->_checkPermission( array('aco' => 'video_view') );    
		
		$this->_getVideoDetail( $video );	
	}
	
	private function _getVideoDetail( $video )
	{
		$uid = $this->Session->read('uid');
		$admins = array( $video['Video']['user_id'] );
		
		$this->_checkPrivacy( $video['Video']['privacy'], $video['User']['id'] );
		
		// if video belongs to a group, check permission
		if ( !empty( $video['Video']['group_id'] ) )
		{
			$this->loadModel('GroupUser');	
			
			$is_member = $this->GroupUser->isMember( $uid, $video['Video']['group_id'] );	
			$this->set('is_member', $is_member);			
							
			if ( $video['Group']['type'] == PRIVACY_PRIVATE )
			{
				$cuser = $this->_getUser();				
				
				if ( !$cuser['Role']['is_admin'] && !$is_member )
				{
					$this->Session->setFlash( __("This is a private group video and can only be viewed by the group's members"), 'default', array( 'class' => 'error-message') );
					$this->redirect( '/pages/no-permission' );
					
					exit;
				}
			}
			
			$group_admins = $this->GroupUser->getUsersList( $video['Video']['group_id'], GROUP_USER_ADMIN );
			$admins = array_merge( $admins, $group_admins );			
		}

		$this->loadModel( 'Comment' );
		$this->loadModel( 'Like' );
		
		$comments = $this->Comment->getComments( $video['Video']['id'], APP_VIDEO );
		$comment_count = $this->Comment->getCommentsCount( $video['Video']['id'], APP_VIDEO );
		
		// get comment likes
		if ( !empty( $uid ) )
		{								
			$comment_likes = $this->Like->getCommentLikes( $comments, $uid );
			$this->set('comment_likes', $comment_likes);
			
			$like = $this->Like->getUserLike( $video['Video']['id'], $uid, APP_VIDEO );
			$this->set('like', $like);
		}
		
		$this->set('comments', $comments);
		$this->set('comment_count', $comment_count);;
		$this->set('video', $video);		
		$this->set('more_comments', '/comments/ajax_browse/video/' . $video['Video']['id'] . '/page:2');		
		$this->set('admins', $admins);
	}
	
	/*
	 * Delete video
	 * @param int $id - video id to delete
	 */
	public function do_delete($id = null)
	{
		$id = intval($id);
		$this->ajax_delete( $id );
		
		$this->Session->setFlash( __('Video has been deleted') );
		$this->redirect( '/videos' );
	} 

	public function ajax_delete($id = null)
	{
		$id = intval($id);
		$this->autoRender = false;
	
		$video = $this->Video->findById($id);
		$this->_checkExistence( $video );
		
		$admins = array( $video['User']['id'] ); // video creator			
		
		if ( !empty( $video['Video']['group_id'] ) ) // if it's a group video, add group admins to the admins array
		{
			$this->loadModel('GroupUser');
			
			$group_admins = $this->GroupUser->getUsersList( $video['Video']['group_id'], GROUP_USER_ADMIN );
			$admins = array_merge( $admins, $group_admins );
		}
		
		$this->_checkPermission( array( 'admins' => $admins ) );		
		$this->Video->deleteVideo( $video );
	}

	public function admin_index()
	{
		if ( !empty( $this->request->data['keyword'] ) )
			$this->redirect( '/admin/videos/index/keyword:' . $this->request->data['keyword'] );
			
		$cond = array();
		if ( !empty( $this->request->named['keyword'] ) )
			$cond['MATCH(Video.title) AGAINST(? IN BOOLEAN MODE)'] = $this->request->named['keyword'];
			
		$videos = $this->paginate( 'Video', $cond );	
        
        $this->loadModel('Category');
        $categories = $this->Category->getCategoriesList( APP_VIDEO );
		
		$this->set('videos', $videos);
        $this->set('categories', $categories);
		$this->set('title_for_layout', 'Videos Manager');
	}
	
	public function admin_delete()
	{
		$this->_checkPermission(array('super_admin' => 1));
		
		if ( !empty( $_POST['videos'] ) )
		{					
			$videos = $this->Video->findAllById( $_POST['videos'] );
		
			foreach ( $videos as $video )
				$this->Video->deleteVideo( $video );	

			$this->Session->setFlash( 'Videos deleted' );				
		}
		
		$this->redirect( $this->referer() );
	}
    
    public function admin_move()
    {
        if ( !empty( $_POST['videos'] ) && !empty( $this->request->data['category_id'] ) )
        {                   
            foreach ( $_POST['videos'] as $video_id )
            {
                $this->Video->id = $video_id;
                $this->Video->save( array( 'category_id' => $this->request->data['category_id'] ) );
            }

            $this->Session->setFlash( 'Videos moved' );               
        }
        
        $this->redirect( $this->referer() );
    }

}

