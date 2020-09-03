<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class SearchController extends AppController {
    
    public function index( $keyword )
    {
        $keyword = urldecode($keyword);
        
        if ( strlen( $keyword ) < 4 )
        {
            $this->Session->setFlash( __('Search term must have at least 4 characters'), 'default', array( 'class' => 'error-message' ) );
            $this->redirect( '/pages/error' );
        }
            
        $jsnsocial_setting = $this->_getSettings();
        $uid = $this->Session->read('uid'); 
        
        if ( !$jsnsocial_setting['guest_search'] && empty( $uid ) )
            $this->_checkPermission();
        
        $this->loadModel('User');
        $this->loadModel('Album');
        $this->loadModel('Blog');     
        $this->loadModel('Group'); 
        $this->loadModel('Topic');
        $this->loadModel('Video');
        
        /*$params = array( 'User.active' => 1,
                         'MATCH(User.name) AGAINST(? IN BOOLEAN MODE)' => $keyword
        );*/
		$params = array( 'User.active' => 1,
                         'User.name LIKE ?' => '%'.$keyword.'%'
        );
        
        $users = $this->User->getUsers( 1, $params, 4 );
        $albums = $this->Album->getAlbums( 'search', $keyword, 1, 4 );
        $blogs = $this->Blog->getBlogs( 'search', $keyword, 1, 4 );
        $groups = $this->Group->getGroups( 'search', $keyword, 1, 4 );
        $topics = $this->Topic->getTopics( 'search', $keyword, 1, 4 );
        $videos = $this->Video->getVideos( 'search', $keyword, 1, 4 );
        
        $this->set('users', $users);
        $this->set('albums', $albums);
        $this->set('blogs', $blogs);
        $this->set('groups', $groups);
        $this->set('topics', $topics);
        $this->set('videos', $videos);
        $this->set('keyword', $keyword);
        $this->set('title_for_layout', $keyword);
    }
}
