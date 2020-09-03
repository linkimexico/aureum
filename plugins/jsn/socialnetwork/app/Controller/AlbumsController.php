<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class AlbumsController extends AppController {
		
	public $paginate = array( 'limit' => RESULTS_LIMIT );

	public function index()
	{
		$this->redirect( '/photos' );
	}
	
	/*
	 * Browse albums based on $type
	 * @param string $type - possible value: all (default), my, home friends, search
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
		}	
		
		$albums = $this->Album->getAlbums( $type, $param, $page );
		$this->set('albums', $albums);
		$this->set('more_url', '/albums/ajax_browse/' . h($url) . '/page:' . ( $page + 1 ) );
		
		if ( $page == 1 && $type == 'home' )
			$this->render('/Elements/ajax/home_album');
		else
			$this->render('/Elements/lists/albums_list');		
	}
	
	public function ajax_save()
	{
		$this->_checkPermission( array( 'confirm' => true ) );						
		$this->autoRender = false;
		$uid = $this->Session->read('uid');

		if (!empty($this->request->data['id']))
		{
			// check edit permission			
			$album = $this->Album->findById( $this->request->data['id'] );
			$this->_checkPermission( array( 'admins' => array( $album['User']['id'] ) ) );
			$this->Album->id = $this->request->data['id'];
		}
		else
			$this->request->data['user_id'] = $uid;

		$this->Album->set( $this->request->data );
		$this->_validateData( $this->Album );
			
		if ( $this->Album->save() ) // successfully saved	
		{
			// save tags
			$this->loadModel( 'Tag' );
			$this->Tag->saveTags($this->request->data['tags'], $this->Album->id, APP_ALBUM);
			
            $response['result'] = 1;
            $response['id'] = $this->Album->id;
            echo json_encode($response);
		}
	}

	public function ajax_create( $aid = 0 )
	{
		$this->_checkPermission( array( 'confirm' => true ) );
        $this->_checkPermission( array('aco' => 'album_create') );    
		
		if ( !empty( $aid ) )
		{
			$album = $this->Album->findById($aid);
			$this->_checkExistence( $album );
            $this->_checkPermission( array( 'admins' => array( $album['User']['id'] ) ) );

			$this->loadModel( 'Tag' );
			$tags = $this->Tag->getContentTags( $aid, APP_ALBUM );
			
			$this->set('tags', $tags);
		}
		else
			$album = $this->Album->initFields();
		
		$this->loadModel('Category');	
        $role_id = $this->_getUserRoleId();	
		$categories = $this->Category->getCategoriesList( APP_ALBUM, $role_id );
		
		$this->set('album', $album);
		$this->set('categories', $categories);
	}

	public function edit( $id = null )
	{
		$id = intval($id);
		$album = $this->Album->findById($id);
		$this->_checkExistence( $album );
        
		$this->_checkPermission( array( 'admins' => array( $album['User']['id'] ) ) );
        $this->_checkPermission( array('aco' => 'album_create') );    

		$this->loadModel( 'Photo' );
		$photos = $this->Photo->getPhotos( APP_ALBUM, $id, null, null );        
		
		if ( isset($this->request->data['cover']) ) // handle form submission
		{
			// update cover
			$this->Album->id = $id;
			$this->Album->save( array( 'cover' => $this->request->data['cover'] ) );

			foreach ($photos as $photo)
			{
				if ( isset( $this->request->data['select_'.$photo['Photo']['id']] ) )
                {
					switch ( $this->request->data['select_photos'] )
                    {
                        case 'delete':
                            $this->Photo->deletePhoto( $photo );
                            break;
                            
                        case 'move':
                            $this->Photo->id = $photo['Photo']['id'];
                            $this->Photo->save(array('target_id' => $this->request->data['album_id']));
                            break;                            
                    }
                    
                    if ( $album['Album']['cover'] == $photo['Photo']['thumb'] )
                    {
                        // update cover image
                        $p = $this->Photo->find('first', array('conditions' => array( 'Photo.type' => 'album', 'Photo.target_id' => $id )));
                        $cover = ( !empty($p) ) ? $p['Photo']['thumb'] : '';
                        
                        $this->Album->id = $id;
                        $this->Album->save(array('cover' => $cover));
                    }
                }
				elseif ( $this->request->data['caption_'.$photo['Photo']['id']] )
				{
					// update caption
					$this->Photo->id = $photo['Photo']['id'];
					$this->Photo->save( array( 'caption' => $this->request->data['caption_'.$photo['Photo']['id']] ) );
				}
			}
			
			$this->Session->setFlash( __('Your changes have been saved') );
			$this->redirect( '/albums/view/' . $album['Album']['id'] );
		}
		else
		{
			// if album does not have a cover yet, use the first photo as cover
			if ( !$album['Album']['cover'] && count($photos) > 0 )
			{
				$this->Album->id = $id;
				$this->Album->save( array( 'cover' => $photos[0]['Photo']['thumb'] ) );
                
                $album['Album']['cover'] = $photos[0]['Photo']['thumb'];
			}
            
            $albums = $this->Album->find('list', array('conditions' => array('Album.user_id' => $album['Album']['user_id']), 'fields' => 'Album.title'));

			$this->set('photos', $photos);
			$this->set('album', $album);
            $this->set('albums', $albums);
			$this->set('title_for_layout', __('Edit Album'));
		}		
	}
	
	public function view($id = null)
	{
		$id = intval($id);
		$album = $this->Album->findById($id);
		$this->_checkExistence( $album );
        $this->_checkPermission( array('aco' => 'album_view') );    
		
		$uid = $this->Session->read('uid');		
		$this->_checkPrivacy( $album['Album']['privacy'], $album['User']['id'] );
		
		$this->loadModel( 'Photo' );
		$photos = $this->Photo->getPhotos( APP_ALBUM, $id );

		$this->loadModel( 'Tag' );	
		$tags = $this->Tag->getContentTags( $id, APP_ALBUM );
		
		$this->loadModel( 'Comment' );
		$comments = $this->Comment->getComments( $id, APP_ALBUM );
		$comment_count = $this->Comment->getCommentsCount( $id, APP_ALBUM );
        
        $this->loadModel( 'Like' );
        $likes = $this->Like->getLikes( $id, APP_ALBUM, 5 );
		
		// get comment likes
		if ( !empty( $uid ) )
		{								
			$comment_likes = $this->Like->getCommentLikes( $comments, $uid );
			$this->set('comment_likes', $comment_likes);
			
			$like = $this->Like->getUserLike( $id, $uid, APP_ALBUM );
			$this->set('like', $like);
		}

		$this->set('photos', $photos);
		$this->set('tags', $tags);
		$this->set('comments', $comments);
        $this->set('likes', $likes);
		$this->set('comment_count', $comment_count);
		$this->set('album', $album);
		$this->set('more_comments', '/comments/ajax_browse/album/' . $id . '/page:2');
		$this->set('more_url', '/photos/ajax_browse/album/' . $id . '/page:2');
		$this->set('title_for_layout', $album['Album']['title']);
		$this->set('admins', array($album['Album']['user_id']));
		$this->set('desc_for_layout', $album['Album']['description']);
		if(!empty($album['Album']['cover'])) $this->set('og', $this->request->webroot.$album['Album']['cover']);
	}
	
	/*
	 * Delete album
	 * @param int $id - album id to delete
	 */
	public function do_delete($id = null)
	{
		$id = intval($id);
		$album = $this->Album->findById($id);
		$this->_checkExistence( $album );
		$this->_checkPermission( array( 'admins' => array( $album['User']['id'] ) ) );
		
		$this->Album->deleteAlbum( $id );
		
		$this->Session->setFlash( __('Album has been deleted') );
		$this->redirect( '/photos' );
	} 

	public function admin_index()
	{
		if ( !empty( $this->request->data['keyword'] ) )
			$this->redirect( '/admin/albums/index/keyword:' . $this->request->data['keyword'] );
			
		$cond = array();
		if ( !empty( $this->request->named['keyword'] ) )
			$cond['MATCH(Album.title) AGAINST(? IN BOOLEAN MODE)'] = $this->request->named['keyword'];
			
		$albums = $this->paginate( 'Album', $cond );	
        
        $this->loadModel('Category');
        $categories = $this->Category->getCategoriesList( APP_ALBUM );
		
		$this->set('albums', $albums);
        $this->set('categories', $categories);
		$this->set('title_for_layout', 'Albums Manager');
	}
	
	public function admin_delete()
	{
		$this->_checkPermission(array('super_admin' => 1));
		
		if ( !empty( $_POST['albums'] ) )
		{					
			foreach ( $_POST['albums'] as $album_id )
				$this->Album->deleteAlbum( $album_id );	

			$this->Session->setFlash( 'Albums deleted' );				
		}
		
		$this->redirect( $this->referer() );
	}
    
    public function admin_move()
    {
        if ( !empty( $_POST['albums'] ) && !empty( $this->request->data['category_id'] ) )
        {                   
            foreach ( $_POST['albums'] as $album_id )
            {
                $this->Album->id = $album_id;
                $this->Album->save( array( 'category_id' => $this->request->data['category_id'] ) );
            }

            $this->Session->setFlash( 'Albums moved' );               
        }
        
        $this->redirect( $this->referer() );
    }

}

