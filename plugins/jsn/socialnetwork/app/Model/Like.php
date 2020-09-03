<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Like extends AppModel {
		
	public $belongsTo = array( 'User' );
	
	public $order = array('Like.id desc');
	
	public function getLikes( $id, $type, $limit = null, $page = 1 )
	{
		$likes = $this->find( 'all', array( 'conditions' => array( 'Like.type' => $type, 
																   'Like.target_id' => $id, 
																   'Like.thumb_up' => 1 ),
											'limit' => $limit,
											'page' => $page
							) ) ;
		return $likes;
	}
	
	public function getUserLike( $id, $uid, $type )
	{
		$like = $this->find( 'first', array( 'conditions' => array( 'Like.type' => $type, 
																	'Like.target_id' => $id, 
																	'Like.user_id' => $uid 
							) ) );
		return $like;
	}
	
	public function getAllUserLikes( $uid )
	{
		$res = array();
		$items = $this->find( 'all', array( 'conditions' => array( 'user_id' => $uid ), 
											'order' => 'Like.id desc', 
											'limit' => RESULTS_LIMIT ) 
							);

		$blogids = array();
		$topicids = array();
		$albumids = array();
		$videoids = array();
		$unions = array();
		$likes = array();

		foreach ($items as $item)
		{
			switch ($item['Like']['type'])
			{
				case 'blog':
					$blogids[] = $item['Like']['target_id'];
					break;
					
				case 'topic':
					$topicids[] = $item['Like']['target_id'];
					break;
					
				case 'album':
					$albumids[] = $item['Like']['target_id'];
					break;
					
				case 'video':
					$videoids[] = $item['Like']['target_id'];
					break;
			}
		}

		if ( !empty($blogids) )
		{
			JsnApp::import('Model', 'Blog');
			$blog = new Blog();
			
			$likes['blog'] = $blog->find( 'all', array( 'conditions' => array( 'Blog.id' => $blogids ) ) );
		}
		
		if ( !empty($topicids) )
		{
			JsnApp::import('Model', 'Topic');
			$topic = new Topic();
			
			$likes['topic'] = $topic->find( 'all', array( 'conditions' => array( 'Topic.id' => $topicids ) ) );
		}
		
		if ( !empty($albumids) )
		{
			JsnApp::import('Model', 'Album');
			$album = new Album();
			
			$likes['album'] = $album->find( 'all', array( 'conditions' => array( 'Album.id' => $albumids ) ) );
		}
		
		if ( !empty($videoids) )
		{
			JsnApp::import('Model', 'Video');
			$video = new Video();
			
			$likes['video'] = $video->find( 'all', array( 'conditions' => array( 'Video.id' => $videoids ) ) );
		}
		
		return $likes;
	}

	public function getActivityLikes( $activities, $uid )
	{
		$res = array();
		$activity_ids = array();
		$activity_comment_ids = array();
		$comment_ids = array();
			
		foreach ( $activities as $activity )
		{
			$activity_ids[] = $activity['Activity']['id'];
			
			foreach ( $activity['ActivityComment'] as $comment )
				$activity_comment_ids[] = $comment['id'];
			
			if ( !empty( $activity['ItemComment'] ) )
				foreach ( $activity['ItemComment'] as $comment )
					$comment_ids[] = $comment['Comment']['id'];
		}
		
		if ( !empty( $activity_ids ) )
			$res['activity_likes'] = $this->find( 'list', array( 'conditions' => array( 'Like.user_id' => $uid, 
																				 		'Like.type' => 'activity', 
																				 		'Like.target_id' => $activity_ids
																			   		  ),
														  		 'fields' => array( 'Like.target_id', 'Like.thumb_up' )
												) );
												
		if ( !empty( $activity_comment_ids ) )									
			$res['comment_likes'] = $this->find( 'list', array( 'conditions' => array( 'Like.user_id' => $uid, 
																				 	   'Like.type' => 'activity_comment', 
																				 	   'Like.target_id' => $activity_comment_ids
																			   		 ),
														  		'fields' => array( 'Like.target_id', 'Like.thumb_up' )
												) );
											
		if ( !empty( $comment_ids ) )	
			$res['item_comment_likes'] = $this->find( 'list', array( 'conditions' => array('Like.user_id' => $uid, 
																					 	   'Like.type' => 'comment', 
																					 	   'Like.target_id' => $comment_ids
																				   		 ),
															  		 'fields' => array( 'Like.target_id', 'Like.thumb_up' )
													) );
									
		return $res;
	}
	
	public function getCommentLikes( $comments, $uid )
	{
		$comment_ids = array();
			
		foreach ( $comments as $comment )
			$comment_ids[] = $comment['Comment']['id'];;
									
		$comment_likes = $this->find( 'list', array( 'conditions' => array( 'user_id' => $uid, 
																			'type' => 'comment', 
																			'target_id' => $comment_ids
																		  ),
													 'fields' => array( 'Like.target_id', 'Like.thumb_up' )
									) );
									
		return $comment_likes;
	}
}
