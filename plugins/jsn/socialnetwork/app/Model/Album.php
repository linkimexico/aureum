<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Album extends AppModel {
						 
	public $belongsTo = array( 'User',
							   'Category' => array( 'counterCache' => 'item_count', 
							   						'counterScope' => array( 'Album.privacy' => PRIVACY_EVERYONE, 
							   												 'Category.type' => APP_ALBUM,
							   												 'Album.photo_count > 0' ) ) 
	);
	
	public $hasMany = array( 'Comment' => array( 
											'className' => 'Comment',	
											'foreignKey' => 'target_id',
											'conditions' => array('Comment.type' => APP_ALBUM),						
											'dependent'=> true
										),
							  'Like' => array( 
											'className' => 'Like',	
											'foreignKey' => 'target_id',
											'conditions' => array('Like.type' => APP_ALBUM),						
											'dependent'=> true
										),
							  'Tag' => array( 
											'className' => 'Tag',	
											'foreignKey' => 'target_id',
											'conditions' => array('Tag.type' => APP_ALBUM),						
											'dependent'=> true
										),
							); 
	
	public $order = "Album.id desc";
	
	public $validate = array(	
				'title' => 	array( 	 
					'rule' => 'notEmpty',
					'message' => 'Title is required'
				),
				'category_id' => 	array( 	 
					'rule' => 'notEmpty',
					'message' => 'Category is required'
				),
	);
	
	/*
	 * Get albums based on type
	 * @param string $type - possible value: all (default), my, home, friends, user, search
	 * @param mixed $param - could be uid (friends, home, my, user) or a query string (search)
	 * @param int $page - page number
	 * @return array $albums
	 */
	public function getAlbums( $type = null, $param = null, $page = 1, $limit = RESULTS_LIMIT )
	{
		$cond = array();
		
		switch ( $type )
		{		
			case 'category':				
				if ( !empty( $param ) )
					$cond = array( 'Album.category_id' => $param, 
								   'Category.type' => APP_ALBUM, 
								   'Album.privacy' => PRIVACY_EVERYONE,
								   'Album.photo_count > ?' => 0,
								   'Album.type' => ''
								 );	
									
				break;	
			
			case 'friends':				
				if ( $param )
				{
					JsnApp::import('Model', 'Friend');	
					$friend = new Friend();
					$friends = $friend->getFriends( $param );										
					$cond = array( 'Album.user_id' => $friends, 
					               'Album.privacy <> ' . PRIVACY_ME, 
					               'Album.photo_count > ?' => 0,
					               'Album.type' => ''
                    );
				}					
				break;
				
			case 'home':
			case 'my':	
				if ( $param )
					$cond = array( 'Album.user_id' => $param );
					
				break;
				
			case 'user':
				if ( $param )
					$cond = array( 'Album.user_id' => $param, 
					               'Album.privacy <> ' . PRIVACY_ME, 
					               'Album.photo_count > ?' => 0
                    );
					
				break;
				
			case 'search':
				if ( $param )
					$cond = array( 'MATCH(Album.title, Album.description) AGAINST(? IN BOOLEAN MODE)' => urldecode($param), 
								   'Album.privacy' => PRIVACY_EVERYONE,
								   'Album.photo_count > ?' => 0,
								   'Album.type' => ''
								 );
					
				break;
			
			default:
				$cond = array( 'Album.privacy' => PRIVACY_EVERYONE, 
				               'Album.photo_count > ?' => 0, 
				               'Album.type' => ''
                             );
		}
		
		$albums = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page ) );
		
		return $albums;
	}
	
	public function getPopularAlbums( $limit = 5, $days = null )
	{
		$cond = array('Album.privacy' => PRIVACY_EVERYONE, 'Album.photo_count > ?' => 0, 'Album.type' => '' );
		
		if ( !empty( $days ) )
			$cond['DATE_SUB(CURDATE(),INTERVAL ? DAY) <= Album.created'] = intval($days);
		
		$albums = $this->find( 'all', array( 'conditions' => $cond, 
											 'order' => 'Album.like_count desc', 
											 'limit' => intval($limit)
							) );
									 
		return $albums;
	}

    public function getUserAlbumByType( $uid, $type )
    {
        $album = $this->find('first', array( 'conditions' => array( 'Album.user_id' => $uid, 'Album.type' => $type ) ) );
        
        return $album;
    }
	
	public function deleteAlbum( $id )
	{
		JsnApp::import('Model', 'Photo');	
		$photo = new Photo();
		$photos = $photo->getPhotos( APP_ALBUM, $id, null, null );
		
		foreach ( $photos as $p )
			$photo->deletePhoto( $p );
			
		$this->delete( $id );
		
		// delete activity
		JsnApp::import('Model', 'Activity');	
		$activity = new Activity();
		$activity->deleteAll( array( 'Activity.item_type' => APP_PHOTO, 'item_id' => $id ), true, true );
	}
}
 