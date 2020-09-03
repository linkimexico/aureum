<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Video extends AppModel {
						 
	public $belongsTo = array( 'User' => array( 'counterCache' => true,
												'counterScope' => array('Video.group_id' => 0)
											 ), 
							   'Category' => array( 'counterCache' => 'item_count', 
							   						'counterScope' => array( 'Video.privacy' => PRIVACY_EVERYONE, 
							   												 'Category.type' => APP_VIDEO ) ),
							   'Group' => array( 'counterCache' => true )
	);
	
	public $hasMany = array( 'Comment' => array( 
											'className' => 'Comment',	
											'foreignKey' => 'target_id',
											'conditions' => array('Comment.type' => APP_VIDEO),						
											'dependent'=> true
										),
						  	 'Like' => array( 
											'className' => 'Like',	
											'foreignKey' => 'target_id',
											'conditions' => array('Like.type' => APP_VIDEO),						
											'dependent'=> true
										),
						  	 'Tag' => array( 
											'className' => 'Tag',	
											'foreignKey' => 'target_id',
											'conditions' => array('Tag.type' => APP_VIDEO),						
											'dependent'=> true
										)
						);
	
	public $order = "Video.id desc";
	
	public $validate = array(							
							'source' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Source is required'
							),
							'title' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Title is required'
							),
							'category_id' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Category is required'
							)
	);
	
	/*
	 * Get videos based on type
	 * @param string $type - possible value: all (default), my, home, friends, user, search
	 * @param mixed $param - could be uid (friends, home, my, user) or a query string (search)
	 * @param int $page - page number
	 * @return array $videos
	 */
	public function getVideos( $type = null, $param = null, $page = 1, $limit = RESULTS_LIMIT )
	{
		$cond = array();
		
		if ( $type == APP_GROUP )		
			$this->unbindModel(	array('belongsTo' => array('Category') ) );		
		else		
			$this->unbindModel(	array('belongsTo' => array('Group') ) );
		
		switch ( $type )
		{
			case 'category':				
				if ( !empty( $param ) )
					$cond = array( 'Video.category_id' => $param, 'Category.type' => APP_VIDEO, 'Video.privacy' => PRIVACY_EVERYONE );	
									
				break;	
						
			case 'friends':				
				if ( $param )
				{
					JsnApp::import('Model', 'Friend');	
					$friend = new Friend();
					$friends = $friend->getFriends( $param );										
					$cond = array( 'Video.user_id' => $friends, 'Video.privacy <> ' . PRIVACY_ME, 'Video.group_id' => 0 );
				}					
				break;
				
			case 'home':
			case 'my':	
				if ( $param )
					$cond = array( 'Video.user_id' => $param, 'Video.group_id' => 0 );
					
				break;
				
			case 'user':
				if ( $param )
					$cond = array( 'Video.user_id' => $param, 'Video.group_id' => 0, 'Video.privacy <> ' . PRIVACY_ME );
					
				break;
				
			case 'search':
				if ( $param )
					$cond = array( 'Video.group_id' => 0, 'MATCH(Video.title, Video.description) AGAINST(? IN BOOLEAN MODE)' => urldecode($param), 'Video.privacy' => PRIVACY_EVERYONE );
					
				break;
				
			case 'group':
				if ( !empty( $param ) )
					$cond = array( 'Video.group_id' => $param );	
									
				break;
			
			default:
				$cond = array( 'Video.privacy' => PRIVACY_EVERYONE, 'Video.group_id' => 0 );
		}
		
		$videos = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page ) );
		
		return $videos;
	}
	
	public function fetchVideo( $source, $url, $setting = null )
	{
		$video = array();
			
		JsnApp::uses('HttpSocket', 'Network/Http');
        $HttpSocket = new HttpSocket();
        
		switch ( $source )
		{
			case 'youtube': 
				if ( strpos( $url, 'http://youtu.be' ) !== false || strpos( $url, 'https://youtu.be' ) !== false )
				{
					$tmp = explode('/', $url);
					$id  = $tmp[count($tmp)-1]; 
				}
				else
				{
					$url_string = parse_url($url, PHP_URL_QUERY);				
			  		parse_str($url_string, $args);
			  		$id = isset($args['v']) ? $args['v'] : false;		
			  	}
				
				if ( !empty( $id ) )
				{
                    //$url = 'http://gdata.youtube.com/feeds/api/videos/' . $id;
					$url = 'http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v='.$id.'&format=json';
                    
                    if ( !empty( $setting['google_dev_key'] ) )
                        $url .= '?key=' . $setting['google_dev_key'];
                        
                    $response = $HttpSocket->get( $url );
                    
                    if ( $response->code > 300 )
                        return;

					/*libxml_use_internal_errors(true);
                    $entry = simplexml_load_string( $response );
                    
                    if ( $entry )
                    {
                        $media = $entry->children('http://search.yahoo.com/mrss/');
                        
                        if ( !empty( $entry ) )
                        {
        				    $video['Video']['source'] = 'youtube';
        				    $video['Video']['source_id'] = $id;
        				    $video['Video']['title'] = (string) $media->group->title[0];
        				    $video['Video']['description'] = (string) $media->group->description[0];
        				      
        				    // get video thumbnail
        				    $attrs = $media->group->thumbnail[0]->attributes();
        				    $video['Video']['thumb'] = substr( $attrs['url'], 0, -5) . 'mqdefault.jpg';
                        }
                    }*/
					$entry=json_decode($response);
					if ( $entry )
               		{
						$video['Video']['source'] = 'youtube';
    				    $video['Video']['source_id'] = $id;
    				    $video['Video']['title'] = (string) $entry->title;
    				    $video['Video']['description'] = '';
    				      
    				    // get video thumbnail
    				    //$attrs = $media->group->thumbnail[0]->attributes();
    				    $video['Video']['thumb'] = $entry->thumbnail_url;
					}
				}
							
				break;
				
			case 'vimeo':
				preg_match('/(\d+)/', $url, $matches);
				
				if ( !empty( $matches[0] ) )
				{
					$id = $matches[0];
                    
                    $entry = $HttpSocket->get( 'http://vimeo.com/api/v2/video/' . $id . '.php' );		
					$entry = unserialize( $entry );
					
				    if ( !empty( $entry ) )
					{
					    $video['Video']['source'] = 'vimeo';
					    $video['Video']['source_id'] = $id;
					    $video['Video']['title'] = $entry[0]['title'];
					    $video['Video']['description'] = str_replace( '<br />', '', $entry[0]['description'] );
						$video['Video']['thumb'] = $entry[0]['thumbnail_medium'];
					}
				}
				
				break; 
		}

		if ( !empty( $video ) )
		{
			$video['Video']['id'] = '';
			$video['Video']['category_id'] = '';
			$video['Video']['privacy'] = PRIVACY_EVERYONE;
		}
		
		return $video;
	}

	public function parseStatus( &$activity )
	{
		if ( strpos( $activity['Activity']['content'], 'youtube.com' ) !== false || strpos( $activity['Activity']['content'], 'youtu.be' ) !== false )
			$source = 'youtube';
						
		if ( strpos( $activity['Activity']['content'], 'vimeo.com' ) !== false )
			$source = 'vimeo';
			
		if ( !empty( $source ) )
		{					
			$vid = $this->fetchVideo($source, $activity['Activity']['content']);
		
			if ( !empty( $vid ) )
				$activity['Content'] = $vid;
		}
	}
	
	public function getPopularVideos( $limit = 5, $days = null )
	{
		$this->unbindModel(	array('belongsTo' => array('Group') ) );
	
		$cond = array('Video.privacy' => PRIVACY_EVERYONE, 'Video.group_id' => 0);
		
		if ( !empty( $days ) )
			$cond['DATE_SUB(CURDATE(),INTERVAL ? DAY) <= Video.created'] = intval($days);
		
		$videos = $this->find( 'all', array( 'conditions' => $cond, 
											 'order' => 'Video.like_count desc', 
											 'limit' => intval($limit)
							) );
									 
		return $videos;
	}
	
	public function deleteVideo( $video )
	{
		// delete photo
		if ( !empty( $video['Video']['thumb'] ) )
			unlink( WWW_ROOT . 'uploads/videos/' . $video['Video']['thumb'] );
			
		$this->delete( $video['Video']['id'] );
		
		// delete activity
		JsnApp::import('Model', 'Activity');	
		$activity = new Activity();
		$activity->deleteAll( array( 'Activity.item_type' => APP_VIDEO, 'Activity.item_id' => $video['Video']['id'] ), true, true );
	}
}
