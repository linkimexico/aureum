<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Blog extends AppModel {	
	
	public $belongsTo = array( 'User'  => array('counterCache' => true	));
	
	public $hasMany = array( 'Comment' => array( 
											'className' => 'Comment',	
											'foreignKey' => 'target_id',
											'conditions' => array('Comment.type' => APP_BLOG),						
											'dependent'=> true
										),
						  	 'Like' => array( 
											'className' => 'Like',	
											'foreignKey' => 'target_id',
											'conditions' => array('Like.type' => APP_BLOG),						
											'dependent'=> true
										),
						  	 'Tag' => array( 
											'className' => 'Tag',	
											'foreignKey' => 'target_id',
											'conditions' => array('Tag.type' => APP_BLOG),						
											'dependent'=> true
										)
						); 
	
	public $order = 'Blog.id desc';
	
	public $validate = array(	
							'title' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Title is required'
							),
							'body' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Body is required'
							)
	);
	
	/*
	 * Get blog entries based on type
	 * @param string $type - possible value: all (default), my, home, friends, user, search
	 * @param mixed $param - could be uid (friends, home, my, user) or a query string (search)
	 * @param int $page - page number
	 * @return array $blogs
	 */
	public function getBlogs( $type = null, $param = null, $page = 1, $limit = RESULTS_LIMIT )
	{
		$cond = array();
		
		switch ( $type )
		{		
			case 'friends':				
				if ( $param )
				{
					JsnApp::import('Model', 'Friend');	
					$friend = new Friend();
					$friends = $friend->getFriends( $param );										
					$cond = array( 'Blog.user_id' => $friends, 'Blog.privacy <> ' . PRIVACY_ME );
				}					
				break;
				
			case 'home':
			case 'my':	
				if ( $param )
					$cond = array( 'Blog.user_id' => $param );
					
				break;
				
			case 'user':
				if ( $param )
					$cond = array( 'Blog.user_id' => $param, 'Blog.privacy <> ' . PRIVACY_ME );
					
				break;
				
			case 'search':
				if ( $param )
					$cond = array( 'MATCH(Blog.title, Blog.body) AGAINST(? IN BOOLEAN MODE)' => urldecode($param), 'Blog.privacy' => PRIVACY_EVERYONE );
					
				break;
			
			default:
				$cond = array( 'Blog.privacy' => PRIVACY_EVERYONE );
		}

		$blogs = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page ) );
		
		return $blogs;
	}
	
	public function getPopularBlogs( $limit = 5, $days = null )
	{
		$cond = array('Blog.privacy' => PRIVACY_EVERYONE);
		
		if ( !empty( $days ) )
			$cond['DATE_SUB(CURDATE(),INTERVAL ? DAY) <= Blog.created'] = intval($days);
		
		$blogs = $this->find( 'all', array( 'conditions' => $cond, 
											'order' => 'Blog.like_count desc', 
											'limit' => intval($limit)
							) );
									 
		return $blogs;
	}
	
	public function deleteBlog( $id )
	{
		$this->delete( $id );
		
		// delete activity
		JsnApp::import('Model', 'Activity');	
		$activity = new Activity();
		$activity->deleteAll( array( 'Activity.item_type' => APP_BLOG, 'Activity.item_id' => $id ), true, true );
	}
}
