<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;



/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class UploadController extends AppController 
{
	public $uses = array();

	public function beforeFilter()
	{
		$this->autoRender = false;
	}

	public function thumb()
	{
		$uid = $this->Session->read('uid');
        
		if (!$uid || ( !$_POST['x'] && !$_POST['y'] && !$_POST['w'] && !$_POST['y'] ))
			return;

        $this->loadModel( 'User' );
        $user = $this->User->findById($uid);
        
        if ( empty( $user['User']['photo'] ) )
            return;

		$path = WWW_ROOT . 'uploads' . DS . 'avatars';

		$ext = $this->_getExtension($user['User']['photo']);
		$thumbname = md5(microtime()) . '.' . $ext;

		$thumbloc = $path . DS . $thumbname;

		JsnApp::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

		$thumb = PhpThumbFactory::create($path . DS . $user['User']['photo'], array('jpegQuality' => 100));  	
		$thumb->crop($_POST['x'], $_POST['y'], $_POST['w'], $_POST['h'])->resize(AVATAR_THUMB_WIDTH, AVATAR_THUMB_HEIGHT)->save($thumbloc);		

		// delete old file
		if ($user['User']['avatar'] && file_exists($path . DS . $user['User']['avatar']))
			unlink($path . DS . $user['User']['avatar']);

		// update user pic in db
		$this->User->id = $uid;
		$this->User->save( array( 'avatar' => $thumbname ) );

        $result['thumb'] = $this->request->webroot . 'uploads/avatars/' . $thumbname;
        
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}

    public function thumb_cover()
    {
        $uid = $this->Session->read('uid');
        
        if (!$uid || ( !$_POST['x'] && !$_POST['y'] && !$_POST['w'] && !$_POST['y'] ))
            return;

        $this->loadModel( 'User' );
        $user = $this->User->findById($uid);
        
        if ( empty( $user['User']['cover'] ) )
            return;

        $path = WWW_ROOT . 'uploads' . DS . 'covers';

        $ext = $this->_getExtension($user['User']['cover']);
        $thumbname = md5(microtime()) . '.' . $ext;

        $thumbloc = $path . DS . $thumbname;

        JsnApp::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
        
        $this->loadModel('Photo');        
        $photo = $this->Photo->find( 'first', array( 'conditions' => array(  'Album.type' => 'cover', 
                                                                             'Album.user_id' => $uid ),
                                                     'limit' => 1,
                                                     'order' => 'Photo.id desc'
                                   ) );

        $thumb = PhpThumbFactory::create(WWW_ROOT . DS . $photo['Photo']['path'], array('jpegQuality' => 100));     
        $thumb->crop($_POST['x'], $_POST['y'], $_POST['w'], $_POST['h'])->resize(COVER_WIDTH, COVER_HEIGHT)->save($thumbloc);     

        // delete old file
        if ($user['User']['cover'] && file_exists($path . DS . $user['User']['cover']))
            unlink($path . DS . $user['User']['cover']);

        // update user cover in db
        $this->User->id = $uid;
        $this->User->save( array( 'cover' => $thumbname ) );

        $result['thumb'] = $this->request->webroot . 'uploads/covers/' . $thumbname;
        
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

	public function avatar($save_original = 0)
	{
		$uid = $this->Session->read('uid');

		$response=$this->avatarnew($save_original);
		
		if (!$uid || !$response)
            return;

		$this->loadModel( 'Activity' );
        $activity = $this->Activity->getRecentActivity( 'user_avatar', $uid );  
        
        if ( empty( $activity ) )
        {
            $this->Activity->save( array( 'type' => 'user',
                                          'action' => 'user_avatar',
                                          'user_id' => $uid
                                ) );
        }
	}

	public function avatarnew($save_original = 0)
	{
		$uid = $this->Session->read('uid');
            
        if (!$uid)
            return;
        
        $this->loadModel( 'Album' );

        $album = $this->Album->getUserAlbumByType( $uid, 'profile' );
        $title = __('Profile Pictures');
        
        if ( empty( $album ) )
        {
            $this->Album->save( array( 'user_id' => $uid, 'type' => 'profile', 'title' => $title ), false );
            $album_id = $this->Album->id;
        }
        else
            $album_id = $album['Album']['id'];
        
        // save this picture to album
        $path = WWW_ROOT.'uploads' . DS . 'albums' . DS . $album_id;
        $url = 'uploads/albums/' . $album_id . '/';
        
        $this->_prepareDir($path);        

		require_once(JPATH_SITE.'/components/com_jsn/helpers/helper.php');
        
        if ( !empty( SocialNetwork::$new_avatar ) )
        {
	
			$pathinfo = pathinfo(SocialNetwork::$new_avatar);
	        
			$filename = md5(uniqid());
	        $ext = @$pathinfo['extension'];		// hide notices if extension is empty

	        $ext = ($ext == '') ? $ext : '.' . $ext;
			
			$filename=$filename . $ext;
			
			copy(JPATH_SITE . DS . SocialNetwork::$new_avatar , $path . DS . $filename);
	
            JsnApp::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
               
            $photo = PhpThumbFactory::create($path . DS . $filename);         
            $this->_rotateImage($photo, $path . DS . $filename);            
                
            // resize image            
            $setting = $this->_getSettings();
            
            if ( $setting['save_original_image'] )
            {
                $original_photo = $url . $filename;
                $medium_photo = 'm_' . $filename;
            }
            else
            {
                $original_photo = '';    
                $medium_photo = $filename;
            }
            
            /* Add to profile photo album*/
           
            //$photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $photo->resize(PHOTO_WIDTH, PHOTO_HEIGHT)->save($path . DS . $medium_photo);
            
            $photo = PhpThumbFactory::create($path . DS . $medium_photo);
            $photo->adaptiveResize(PHOTO_THUMB_WIDTH, PHOTO_THUMB_HEIGHT)->save($path . DS . 't_' . $filename);
            
            // save to db
            $this->loadModel( 'Photo' );
            $this->Photo->create();
            $this->Photo->set( array('user_id'   => $uid, 
                                     'target_id' => $album_id, 
                                     'type'      => APP_ALBUM, 
                                     'path'      => $url . $medium_photo, 
                                     'thumb'     => $url . 't_' . $filename,
                                     'original'  => $original_photo
            ) );
            $this->Photo->save();
            
            // save album cover
            
            $this->Album->id = $album_id;
            $this->Album->save( array( 'cover' => $url . 't_' . $filename ) );
            
            return true;
        }
        
        return false;
	}

    public function cover()
    {
        $uid = $this->Session->read('uid');
            
        if (!$uid)
            return;
        
        $this->loadModel( 'Album' );

        $album = $this->Album->getUserAlbumByType( $uid, 'cover' );
        $title = __('Cover Pictures');
        
        if ( empty( $album ) )
        {
            $this->Album->save( array( 'user_id' => $uid, 'type' => 'cover', 'title' => $title ), false );
            $album_id = $this->Album->id;
        }
        else
            $album_id = $album['Album']['id'];
        
        // save this picture to album
        $path = WWW_ROOT.'uploads' . DS . 'albums' . DS . $album_id;
        $url  = 'uploads/albums/' . $album_id . '/';
        
        $this->_prepareDir($path);
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
            
        JsnApp::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);
        
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);
        
        if ( !empty( $result['success'] ) )
        {
            // resize image
            JsnApp::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);         
            $this->_rotateImage($photo, $path . DS . $result['filename']);
            
            $setting = $this->_getSettings();
            
            if ( $setting['save_original_image'] )
            {
                $original_photo = $url . $result['filename'];
                $medium_photo = 'm_' . $result['filename'];
            }
            else
            {
                $original_photo = '';    
                $medium_photo = $result['filename'];
            }
            
            /* Add to cover photo album*/
           
            //$photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $photo->resize(PHOTO_WIDTH, PHOTO_HEIGHT)->save($path . DS . $medium_photo);
            
            $photo = PhpThumbFactory::create($path . DS . $medium_photo);
            $photo->adaptiveResize(PHOTO_THUMB_WIDTH, PHOTO_THUMB_HEIGHT)->save($path . DS . 't_' . $result['filename']);
            
            // save to db
            $this->loadModel( 'Photo' );
            $this->Photo->create();
            $this->Photo->set( array('user_id'   => $uid, 
                                     'target_id' => $album_id, 
                                     'type'      => APP_ALBUM, 
                                     'path'      => $url . $medium_photo, 
                                     'thumb'     => $url . 't_' . $result['filename'],
                                     'original'  => $original_photo
            ) );
            $this->Photo->save();
            
            // save album cover
            
            $this->Album->id = $album_id;
            $this->Album->save( array( 'cover' => $url . 't_' . $result['filename'] ) );
            
            /* Create and update cover */
            
            $cover_path       = WWW_ROOT . 'uploads' . DS . 'covers';
            $cover_loc        = $cover_path . DS . $result['filename'];
            
            if (!file_exists( $cover_path ))
            {
                mkdir( $cover_path, 0755, true );
                file_put_contents( WWW_ROOT . $path . DS . 'index.html', '' );
            }

            // resize image
            $cover = PhpThumbFactory::create($path . DS . $medium_photo, array('jpegQuality' => PHOTO_QUALITY));
            $cover->adaptiveResize(COVER_WIDTH, COVER_HEIGHT)->save($cover_loc);
            
            $this->loadModel('User');
            $user = $this->User->findById($uid);

            // delete old files
            $this->User->removeCoverFile( $user['User'] );            

            // update user cover pic in db
            $this->User->id = $uid;
            $this->User->save( array('cover' => $result['filename']) );     
            
            $result['cover'] = $this->request->webroot .  'uploads/covers/' . $result['filename'];
            $result['photo'] = $this->request->webroot .  $url . $medium_photo;
        }
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    public function wall()
    {
        $uid = $this->Session->read('uid');
            
        if (!$uid)
            return;
        
        $this->loadModel( 'Album' );

        $album = $this->Album->getUserAlbumByType( $uid, 'newsfeed' );
        $title = __('Newsfeed Photos');
        
        if ( empty( $album ) )
        {
            $this->Album->save( array( 'user_id' => $uid, 'type' => 'newsfeed', 'title' => $title ), false );
            $album_id = $this->Album->id;
        }
        else
            $album_id = $album['Album']['id'];
        
        // save this picture to album
        $path = WWW_ROOT.'uploads' . DS . 'albums' . DS . $album_id;
        $url  = 'uploads/albums/' . $album_id . '/';
        
        $this->_prepareDir($path);
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
            
        JsnApp::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);
        
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);        
        
        if ( !empty( $result['success'] ) )
        {
            // resize image
            JsnApp::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);         
            $this->_rotateImage($photo, $path . DS . $result['filename']);
            
            $setting = $this->_getSettings();
            
            if ( $setting['save_original_image'] )
            {
                $original_photo = $url . $result['filename'];
                $medium_photo = 'm_' . $result['filename'];
            }
            else
            {
                $original_photo = '';    
                $medium_photo = $result['filename'];
            }
            
            /* Add to cover photo album*/
           
            //$photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $photo->resize(PHOTO_WIDTH, PHOTO_HEIGHT)->save($path . DS . $medium_photo);
            
            $photo = PhpThumbFactory::create($path . DS . $medium_photo);
            $photo->adaptiveResize(PHOTO_THUMB_WIDTH, PHOTO_THUMB_HEIGHT)->save($path . DS . 't_' . $result['filename']);
            
            // save to db
            $this->loadModel( 'Photo' );
            $this->Photo->create();
            $this->Photo->set( array('user_id'   => $uid, 
                                     'target_id' => $album_id, 
                                     'type'      => APP_ALBUM, 
                                     'path'      => $url . $medium_photo, 
                                     'thumb'     => $url . 't_' . $result['filename'],
                                     'original'  => $original_photo
            ) );
            $this->Photo->save();
            
            // save album cover
            
            $this->Album->id = $album_id;
            $this->Album->save( array( 'cover' => $url . 't_' . $result['filename'] ) );            
            
            $result['photo_id'] = $this->Photo->id;
            $result['photo'] = $url . 't_' . $result['filename'];
        }
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

	public function photos($type, $target_id, $save_original = 0)
	{
		$uid = $this->Session->read('uid');
            
		if (!$type || !$target_id || !$uid)
			return;
        
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
            
        JsnApp::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);
        
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $path = WWW_ROOT.'uploads' . DS . Inflector::pluralize($type) . DS . $target_id;
        $url  = 'uploads/' . Inflector::pluralize($type) . '/' . $target_id. '/';
        $this->_prepareDir($path);
        
        $result = $uploader->handleUpload($path);
        
        if ( !empty( $result['success'] ) )
        {
            // resize image
            JsnApp::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);         
            $this->_rotateImage($photo, $path . DS . $result['filename']);
            
            if ( $save_original )
            {
                $original_photo = $url . $result['filename'];
                $medium_photo = 'm_' . $result['filename'];
            }
            else
            {
                $original_photo = '';    
                $medium_photo = $result['filename'];
            }
            
            //$photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $photo->resize(PHOTO_WIDTH, PHOTO_HEIGHT)->save($path . DS . $medium_photo);
            
            $photo = PhpThumbFactory::create($path . DS . $medium_photo);
            $photo->adaptiveResize(PHOTO_THUMB_WIDTH, PHOTO_THUMB_HEIGHT)->save($path . DS . 't_' . $result['filename']);
            
            // save to db
            $this->loadModel( 'Photo' );
            $this->Photo->create();
            $this->Photo->set( array('user_id'   => $uid, 
                                     'target_id' => $target_id, 
                                     'type'      => $type, 
                                     'path'      => $url . $medium_photo, 
                                     'thumb'     => $url . 't_' . $result['filename'],
                                     'original'  => $original_photo
            ) );
            $this->Photo->save();
            
            $result['photo_id'] = $this->Photo->id;
            $result['thumb'] = $this->request->webroot .  $url . 't_' . $result['filename'];
        }
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}

	public function attachments($plugin_id, $target_id = 0)
	{
		$uid = $this->Session->read('uid');
            
		if (!$plugin_id || !$uid)
			return;
        
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'zip', 'txt', 'pdf');
            
        JsnApp::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);
        
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $path = WWW_ROOT.'uploads' . DS . 'attachments';
        $url  = 'uploads/attachments';
		
		$original_filename = $this->request->data['qqfilename'];//$original_filename = $this->request->query['qqfile'];
		$ext = $this->_getExtension($original_filename);
        
        $result = $uploader->handleUpload($path);
        
        if ( !empty( $result['success'] ) )
        {
            if ( in_array( strtolower($ext), array('jpg', 'jpeg', 'png', 'gif') ) )
			{
				// resize image
	            JsnApp::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
                
                $photo = PhpThumbFactory::create($path . DS . $result['filename']);         
                $this->_rotateImage($photo, $path . DS . $result['filename']);
	            
	            //$photo = PhpThumbFactory::create($path . DS . $result['filename']);
	            $photo->resize(PHOTO_WIDTH, PHOTO_HEIGHT)->save($path . DS . $result['filename']);
	            
	            $photo = PhpThumbFactory::create($path . DS . $result['filename']);
	            $photo->adaptiveResize(PHOTO_THUMB_WIDTH, PHOTO_THUMB_HEIGHT)->save($path . DS . 't_' . $result['filename']);
			}            
            
            // save to db
            $this->loadModel( 'Attachment' );
            $this->Attachment->create();
            $this->Attachment->set( array('user_id'  		  => $uid, 
	                                      'target_id' 		  => $target_id, 
	                                      'plugin_id'      	  => $plugin_id, 
	                                      'filename'      	  => $result['filename'], 
	                                      'original_filename' => $original_filename,
	                                      'extension'  		  => $ext
            ) );
            $this->Attachment->save();
            
            $result['attachment_id'] = $this->Attachment->id;
        }
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}
	
	public function images()
	{
		$error = false;

		$allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
            
        JsnApp::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);
        
        $path = WWW_ROOT.'uploads' . DS . 'images';
        
        $result = $uploader->handleUpload($path);
        
        if ( !empty( $result['success'] ) )
        {
            // resize image
            JsnApp::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);         
            $this->_rotateImage($photo, $path . DS . $result['filename']);
            
            //$photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $photo->resize(PHOTO_WIDTH, PHOTO_HEIGHT)->save($path . DS . $result['filename']);
            
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $photo->resize(IMAGE_WIDTH, IMAGE_HEIGHT)->save($path . DS . 't_' . $result['filename']);
		}

		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}

	public function event($id = null)
	{
		$uid = $this->Session->read('uid');
            
        if (!$uid)
            return;        
        
        $this->loadModel( 'Event' );
        
        if (!$id)
        {
            $path = WWW_ROOT.'uploads' . DS . 'tmp';
            $url = 'uploads/tmp/';
        }
        else
        {           
            $event = $this->Event->findById( $id );
            $this->_checkExistence($event);
            
            $path = WWW_ROOT.'uploads' . DS . 'events';
            $url = 'uploads/events/';
        }
        
        $this->_prepareDir($path);        
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
            
        JsnApp::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);
        
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);
        
        if ( !empty( $result['success'] ) )
        {
            // resize image
            JsnApp::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);         
            $this->_rotateImage($photo, $path . DS . $result['filename']);
           
            //$photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $photo->resize(GROUP_AVATAR_WIDTH, GROUP_AVATAR_HEIGHT)->save($path . DS . $result['filename']);
            
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $photo->adaptiveResize(AVATAR_THUMB_WIDTH, AVATAR_THUMB_HEIGHT)->save($path . DS . 't_' . $result['filename']);
            
            if ($id)
            {
                // save to db
                $this->Event->id = $id;
                $this->Event->set( array('photo' => $result['filename']) );
                $this->Event->save();
                
                // delete old files
                if ($event['Event']['photo'] && file_exists($path . DS . $event['Event']['photo']))
                {
                    unlink($path . DS . $event['Event']['photo']);
                    unlink($path . DS . 't_' . $event['Event']['photo']);
                }
            }
            
            $result['avatar'] = $this->request->webroot . $url . $result['filename'];
            $result['filename'] = $result['filename'];
        }
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}

	public function group($id = null)
	{
		$uid = $this->Session->read('uid');
            
        if (!$uid)
            return;        
        
        $this->loadModel( 'Group' );
        
        if (!$id)
        {
            $path = WWW_ROOT.'uploads' . DS . 'tmp';
            $url = 'uploads/tmp/';
        }
        else
        {           
            $group = $this->Group->findById( $id );
            $this->_checkExistence($group);
            
            $path = WWW_ROOT.'uploads' . DS . 'groups';
            $url = 'uploads/groups/';
        }
        
        $this->_prepareDir($path);        
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
            
        JsnApp::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);
        
        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);
        
        if ( !empty( $result['success'] ) )
        {
            // resize image
            JsnApp::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);         
            $this->_rotateImage($photo, $path . DS . $result['filename']);
           
            //$photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $photo->resize(GROUP_AVATAR_WIDTH, GROUP_AVATAR_HEIGHT)->save($path . DS . $result['filename']);
            
            $photo = PhpThumbFactory::create($path . DS . $result['filename']);
            $photo->adaptiveResize(AVATAR_THUMB_WIDTH, AVATAR_THUMB_HEIGHT)->save($path . DS . 't_' . $result['filename']);
            
            if ($id)
            {
                // save to db
                $this->Group->id = $id;
                $this->Group->set( array('photo' => $result['filename']) );
                $this->Group->save();
                
                // delete old files
                if ($group['Group']['photo'] && file_exists($path . DS . $group['Group']['photo']))
                {
                    unlink($path . DS . $group['Group']['photo']);
                    unlink($path . DS . 't_' . $group['Group']['photo']);
                }
            }
            
            $result['avatar'] = $this->request->webroot . $url . $result['filename'];
            $result['filename'] = $result['filename'];
        }
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}

	public function _getExtension($filename = null)
	{
		$tmp = explode('.', $filename);
		$re = array_pop($tmp);
		return $re;
	}
    
    private function _prepareDir($path)
    {
        //$path = WWW_ROOT . $path;

        if (!file_exists($path))
        {
            mkdir($path, 0755, true);
            file_put_contents( $path . DS . 'index.html', '' );
        }
    }
    
    private function _rotateImage(&$photo, $path)
    { 
        // rotate image if necessary
        $exif = exif_read_data($path);
        
        if (!empty($exif['Orientation']))        
            switch ($exif['Orientation']) 
            {
                case 8:
                    $photo->rotateImageNDegrees(90)->save($path);
                    break;
                case 3:
                    $photo->rotateImageNDegrees(180)->save($path);
                    break;
                case 6:
                    $photo->rotateImageNDegrees(-90)->save($path);
                    break;
            }
    }
}

?>
