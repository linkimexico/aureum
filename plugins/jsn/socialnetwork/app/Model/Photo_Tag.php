<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class PhotoTag extends AppModel {
		
	public $belongsTo = array('User');
	
	public $order = 'Comment.created asc';
	
	public function getComments( $id, $type, $page = 1 )
	{
		$comment_count = $this->getCommentsCount( $id, $type );
													
		$offset = 0;
		$comments = array();
		
		if ( $comment_count > RESULTS_LIMIT )
			$offset = $comment_count - (RESULTS_LIMIT * $page);
			
		if ( $offset >= 0 )
			$comments = $this->find( 'all', array( 'conditions' => array( 'type' => $type, 
																		  'target_id' => $id
																		),
												   'limit'  => RESULTS_LIMIT,
												   'offset' => $offset
									) );
		return $comments;
	}
	
	public function getCommentsCount( $id, $type )
	{
		$this->cacheQueries = true;
		$comment_count = $this->find( 'count', array( 'conditions' => array( 'type' => $type, 
																	  		 'target_id' => $id
									) ) );
		return $comment_count;
	}
}
