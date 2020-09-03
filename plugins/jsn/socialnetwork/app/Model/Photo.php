<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Photo extends AppModel {	

	public $belongsTo = array( 'Album'  => array( 
												'foreignKey' => 'target_id',
											    'conditions' => 'Photo.type = "album"',
											    'counterCache' => true,
											    'counterScope' => 'Photo.type = "album"'
											 ),
							   'Group'  => array( 
												'foreignKey' => 'target_id',
											    'conditions' => 'Photo.type = "group"',
											    'counterCache' => true,
											    'counterScope' => 'Photo.type = "group"'
											 ),
							   'User'  => array(	
												'counterCache' => true, 
												'counterScope' => array('Photo.type = "album"')
											)
						);
						
	public $hasMany = array( 'Comment' => array( 
												'className' => 'Comment',	
												'foreignKey' => 'target_id',
												'conditions' => array('Comment.type' => APP_PHOTO),						
												'dependent'=> true
											),
						  	 'Like' => array( 
												'className' => 'Like',	
												'foreignKey' => 'target_id',
												'conditions' => array('Like.type' => APP_PHOTO),						
												'dependent'=> true
											),
						  	 'Tag' => array( 
												'className' => 'Tag',	
												'foreignKey' => 'target_id',
												'conditions' => array('Tag.type' => APP_PHOTO),						
												'dependent'=> true
											)
						); 

	public $validate = array( 'type' => array( 'rule' => 'notEmpty'),
							  'target_id' => array( 'rule' => 'notEmpty'),
							  'user_id' => array( 'rule' => 'notEmpty'),
							  'path' => array( 'rule' => 'notEmpty' ),
							  'thumb' => array( 'rule' => 'notEmpty' )
						 );

	public $order = 'Photo.id desc';
	
	/*
	 * Get photos based on type
	 * @param string $type - possible value: album, group
	 * @param int $target_id - could be album_id or group_id
	 * @param int $page - page number
	 * @return array $photos
	 */
	public function getPhotos( $type = null, $target_id = null, $page = 1, $limit = 5 )
	{		
		if ( $type == APP_GROUP )		
			$this->unbindModel(	array('belongsTo' => array('Album') ) );		
		else		
			$this->unbindModel(	array('belongsTo' => array('Group') ) );
	
		$cond = array( 'Photo.type' => $type, 'target_id' => $target_id );
		
		if ( !empty( $page ) )
			$photos = $this->find( 'all', array( 'conditions' => $cond, 'limit' => RESULTS_LIMIT, 'page' => $page ) );
		else
			$photos = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit ) );
							
		return $photos;
		
	}
	
	public function deletePhoto( $photo )
	{
		// delete activity
		if ( $photo['Album']['type'] == 'newsfeed' )
		{
			JsnApp::import('Model', 'Activity');	
			$activity = new Activity();
			
			$activity->deleteAll( array( 'Activity.item_type' => APP_PHOTO, 'item_id' => $photo['Photo']['id'] ), true, true );
		}
			
		if ( file_exists(WWW_ROOT . $photo['Photo']['path']) )
			unlink(WWW_ROOT . $photo['Photo']['path']);

		if ( file_exists(WWW_ROOT . $photo['Photo']['thumb']) )
			unlink(WWW_ROOT . $photo['Photo']['thumb']);
		
		if ( !empty( $photo['Photo']['original'] ) && file_exists(WWW_ROOT . $photo['Photo']['original'] ) )
			unlink(WWW_ROOT . $photo['Photo']['original']);
			
		$this->delete( $photo['Photo']['id'] );
	}
}
