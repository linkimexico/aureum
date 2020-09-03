<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Topic extends AppModel {	
	
	public $belongsTo = array( 'User' => array( 'counterCache' => true,
												'counterScope' => array('Topic.group_id' => 0)
											 ), 
							   'Category' => array( 
							   				'counterCache' => 'item_count', 
							   				'counterScope' => array( 'Category.type' => APP_TOPIC )
								), 
							   'LastPoster' => array( 
							   				'className' => 'User', 
							   				'foreignKey' => 'lastposter_id'
								),
								'Group' => array( 'counterCache' => true )
						);
						
	public $hasMany = array( 'Comment' => array( 
											'className' => 'Comment',	
											'foreignKey' => 'target_id',
											'conditions' => array('Comment.type' => APP_TOPIC),						
											'dependent'=> true
										),
						  	 'Like' => array( 
											'className' => 'Like',	
											'foreignKey' => 'target_id',
											'conditions' => array('Like.type' => APP_TOPIC),						
											'dependent'=> true
										),
						  	 'Tag' => array( 
											'className' => 'Tag',	
											'foreignKey' => 'target_id',
											'conditions' => array('Tag.type' => APP_TOPIC),						
											'dependent'=> true
										)
						); 
						
	public $order = "Topic.last_post desc";
	
	public $validate = array(	
							'title' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Title is required'
							),
							'category_id' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Category is required'
							),
							'body' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Body is required'
							)
	);
	
	/*
	 * Get topics based on type
	 * @param string $type - possible value: my, home, category, user, search, group
	 * @param mixed $param - could be catid (category), uid (home, my, user) or a query string (search)
	 * @param int $page - page number
	 * @return array $topics
	 */
	public function getTopics( $type = null, $param = null, $page = 1, $limit = RESULTS_LIMIT )
	{
		$cond  = array('Topic.group_id' => 0);
        $order = null;
		
		if ( $type == APP_GROUP )		
			$this->unbindModel(	array('belongsTo' => array('Category') ) );		
		else		
			$this->unbindModel(	array('belongsTo' => array('Group') ) );
		
		switch ( $type )
		{		
			case 'category':				
				if ( !empty( $param ) )
                {
					$cond  = array( 'Topic.category_id' => $param, 'Category.type' => APP_TOPIC );
                    $order = 'Topic.pinned desc, Topic.last_post desc';
				}	
									
				break;
				
			case 'friends':				
				if ( $param )
				{
					JsnApp::import('Model', 'Friend');	
					$friend = new Friend();
					$friends = $friend->getFriends( $param );										
					$cond = array( 'Topic.user_id' => $friends, 'Topic.group_id' => 0 );
				}					
				break;
				
			case 'home':
			case 'my':	
				if ( !empty( $param ) )
					$cond = array( 'Topic.user_id' => $param, 'Topic.group_id' => 0 );
					
				break;
				
			case 'user':
				if ( $param )
					$cond = array( 'Topic.user_id' => $param, 'Topic.group_id' => 0 );
					
				break;
				
			case 'search':
				if ( $param )
					$cond = array( 'Topic.group_id' => 0, 'MATCH(Topic.title, Topic.body) AGAINST(? IN BOOLEAN MODE)' => urldecode($param) );
					
				break;
				
			case 'group':
				if ( !empty( $param ) )
                {
					$cond = array( 'Topic.group_id' => $param );	
                    $order = 'Topic.pinned desc, Topic.last_post desc';
                }
									
				break;
		}
		
		$topics = $this->find( 'all', array( 'conditions' => $cond, 'order' => $order, 'limit' => $limit, 'page' => $page ) );
		
		return $topics;
	}
	
	public function getPopularTopics( $limit = 5, $days = null )
	{
		$this->unbindModel(	array('belongsTo' => array('Group') ) );
		
		$cond = array( 'Topic.group_id' => 0 );
		
		if ( !empty( $days ) )
			$cond['DATE_SUB(CURDATE(),INTERVAL ? DAY) <= Topic.created'] = intval($days);
		
		$topics = $this->find( 'all', array( 'conditions' => $cond, 
											 'order' => 'Topic.like_count desc', 
											 'limit' => intval($limit)
							) );
									 
		return $topics;
	}
	
	public function deleteTopic( $id )
	{
		$this->delete( $id );
		
		// delete activity
		JsnApp::import('Model', 'Activity');	
		$activity = new Activity();
		$activity->deleteAll( array( 'Activity.item_type' => APP_TOPIC, 'Activity.item_id' => $id ), true, true );
		
		// delete attachments
		JsnApp::import('Model', 'Attachment');	
		$attachment = new Attachment();
		$attachments = $attachment->getAttachments( PLUGIN_TOPIC_ID, $id);
		
		foreach ( $attachments as $a )
			$attachment->deletePhoto( $a );
	}
}
