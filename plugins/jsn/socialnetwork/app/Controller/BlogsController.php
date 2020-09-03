<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class BlogsController extends AppController {
	
	public $paginate = array( 'limit' => RESULTS_LIMIT );
	
	public function index()
	{
		$jsnsocial_setting = $this->_getSettings();
		$this->loadModel( 'Tag' );
			
		$blogs = $this->Blog->getBlogs();
		$tags = $this->Tag->getTags(APP_BLOG, $jsnsocial_setting['popular_interval']);
		
		$this->set('tags', $tags);
		$this->set('blogs', $blogs);
		$this->set('title_for_layout', __('Blogs'));
	}
	
	/*
	 * Browse entries based on $type
	 * @param string $type - possible value: all (default), my, home, friends, search
	 * @param mixed $param - could be uid (user) or a query string (search)
	 */
	public function ajax_browse( $type = null, $param = null )
	{
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
		$url = ( !empty( $param ) )	? $type . '/' . $param : $type;
		$uid = $this->Session->read('uid');	
		
		switch ( $type )
		{
			case 'home': 
			case 'my':
				$this->set('user_blog', true);
				$this->_checkPermission();
				$param = $uid;
				break;
				
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
		}	
		
		$blogs = $this->Blog->getBlogs( $type, $param, $page );		
		$this->set('blogs', $blogs);	
		$this->set('more_url', '/blogs/ajax_browse/' . h($url) . '/page:' . ( $page + 1 ) );
		
		if ( $page == 1 && $type == 'home' )
			$this->render('/Elements/ajax/home_blog');
		else
			$this->render('/Elements/lists/blogs_list');
	}
	
/*
	 * Show add/edit topic form
	 * @param int $id - topic id to edit
	 */
	public function create($id = null)
	{
		$id = intval($id);		
		$this->_checkPermission( array( 'confirm' => true ) );		
        $this->_checkPermission( array('aco' => 'blog_create') );    
	
		if (!empty($id)) // editing
		{
			$blog = $this->Blog->findById($id);
			$this->_checkExistence( $blog );
            $this->_checkPermission( array( 'admins' => array( $blog['User']['id'] ) ) );
			
			$this->loadModel( 'Tag' );
			$tags = $this->Tag->getContentTags( $id, APP_BLOG );
				
			$this->set('tags', $tags);			
			$this->set( 'title_for_layout', __('Edit Entry') );
		}
		else
		{
			$blog = $this->Blog->initFields();
			$this->set( 'title_for_layout', __('Write New Entry') );
		}

		$this->set('blog', $blog);	
	}
	
	/*
	 * Save add/edit form
	 */
	public function ajax_save()
	{
		$this->_checkPermission( array( 'confirm' => true ) );		
		$this->autoRender = false;	
		$uid = $this->Session->read('uid');
			
		if ( !empty( $this->request->data['id'] ) ) // edit blog
		{
			// check edit permission			
			$blog = $this->Blog->findById( $this->request->data['id'] );
			$this->_checkPermission( array( 'admins' => array( $blog['User']['id'] ) ) );
			$this->Blog->id = $this->request->data['id'];
		}
		else
		{
			$this->request->data['user_id'] = $uid;
		}
		
		$this->request->data['body'] = str_replace( '../', '/', $this->request->data['body'] );

		$this->Blog->set( $this->request->data );
		$this->_validateData( $this->Blog );
			
		if ( $this->Blog->save() )
		{		
			if ( empty( $this->request->data['id'] ) ) // add blog
			{
				// insert into activity feed
				if ( $this->request->data['privacy'] != PRIVACY_ME )
				{
					$this->loadModel( 'Activity' );					
					$this->Activity->save( array( 'type' 	  => APP_USER,
												  'action'    => 'blog_create',
												  'user_id'   => $uid,
												  'item_type' => APP_BLOG,
												  'item_id'   => $this->Blog->id,
												  'query'	  => 1,
												  'privacy'	  => $this->request->data['privacy'],
												  'params'	  => 'item'
										) );
				}					
			}	

			$this->loadModel( 'Tag' );
			$this->Tag->saveTags( $this->request->data['tags'], $this->Blog->id, APP_BLOG );
			
			$response['result'] = 1;
            $response['id'] = $this->Blog->id;
            echo json_encode($response);
		}
	}

	public function view($id = null)
	{
		$id = intval($id); 
		$blog = $this->Blog->findById($id);
		$this->_checkExistence( $blog );
        $this->_checkPermission( array('aco' => 'blog_view') );    
        
		$uid = $this->Session->read('uid');
		
		$areFriends = false;
		if ( !empty($uid) ) //  check if user is a friend
		{
			$this->loadModel( 'Friend' );
			$areFriends = $this->Friend->areFriends( $uid, $blog['User']['id'] );
		}
		
		$this->_checkPrivacy( $blog['Blog']['privacy'], $blog['User']['id'], $areFriends );
		
		$this->loadModel( 'Tag' );
		$tags = $this->Tag->getContentTags( $id, APP_BLOG );
		
		$this->loadModel( 'Comment' );		
		$comments = $this->Comment->getComments( $id, APP_BLOG );
		
		$this->loadModel( 'Like' );
        $likes = $this->Like->getLikes( $id, APP_BLOG, 5 );
		
		// get comment likes
		if ( !empty( $uid ) )
		{							
			$comment_likes = $this->Like->getCommentLikes( $comments, $uid );
			$this->set('comment_likes', $comment_likes);
			
			$like = $this->Like->getUserLike( $id, $uid, APP_BLOG );
			$this->set('like', $like);
		}
		
		$other_entries = $this->Blog->find( 'all', array( 'conditions' => array( 'Blog.user_id' => $blog['Blog']['user_id'], 
																				 'Blog.id <> ' . $id
																				), 
														  'order' => 'Blog.id desc', 
														  'limit' => 5 
										) );		
		
		$og = array('type' => 'blog');
		
		$this->set('og', $og);
		$this->set('blog', $blog);
		$this->set('likes', $likes);
		$this->set('other_entries', $other_entries);
		$this->set('comments', $comments);
        $this->set('likes', $likes);
		$this->set('tags', $tags);
		$this->set('areFriends', $areFriends);
		$this->set('more_comments', '/comments/ajax_browse/blog/' . $id . '/page:2');
		$this->set('title_for_layout', $blog['Blog']['title']);
		$this->set('admins', array($blog['Blog']['user_id']));
		$this->set('desc_for_layout', $blog['Blog']['body']);
		$this->set('og', DS.$blog['User']['photo']);
	}
	
	/*
	 * Delete blog
	 * @param int $id - blog id to delete
	 */
	public function do_delete($id = null)
	{
		$id = intval($id);
		$blog = $this->Blog->findById($id);
		$this->_checkExistence( $blog );
		$this->_checkPermission( array( 'admins' => array( $blog['User']['id'] ) ) );
			
		$this->Blog->deleteBlog( $id );
		
		$this->Session->setFlash( __('Entry has been deleted') );
		$this->redirect( '/blogs' );
	} 

	public function admin_index()
	{
		if ( !empty( $this->request->data['keyword'] ) )
			$this->redirect( '/admin/blogs/index/keyword:' . $this->request->data['keyword'] );
			
		$cond = array();
		if ( !empty( $this->request->named['keyword'] ) )
			$cond['MATCH(Blog.title) AGAINST(? IN BOOLEAN MODE)'] = $this->request->named['keyword'];
			
		$blogs = $this->paginate( 'Blog', $cond );	
		
		$this->set('blogs', $blogs);
		$this->set('title_for_layout', 'Blogs Manager');
	}
	
	public function admin_delete()
	{
		$this->_checkPermission(array('super_admin' => 1));
		
		if ( !empty( $_POST['blogs'] ) )
		{					
			foreach ( $_POST['blogs'] as $blog_id )
				$this->Blog->deleteBlog( $blog_id );	

			$this->Session->setFlash( 'Entries deleted' );				
		}
		
		$this->redirect( $this->referer() );
	}

}

?>
