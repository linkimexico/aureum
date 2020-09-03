<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class PhotosController extends AppController {
	
	public function index($cat_id = null)
	{
		$this->loadModel( 'Album' );
		$this->loadModel( 'Tag' );
		$this->loadModel( 'Category' );
		
        $cat_id = intval($cat_id); 
		$jsnsocial_setting = $this->_getSettings(); 
        
		$categories    = $this->Category->getCategories( APP_ALBUM );		
		$tags = $this->Tag->getTags( APP_ALBUM, $jsnsocial_setting['popular_interval'] );
		
		if ( !empty( $cat_id ) )
			$albums  = $this->Album->getAlbums('category', $cat_id);
		else
			$albums = $this->Album->getAlbums();

		$this->set('categories', $categories);
		$this->set('tags', $tags);
		$this->set('albums', $albums);
        $this->set('cat_id', $cat_id);
		$this->set('title_for_layout', __('Photos'));
	}
	
	/*
	 * Browse photos based on $type
	 * @param string $type - possible value: album, group
	 * @param mixed $target_id - could be album_id or group_id
	 */
	public function ajax_browse( $type = null, $target_id = null )
	{
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
		$uid  = $this->Session->read('uid');	
		
		switch ( $type )
		{
			case 'album':
				// don't check album's privacy for now to save queries
				// photo detail page will handle privacy checking
				
				break;
				
			case 'group':
				// check permission if group is private
				$this->loadModel('Group');
				$group = $this->Group->findById( $target_id );
				
				$this->loadModel('GroupUser');
				$is_member = $this->GroupUser->isMember( $uid, $target_id );
				
				if ( $group['Group']['type'] == PRIVACY_PRIVATE )
				{
					$cuser = $this->_getUser();
					
					if ( !$cuser['Role']['is_admin'] && !$is_member )
						return;
				}
				
				$this->set('is_member', $is_member);
				
				break;
		}		
		
		$photos = $this->Photo->getPhotos( $type, $target_id, $page );
		
		$this->set('photos', $photos);
		$this->set('target_id', $target_id);	
		$this->set('page', $page);	
        $this->set('type', $type);  
		$this->set('more_url', '/photos/ajax_browse/' . h($type) . '/' . intval($target_id) . '/page:' . ( $page + 1 ) ) ;
		
		if ( $page == 1 && $type == APP_GROUP )
			$this->render('/Elements/ajax/group_photo');
		else
			$this->render('/Elements/lists/photos_list');
	}

	public function upload( $aid = null )
	{
		$this->_checkPermission( array( 'confirm' => true ) );
		$this->loadModel( 'Album' );
				
		$uid = $this->Session->read('uid');

		$albums = $this->Album->find( 'list', array( 'conditions' => array( 'Album.user_id' => $uid, 'Album.photo_count <= ' . MAX_PHOTOS, 'Album.type' => '' ) ) );
		$this->set('albums', $albums);

		$this->set('aid', $aid);
		$this->set('title_for_layout', 'Upload Photos');
	}

	public function view($id = null)
	{
		$id = intval($id);
		$photo = $this->Photo->findById($id);
		$this->_checkExistence( $photo );
        $this->_checkPermission( array('aco' => 'photo_view') );    
		
		switch ( $photo['Photo']['type'] )
		{
			case APP_ALBUM:
				$this->_checkPrivacy( $photo['Album']['privacy'], $photo['User']['id'] );
				$title = $photo['Album']['title'];
				$description = $photo['Album']['description'];
                $photos = $this->Photo->getPhotos(APP_ALBUM, $photo['Photo']['target_id'], 1);
                
				break;
				
			case APP_GROUP:
				$title = __('Photos of %s', $photo['Group']['name']);
				$description = $photo['Group']['description'];
                $photos = $this->Photo->getPhotos(APP_GROUP, $photo['Photo']['target_id'], 1);
                
				break;
		}
		
		$this->_getPhotoDetail( $photo );
		
		$this->loadModel( 'Friend' );
		$uid = $this->Session->read('uid');
		$friends = $this->Friend->getFriendsList( $uid );
        
		$type = $photo['Photo']['type'];
        $target_id = $photo['Photo']['target_id'];
        
		if ( !empty( $this->request->named['uid'] ) )
		{
			$this->loadModel( 'User' );
			$user = $this->User->findById( $this->request->named['uid'] );
			$this->set('user', $user);
            
            $this->loadModel('PhotoTag');
            $photos = $this->PhotoTag->getPhotos($this->request->named['uid']);
            
            $type = APP_USER;
            $target_id = $this->request->named['uid'];
		}	
		
		$can_tag = false;
		if ( $uid && ( $uid == $photo['User']['id'] || $this->Friend->areFriends( $uid, $photo['User']['id'] ) ) )
			$can_tag = true;
              
        $this->set('photos', $photos);				
		$this->set('photo', $photo);
        $this->set('type', $type);
        $this->set('target_id', $target_id);
		$this->set('can_tag', $can_tag);
		$this->set('friends', $friends);
        $this->set('no_right_column', true);
		$this->set('title_for_layout', $title);
		$this->set('desc_for_layout', $description);
		if(!empty($photo['Photo']['path'])) $this->set('og', $this->request->webroot.$photo['Photo']['path']);
    }

	public function ajax_view( $id = null, $mode = null )
	{
		$id = intval($id);
		$photo = $this->Photo->findById($id);
		$this->_checkExistence( $photo );
        $this->_checkPermission( array('aco' => 'photo_view') );    
		
		$uid = $this->Session->read('uid');
		$this->loadModel( 'Friend' );
		
		$this->_getPhotoDetail( $photo, $mode );		
		$this->set('photo', $photo);
        
        $can_tag = false;
        if ( $uid && ( $uid == $photo['User']['id'] || $this->Friend->areFriends( $uid, $photo['User']['id'] ) ) )
            $can_tag = true;
        
        $this->set('can_tag', $can_tag);		
		$this->render('/Elements/ajax/photo_detail');
	}
	
	private function _getPhotoDetail( $photo, $mode = null )
	{
		$uid 	 = $this->Session->read('uid');
		$tag_uid = 0;

		if ( !empty( $this->request->named['uid'] ) ) // tagged photos
		{
			$this->loadModel('PhotoTag');
			$photo_tag = $this->PhotoTag->find( 'first', array( 'conditions' => array( 'photo_id' => $photo['Photo']['id'],
																					   'PhotoTag.user_id' => $this->request->named['uid'] )
			) );
			
			$neighbors = $this->PhotoTag->find( 'neighbors', array(  'field' => 'id', 
																	 'value' => $photo_tag['PhotoTag']['id'], 
																	 'conditions' => array( 'PhotoTag.user_id' => $this->request->named['uid']
											)	)	);
											
			$tag_uid = $this->request->named['uid'];
		}
		else
		{
			$neighbors = $this->Photo->find( 'neighbors', array( 'field' => 'id', 
																 'value' => $photo['Photo']['id'], 
																 'conditions' => array( 'Photo.type' => $photo['Photo']['type'], 
																 						'target_id' => $photo['Photo']['target_id']
										)	)	);
		}
				
		$this->loadModel( 'Comment' );
		$this->loadModel( 'Like' );
		
		$comments = $this->Comment->getComments( $photo['Photo']['id'], APP_PHOTO );
		$comment_count = $this->Comment->getCommentsCount( $photo['Photo']['id'], APP_PHOTO );
		
		// get comment likes
		if ( !empty( $uid ) )
		{								
			$comment_likes = $this->Like->getCommentLikes( $comments, $uid );
			$this->set('comment_likes', $comment_likes);
            
            $like = $this->Like->getUserLike( $photo['Photo']['id'], $uid, APP_PHOTO );
            $this->set('like', $like);
		}		
		
		$likes = $this->Like->getLikes( $photo['Photo']['id'], APP_PHOTO, 5 );
		
		$this->loadModel( 'PhotoTag' );
		$photo_tags = $this->PhotoTag->findAllByPhotoId( $photo['Photo']['id'] );
		
		// check to see if user can delete photo
		$admins = array($photo['Photo']['user_id']);
		
		if ( $photo['Photo']['type'] == APP_GROUP ) // if it's a group photo, add group admins to the admins array
		{
			// get group admins
			$this->loadModel('GroupUser');
			
			$is_member = $this->GroupUser->isMember( $uid, $photo['Photo']['target_id'] );	
			$this->set('is_member', $is_member);
			
			$group_admins = $this->GroupUser->getUsersList( $photo['Photo']['target_id'], GROUP_USER_ADMIN );
			$admins = array_merge( $admins, $group_admins );
		}

		$this->set('likes', $likes);
		$this->set('photo_tags', $photo_tags);
		$this->set('comments', $comments);
		$this->set('comment_count', $comment_count);
		$this->set('neighbors', $neighbors);
		$this->set('admins', $admins);
		$this->set('tag_uid', $tag_uid);
        
        $this->set('more_comments', '/comments/ajax_browse/photo/' . $photo['Photo']['id'] . '/page:2');
	}

	public function ajax_upload($type = null, $target_id = null)
	{
		$target_id = intval($target_id);
		$this->_checkPermission( array('aco' => 'photo_upload') );    
		$this->set('target_id', $target_id);
		$this->set('type', $type);
	}
	
	public function do_activity( $type )
	{
		$this->_checkPermission();
		$uid = $this->Session->read('uid');
		
		if ( !empty( $this->request->data['new_photos'] ) )
		{		
			$new_photos = explode( ',', $this->request->data['new_photos'] );
            
            $this->loadModel( 'Activity' );
				
			switch ( $type )
			{
				case APP_ALBUM:			
					$this->loadModel( 'Album' );
					$album = $this->Album->findById( $this->request->data['target_id'] );
					
					$text = '<a href="' . $this->request->base . '/albums/view/' . $album['Album']['id'] . '">' . h($album['Album']['title']) . '</a>';
					$url = '/albums/edit/' . $this->request->data['target_id'];
                    
                    $activity = $this->Activity->getItemActivity( APP_PHOTO, $this->request->data['target_id'] );
                    
                    if ( !empty( $activity ) ) // update the existing one
                    {
                        $this->Activity->id = $activity['Activity']['id'];
                        $this->Activity->save( array( 'items' => $this->request->data['new_photos'], 'privacy' => $album['Album']['privacy'] ) );
                    }
                    else // insert new
                        $this->Activity->save( array( 'type'      => APP_USER,
                                                      'action'    => 'photos_add',
                                                      'user_id'   => $uid,
                                                      'params'    => $text,
                                                      'items'     => $this->request->data['new_photos'],
                                                      'item_type' => APP_PHOTO,
                                                      'item_id'   => $this->request->data['target_id'],
                                                      'privacy'   => $album['Album']['privacy'],
                                                      'query'     => 1,
                                                      'params'    => 'item'
                                            ) );
					
					break;
			
				case APP_GROUP:
					$url = '/groups/view/' . $this->request->data['target_id'];
					$privacy = PRIVACY_EVERYONE;
					
					$this->loadModel('Group');
					$group = $this->Group->findById( $this->request->data['target_id'] );
					
					if ( $group['Group']['type'] == PRIVACY_PRIVATE )
						$privacy = PRIVACY_ME;
                    
                    $this->Activity->save( array( 'type'      => APP_GROUP,
                                                  'target_id' => $this->request->data['target_id'],
                                                  'action'    => 'photos_add',
                                                  'user_id'   => $uid,
                                                  'items'     => $this->request->data['new_photos'],
                                                  'item_type' => APP_PHOTO,
                                                  'privacy'   => $privacy,
                                                  'query'     => 1
                                        ) );
					
					break;
			}
		}
							
		$this->redirect( $url );
	}

	public function ajax_tag()
	{
		$this->autoRender = false;
		$this->_checkPermission( array( 'confirm' => true ) );
		
		$uid   = $this->Session->read('uid');
		$this->loadModel( 'PhotoTag' );
		
		$user_id  = $this->request->data['uid'];
		$photo_id = $this->request->data['photo_id'];
		
		// if tagging a member then check if that member is already tagged in this photo
		if ( !empty( $user_id ) )
			$tag = $this->PhotoTag->find( 'first', array( 'conditions' => array( 'photo_id' => $photo_id, 'PhotoTag.user_id' => $user_id ) ) );
					
		if ( empty( $tag ) )
		{
			$this->PhotoTag->save( array( 'photo_id'  => $photo_id,
										  'user_id'   => $user_id,
										  'tagger_id' => $uid,
										  'value' 	  => $this->request->data['value'],
										  'style' 	  => $this->request->data['style']
								) 	);
										
			if ( $user_id )
			{
				// insert into activity
				$this->loadModel( 'Activity' );
				$activity = $this->Activity->getRecentActivity( 'photos_tag', $user_id );	
												 
				if ( !empty( $activity ) )
				{
					$photo_ids = explode( ',', $activity['Activity']['items'] );
					$photo_ids[] = $photo_id;
					
					$this->Activity->id = $activity['Activity']['id'];
					$this->Activity->save( array( 'items' => implode( ',', $photo_ids ),
												  'item_id' => 0
					) );
				}
				else
				{
					$this->Activity->save( array( 'type' 	  => APP_USER,
												  'action' 	  => 'photos_tag',
												  'user_id'   => $user_id,
												  'item_type' => APP_PHOTO,
												  'items'     => $photo_id,
												  'query'	  => 1,
												  'params'    => 'no-comments'
										) );
				}
									
				if ( $user_id != $uid )
				{
					// add notification
					$this->loadModel( 'Notification' );
					$this->Notification->record( array( 'recipients'  => $user_id,
														'sender_id'   => $uid,
														'action' 	  => 'photo_tag',
														'url' 		  => '/photos/view/' . $photo_id . '#content'	
												) );
				}
			}
			
            $response['result'] = 1;
            $response['id'] = $this->PhotoTag->id;
		}
		else
        {
            $response['result'] = 0;
            $response['message'] = __('Duplicated tag!');
        }

        echo json_encode($response);
	}
	
	public function ajax_remove_tag()
	{
		$this->autoRender = false;		
		$this->_checkPermission( array( 'confirm' => true ) );
		$uid = $this->Session->read('uid');
		
		$this->loadModel( 'PhotoTag' );
		$tag = $this->PhotoTag->findById( $this->request->data['tag_id'] );
		
		// tagger, user was tagged and photo author can delete tag
		$admins = array( $tag['PhotoTag']['user_id'], $tag['PhotoTag']['tagger_id'], $tag['Photo']['user_id'] );
		
		$this->_checkPermission( array( 'admins' => $admins ) );
		$this->PhotoTag->delete( $this->request->data['tag_id'] );
	}
	
	public function ajax_fetch()
	{
		switch ( $this->data['type'] )
		{
			case APP_ALBUM:
                // check the privacy of album
                $this->loadModel('Album');
                $album = $this->Album->findById($this->data['target_id']);
                
                $uid = $this->Session->read('uid');     
                $this->_checkPrivacy( $album['Album']['privacy'], $album['User']['id'] );
                
                $photos = $this->Photo->getPhotos(APP_ALBUM, $this->data['target_id'], $this->data['page']);
				
				break;
                
            case APP_GROUP:
                // @todo: check the type of group
                
                $photos = $this->Photo->getPhotos(APP_GROUP, $this->data['target_id'], $this->data['page']);
                
                break;
                
            case APP_USER:
                $this->loadModel('PhotoTag');                
                $photos = $this->PhotoTag->getPhotos($this->data['target_id'], $this->data['page']);
                
                break;
		}
		
		$this->set('photos', $photos);
        $this->render('/Elements/ajax/photo_thumbs');
	}
    
    public function ajax_friends_list()
    {
        $this->_checkPermission();
        $uid = $this->Session->read('uid');
        
        $this->loadModel('Friend');
        $friends = $this->Friend->getFriendsList( $uid );
        
        $this->set('friends', $friends);
        $this->render('/Elements/misc/photo_friends_list');
    }
	
	public function ajax_remove()
	{
		$this->autoRender = false;		
		$this->_checkPermission( array( 'confirm' => true ) );
		
		$photo = $this->Photo->findById( $this->request->data['photo_id'] );		
		$admins = array($photo['Photo']['user_id']);
		
		if ( $photo['Photo']['type'] == APP_GROUP ) // if it's a group photo, add group admins to the admins array
		{
			// get group admins
			$this->loadModel('GroupUser');
			
			$group_admins = $this->GroupUser->getUsersList( $photo['Photo']['target_id'], GROUP_USER_ADMIN );
			$admins = array_merge( $admins, $group_admins );
		}
		
		// make sure user can delete photo
		$this->_checkPermission( array( 'admins' => $admins ) );
		
		// permission ok, delete photo now
		$this->Photo->deletePhoto( $photo );
	}
}

