<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Comment extends AppModel 
{

	public $hasMany = array( 'Like' => 	array( 'className' => 'Like',	
											   'foreignKey' => 'target_id',
											   'conditions' => array('Like.type' => 'comment'),						
											   'dependent'=> true
											 ),
							);	
						 
	public $belongsTo = array('User');
	
	public $validate = array( 'message' => array( 'rule' => 'notEmpty') );
	
	public $order = 'Comment.id asc';
	
	/*
	 * Get comments based on $id and $type
	 * @param int $id - item id
	 * @param string $tyoe - item type
	 * @param int $page
	 * @return array $comments
	 */
	
	public function getComments( $id, $type, $page = 1 )
	{
		$comment_count = $this->getCommentsCount( $id, $type );
													
		$offset = 0;
		$comments = array();
		
		if ( $comment_count >= RESULTS_LIMIT )
			$offset = $comment_count - (RESULTS_LIMIT * $page);
		
		if ( $offset >= 0 || ( $offset < 0 && RESULTS_LIMIT >= abs( $offset ) ) )
		{		
			if ( $offset < 0 && RESULTS_LIMIT >= abs( $offset ) )
			{
				$limit = RESULTS_LIMIT - abs( $offset );
				$offset = 0;
			}
			else
				$limit = RESULTS_LIMIT;

			$comments = $this->find( 'all', array( 'conditions' => array( 'Comment.type' => $type, 
																		  'Comment.target_id' => $id
																		),
												   'limit'  => $limit,
												   'offset' => $offset
									) );
		}

		return $comments;
	}
	
	/*
	 * Get comments count based on $id and $type
	 * @param int $id - item id
	 * @param string $tyoe - item type
	 * @return int $comment_count
	 */
	
	public function getCommentsCount( $id, $type )
	{
		$comment_count = $this->find( 'count', array( 'conditions' => array( 'Comment.type' => $type, 
																	  		 'Comment.target_id' => $id
									) ) );
		return $comment_count;
	}
}
 