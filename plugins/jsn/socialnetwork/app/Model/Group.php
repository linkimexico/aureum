<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Group extends AppModel {	

	public $belongsTo = array( 'User',
							   'Category' => array( 'counterCache' => 'item_count', 
												 	'counterScope' => array( 'Group.type <> ?' => PRIVACY_PRIVATE,
							   												 'Category.type' => APP_GROUP ) ) 
					);
	
	public $hasMany = array( 'Activity' => array( 
											'className' => 'Activity',	
											'foreignKey' => 'target_id',
											'conditions' => array('Activity.type' => APP_GROUP),						
											'dependent'=> true
										),
						  	 'GroupUser' => array( 
						  					'className' => 'GroupUser',												  			
						  					'dependent'=> true
										),
						);  
						
	public $validate = array(	
							'name' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Name is required'
							),
							'category_id' => array( 	 
								'rule' => 'notEmpty',
								'message' => 'Category is required'
							),							
							'description' => array( 	 
								'rule' => 'notEmpty',
								'message' => 'Description is required'
							),		
							'user_id' => array( 'rule' => 'notEmpty')
	);
	
	public function getGroups( $type = null, $param = null, $page = 1, $limit = RESULTS_LIMIT )
	{
		$cond = array();
		
		switch ( $type )
		{				
			case 'category':						
				$cond = array( 'Group.category_id' => $param, 'Group.type <> ?' => PRIVACY_PRIVATE ); 			
				break;
				
			case 'search':	
				$cond = array( 'MATCH(Group.name, Group.description) AGAINST(? IN BOOLEAN MODE)' => urldecode($param), 'Group.type <> ?' => PRIVACY_PRIVATE ); 			
				break;
				
			default:
				$cond = array('Group.type <> ?' => PRIVACY_PRIVATE);	
		}
		
		$groups = $this->find( 'all', array( 'conditions' => $cond, 
											 'limit' 	  => $limit, 
											 'page' 	  => $page, 
											 'order' 	  => 'Group.id desc' 
							) );
		
		return $groups;
	}
	
	public function getPopularGroups( $limit = 5, $days = null )
	{
		$cond = array( 'Group.type <> ?' => PRIVACY_PRIVATE );
		
		if ( !empty( $days ) )
			$cond['DATE_SUB(CURDATE(),INTERVAL ? DAY) <= Group.created'] = intval($days);
		
		
		$groups = $this->find( 'all', array( 'conditions' => $cond,
											 'order' 	   => 'Group.group_user_count desc', 
											 'limit' 	   => intval($limit)
							) );
							
		return $groups;
	}
	
	public function deleteGroup( $group )
	{
		// delete photo
		if ( !empty( $group['Group']['photo'] ) )
			unlink( WWW_ROOT . 'uploads/groups/' . $group['Group']['photo'] );
			
		// delete group photos
		JsnApp::import('Model', 'Photo');	
		$photo = new Photo();
		$photos = $photo->getPhotos( APP_GROUP, $group['Group']['id'], null, null );
		
		foreach ( $photos as $p )
			$photo->deletePhoto( $p );
			
		// delete group videos
		JsnApp::import('Model', 'Video');	
		$video = new Video();
		$videos = $video->getVideos( APP_GROUP, $group['Group']['id'], null, null );
		
		foreach ( $videos as $v )
			$video->deleteVideo( $v );
			
		// delete group topics
		JsnApp::import('Model', 'Topic');	
		$topic = new Topic();
		$topics = $topic->getTopics( APP_GROUP, $group['Group']['id'], null );
		
		foreach ( $topics as $t )
			$topic->deleteTopic( $t['Topic']['id'] );
			
		// delete group
		$this->delete( $group['Group']['id'] );
		
		// delete activity
		JsnApp::import('Model', 'Activity');	
		$activity = new Activity();
		$activity->deleteAll( array( 'Activity.item_type' => APP_GROUP, 'item_id' => $group['Group']['id'] ), true, true );
        $activity->deleteAll( array( 'Activity.target_id' => $group['Group']['id'], 'Activity.type' => APP_GROUP ), true, true );
	}
}
