<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Tag extends AppModel 
{
	public function saveTags($new_tags, $target_id, $type)
	{
		$new_tags = explode(',', $new_tags);
		foreach ($new_tags as &$tag)
			$tag = strtolower(trim($tag));

		$current_tags = $this->find('list', array('conditions' => array('Tag.target_id' => $target_id, 'Tag.type' => $type), 'fields' => array('Tag.id', 'tag')));

		// loop through new tags and add any tag that does not exist in current tags
		foreach ($new_tags as $val)
		{
			$check = array_keys($new_tags, $val);
			if (!empty($val) && !in_array($val, $current_tags) && count($check) == 1)
			{
				$this->create();
				$this->save( array('target_id' => $target_id, 'type' => $type, 'tag' => $val) );
			}
		}

		// loop through current tags and remove any tag that does not exist in new tags
		foreach ($current_tags as $key => $val)
			if (!in_array($val, $new_tags))
				$this->delete($key);
	}
    
    /**
     * Get popular tags
     * @param string $type
     * @param int $days
     * @param int $limit
     * @return array $tags
     */
	public function getTags($type = '', $days = 0, $limit = RESULTS_LIMIT)
	{
		//$cond = array('DATE_SUB(CURDATE(),INTERVAL ? DAY) <= Tag.created' => $days);
		$cond = array();
		if ($type)
			$cond['type'] = $type;

		$tags = $this->find( 'all', array( 'fields' => array( 'DISTINCT tag', '(SELECT count(*) FROM ' . $this->tablePrefix . 'tags WHERE tag=Tag.tag) as count' ),
										   'conditions' => $cond,
										   'order' => 'count desc',
										   'limit' => $limit
				 			) 	);

		return $tags;
	}
	
	public function getContentTags( $id, $type )
	{
		$tags = $this->find('list', array('conditions' => array('Tag.target_id' => $id, 'Tag.type' => $type), 'fields' => array('tag')));										
		return $tags;
	}
	
	public function getSimilarVideos( $id, $tags )
	{
		$this->bindModel(
			array('belongsTo' => array(
					'Video' => array(
						'className' => 'Video',
						'foreignKey' => 'target_id'
					)
				)
			)
		);
		
		$similar_videos = $this->find('all', array( 'conditions' => array( 'Tag.tag' => $tags, 
																		   'Tag.type' => APP_VIDEO, 
																		   'Tag.target_id <> ?' => $id
																		), 
											   		'fields' => array( 'DISTINCT Video.id, Video.title, Video.thumb, Video.like_count' ),
											   		'limit' => 5	
									) 	);
		return $similar_videos;
	}
}

?>