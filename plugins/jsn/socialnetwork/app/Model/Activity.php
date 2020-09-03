<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Activity extends AppModel 
{
	public $validate = array( 'user_id' => array( 'rule' => 'notEmpty'),
							  'message' => array( 'rule' => 'notEmpty')
						 );							 
						 
	public $belongsTo = array( 'User' );
	
	public $hasMany = array( 'ActivityComment' => array( 
														 'className' => 'ActivityComment',							
													  	 'dependent' => true,
													  	 'order' => 'ActivityComment.id asc'
													),
							 'Like' => 			  array( 
										 				 'className' => 'Like',	
														 'foreignKey' => 'target_id',
														 'conditions' => array('Like.type' => 'activity'),						
														 'dependent'=> true
													),
						); 
	
	public $order = 'Activity.modified desc';
	
	/*
	 * Get the activities based on $type
	 * @param string $type
	 * @param mixed $param
     * @param mixed #param2
	 * @param int $page
	 * @return array $activities - formated array of activities
	 */
	
	public function getActivities( $type = null, $param = null, $param2 = null, $page = 1 )
	{
		$this->recursive = 2;
		$this->cacheQueries = true;
		$this->ActivityComment->cacheQueries = true;
        $this->User->cacheQueries = true;
		$cond = array();
		
		$this->unbindModel(
			array('hasMany' => array('Like'))
		);
		
		$this->ActivityComment->unbindModel(
			array('belongsTo' => array('Activity'))
		);
        
        $this->User->unbindModel(
            array('belongsTo' => array('Role'))
        );
		
		switch ( $type )
		{
			case 'home':
			case 'everyone':
				if ( !empty( $param ) )
				{
					JsnApp::import('Model', 'GroupUser');
					JsnApp::import('Model', 'EventRsvp');

					$group_user = new GroupUser();
					$event_rsvp = new EventRsvp();
					
					$groups = $group_user->getMyGroupsList( $param );					
					$events = $event_rsvp->getMyEventsList( $param );					
					
					$cond = array( 'OR' => array( array( 'Activity.type' => APP_USER, 'Activity.privacy' => PRIVACY_EVERYONE ),
												  array( 'Activity.type' => APP_GROUP, 'Activity.target_id' => $groups ),
												  array( 'Activity.type' => APP_EVENT, 'Activity.target_id' => $events )  
					) );
				}
				else
					$cond = array( 'Activity.type' => APP_USER, 'Activity.privacy' => PRIVACY_EVERYONE );
					
				break;
				
			case 'friends':
				JsnApp::import('Model', 'Friend');	
				$friend = new Friend();
				$friends = $friend->getFriends( $param );
				$friends[] = $param;
				
				JsnApp::import('Model', 'GroupUser');
				JsnApp::import('Model', 'EventRsvp');

				$group_user = new GroupUser();
				$event_rsvp = new EventRsvp();
				
				$groups = $group_user->getMyGroupsList( $param );					
				$events = $event_rsvp->getMyEventsList( $param );
				
				$cond = array( 'OR' => array( array( 'Activity.type' => APP_USER, 'Activity.user_id' => $friends, 'Activity.privacy <> ' . PRIVACY_ME ),
											  array( 'Activity.type' => APP_GROUP, 'Activity.target_id' => $groups ),
											  array( 'Activity.type' => APP_EVENT, 'Activity.target_id' => $events )  
				) );
				
				break;
				
			case 'profile':
                // $param: id of the user's profile page
                // $param2: id of the current logged in user
                
                $cond1 = array( 'Activity.user_id' => $param );
                
                if ( $param != $param2 ) // current user != user profile page
                    $cond1['Activity.privacy'] = PRIVACY_EVERYONE;
                
				$cond = array( 'OR' => array( $cond1, 
											  array( 'Activity.target_id' => $param, 
											  		 'Activity.type' => APP_USER, 
											  		 'Activity.action' => 'wall_post' 
				                                   )
                                            )
                             );
				
				break;
				
			case 'detail':
				$cond = array( 'Activity.id' => $param );
				break;
                
            default:
                $cond = array( 'Activity.type' => $type, 'Activity.target_id' => $param );
		}
		
		$activities = $this->find('all', array( 'conditions' => $cond, 
												'limit' => RESULTS_LIMIT,
												'page' => $page
								)	);
								
		// save all item types in an array
		$items = array();
		$activty_ids = array();		
		
		foreach ( $activities as &$activity )
		{
			if ( $activity['Activity']['query'] )
			{
				if ( !empty( $activity['Activity']['item_id'] ) )
				{
					// single item activity
					if ( empty($items[$activity['Activity']['item_type']])
					     || !in_array( $activity['Activity']['item_id'], $items[$activity['Activity']['item_type']] ) 
                    )                    
						$items[$activity['Activity']['item_type']][] = $activity['Activity']['item_id'];                    
				}
				elseif ( !empty( $activity['Activity']['items'] ) )
				{
					// multiple items activity
					$ids = explode(',', $activity['Activity']['items']);
					
					foreach ( $ids as $id)
						if ( $id > 0
						     && ( empty($items[$activity['Activity']['item_type']])
                             || !in_array( $id, $items[$activity['Activity']['item_type']] ) ) 
                        )                        
							$items[$activity['Activity']['item_type']][] = $id;                        
				}
			}
			
			// wall post
			if ( $activity['Activity']['target_id']
			     && !in_array( $type, array( APP_GROUP, APP_EVENT ) ) 
			     && ( empty($items[$activity['Activity']['type']]) || !in_array( $activity['Activity']['target_id'], $items[$activity['Activity']['type']] ) ) 
            )		    
		        $items[$activity['Activity']['type']][] = $activity['Activity']['target_id'];            
			
			// parse link & video link
			if ( $activity['Activity']['action'] == 'wall_post' )
			{
				if ( empty( $video ) )
				{					
					JsnApp::import('Model', 'Video');
					$video = new Video();
				}
							
				$video->parseStatus( $activity );
			}
			
			$activty_ids[] = $activity['Activity']['id'];
		}
		
		if ( !empty( $items ) )
		{
			JsnApp::import('Model', 'Comment');
            $comment = new Comment();
			
			JsnApp::import('Model', 'Like');
            $like = new Like();
                			
			foreach ( $items as $item_type => $item_ids )
			{
				// query items from item types array
				$model = ucfirst( $item_type );
				JsnApp::import('Model', $model);
				
				$instance = new $model();
				$items2 = $instance->find( 'all', array( 'conditions' => array( $model . '.id' => $item_ids ) ) );
				
				// save the items to item types array
				foreach ( $items2 as $item )
					$items[$item_type][$item[$model]['id']] = $item;
			}	

			JsnApp::import('Model', 'Photo');
			$photo = new Photo();
			
			// save the items to activities array			
			foreach ( $activities as $key => &$activity )
			{
				/*if ( $activity['Activity']['item_type'] == APP_PHOTO
				     && !in_array( $activity['Activity']['action'], array( 'comment_add', 'like_add' ) ) )*/
				if ( $activity['Activity']['item_type'] == APP_PHOTO && !empty( $activity['Activity']['items'] ) )
				{
					$photo_ids = explode( ',', $activity['Activity']['items'] );
					$activity['Content'] = $photo->find( 'all', array( 'conditions' => array( 'Photo.id' => $photo_ids ),
																	   'limit' => 8
													 ) );
				}			
				elseif ( $activity['Activity']['query'] )
				{					    
					if ( !empty( $activity['Activity']['item_type'] ) 
					     && !empty( $items[$activity['Activity']['item_type']][$activity['Activity']['item_id']] ) )
					{		
						if ( !empty( $activity['Activity']['items'] ) )
						{
							// multiple items
							$ids = explode(',', $activity['Activity']['items']);
							
							foreach ( $ids as $id)
							{
								if ( $id > 0 
								     && !empty( $items[$activity['Activity']['item_type']][$id] ) 
								     && is_array( $items[$activity['Activity']['item_type']][$id] )
                                   )
								{
									$activity['TextContent'][] = $items[$activity['Activity']['item_type']][$id];
									$activity['Content'][] = $items[$activity['Activity']['item_type']][$id];
								}
							}								
						}
						elseif ( !empty( $activity['Activity']['item_id'] ) )
						{
							// single item							
							if ( $activity['Activity']['query'] 
							     && !empty($items[$activity['Activity']['item_type']][$activity['Activity']['item_id']]) )
							{
								$activity['TextContent'][] = $items[$activity['Activity']['item_type']][$activity['Activity']['item_id']];							
								$activity['Content'] = $items[$activity['Activity']['item_type']][$activity['Activity']['item_id']];
							}
						}
					}
					else // item was deleted
						unset( $activities[$key] );												
				}

				// item activity
				if ( $activity['Activity']['params'] == 'item' )
				{
                    $item_type = ( $activity['Activity']['item_type'] == APP_PHOTO ) ? APP_ALBUM : $activity['Activity']['item_type'];
                    
                    // get item's comments
                    $activity['ItemComment'] = $comment->find('all', array(  'conditions' => array( 
                                                                                 'Comment.target_id' => $activity['Activity']['item_id'],
                                                                                 'Comment.type'      => $item_type ),
                                                                             'order' => 'Comment.id desc',
                                                                             'limit' => 2
                    )  );
					
					$activity['ItemComment'] = array_reverse( $activity['ItemComment'] );
					
					// get items' likes
					$activity['Likes'] = $like->find('list', array( 'conditions' => array( 
																		'Like.target_id' => $activity['Activity']['item_id'],
                                                                        'Like.type'      => $item_type ),
                    											   'fields' => array( 'Like.user_id', 'Like.thumb_up' )                                                    
					) );
                }  
				
				// wall post, photos, videos, topics on profile/group/event wall
				if ( $activity['Activity']['target_id'] 
					 && !in_array( $type, array(APP_GROUP, APP_EVENT) ) 
					 && is_array( $items[$activity['Activity']['type']][$activity['Activity']['target_id']] ) 
					 && !( $type == 'profile' && $activity['Activity']['target_id'] == $param && $activity['Activity']['type'] == APP_USER )
				)
					$activity['TextContent'] = $items[$activity['Activity']['type']][$activity['Activity']['target_id']];
			}
		}
					
		return $activities;
	}

	/*
	 * Get latest activity of $uid for $action within a day
	 * @param string $action
	 * @param int $uid - user id
     * @param string $item_type
	 * @return array $activity
	 */
	
	public function getRecentActivity( $action = null, $uid = null, $item_type = null )
	{
		$cond = array( 'Activity.user_id' => $uid, 
                       'Activity.action' => $action,
                       'DATE_SUB(CURDATE(),INTERVAL 1 DAY) <= Activity.created'
        );
        
        if ( !empty( $item_type ) )
            $cond['Activity.item_type'] = $item_type;
            
		$activity = $this->find( 'first', array( 'conditions' => $cond	)	);
        
		return $activity;
	}
	
	/*
	 * Get item activity
	 * @param string $item_type
	 * @param int $item_id
	 * @return array $activity
	 */
	
	public function getItemActivity( $item_type = null, $item_id = null )
	{			
		$activity = $this->find( 'first', array( 'conditions' => array( 'Activity.item_type' => $item_type, 
																	    'Activity.item_id' 	 => $item_id,
																	    'Activity.params'	 => 'item',
																	    'Activity.type' 	 => 'user'
								) 	)	);
		return $activity;
	}
	
	/*
	 * Get comment/like activity of an item
	 * @param string $item_type
	 * @param int $item_id
	 * @return array $activity
	 */
	
	public function getCommentLikeActivity( $item_type = null, $item_id = null, $action = 'like_add' )
	{
		$activity = $this->find( 'first', array( 'conditions' => array( 'Activity.action' 	 => $action, 
																	    'Activity.item_type' => $item_type,
																	    'Activity.item_id' 	 => $item_id
								) 	)	);
		return $activity;
	}
    
    public function parseLink( &$data )
    {
        JsnApp::uses('Validation', 'Utility');
        $url = trim( $data['content'] );
        
        if ( Validation::url( $url ) )
        {   
            if ( strpos( $url, 'http' ) === false )
                $url = 'http://' . $url;
            
            JsnApp::uses('HttpSocket', 'Network/Http');
            $HttpSocket = new HttpSocket();
            
            $response = $HttpSocket->get( $url );
            
            if ( $response->code == 301 ) // moved permanently
                $response = $HttpSocket->get( $response->headers['Location'] );
            
            if ( $response->code < 300 )
            {
                // get title       
                //if( preg_match("/<title>(.+)<\/title>/i", $content, $m) )
                if( preg_match("|<[\s]*title[\s]*>([^<]+)<[\s]*/[\s]*title[\s]*>|Ui", $response->body, $m) )
                    $link['title'] = trim( $m[1] );
                
                if ( !empty( $link['title'] ) )
                {
                    // get description
                    if( preg_match("/<meta name=\"description\" content=\"(.+)\"/i", $response->body, $m) )
                        $link['description'] = trim( $m[1] );
                    
                    if( empty($link['description']) && preg_match("/<meta content=\"(.+)\" name=\"description\"/i", $response->body, $m) )
                        $link['description'] = trim( $m[1] );
                                         
                    if( empty($link['description']) && preg_match("/<meta property=\"og:description\" content=\"(.+)\"/i", $response->body, $m) )
                        $link['description'] = trim( $m[1] );
                    
                    // get image
                    if( preg_match("/<meta property=\"og:image\" content=\"(.+)\"/i", $response->body, $m) )
                    {
                        $image_url = trim( $m[1] );
                        
                        if ( $image_url )
                        {
                            $tmp = explode('.', $image_url);
                            $ext = strtolower( array_pop($tmp) );
                            
                            if ( in_array($ext, array( 'jpg', 'jpeg', 'gif', 'png' ) ) )
                            {                            
                                $image_name = md5( time() ) . '.' . $ext;
                                                    
                                try
                                {
                                	$image = $HttpSocket->get( $image_url );
                                }
                                catch (Exception $e) 
                                {
                                	$image_url = str_replace('https://', 'http://', $image_url);
                                	$image = $HttpSocket->get( $image_url );
                                }
                                                            
                                if ( $image->code < 300 )
                                {
                                    $image_loc = WWW_ROOT . 'uploads/links/' . $image_name;          
                                    file_put_contents( $image_loc, $image );
                                    
                                    // resize image
                                    JsnApp::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));                                
                                    $thumb = PhpThumbFactory::create($image_loc, array('jpegQuality' => 100));
                                    $thumb->resize(100, 100)->save($image_loc); 
									$link['image'] = $image_name;
                                }
                                    
                                
                            }
                        }
                    }
                    
                    $data['params'] = serialize( $link );
                    
                    if ( substr($data['content'], 0, 4) != 'http' )
                        $data['content'] = 'http://' . $data['content'];
                }
            }
        }
    }
}
 