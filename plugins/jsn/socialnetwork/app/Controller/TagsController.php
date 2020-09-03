<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class TagsController extends AppController {
	
	public $paginate = array( 'limit' => RESULTS_LIMIT );
	
	public function view($tag = null, $order = null)
	{
		$tag = h(urldecode($tag));
		$items = $this->Tag->findAllByTag( $tag );

		$blogids = array();
		$topicids = array();
		$albumids = array();
		$videoids = array();
		$unions = array();

		foreach ($items as $item)
		{
			switch ($item['Tag']['type'])
			{
				case 'blog':
					$blogids[] = $item['Tag']['target_id'];
				break;
				case 'topic':
					$topicids[] = $item['Tag']['target_id'];
				break;
				case 'album':
					$albumids[] = $item['Tag']['target_id'];
				break;
				case 'video':
					$videoids[] = $item['Tag']['target_id'];
				break;
			}
		}

		if ( !empty($blogids) )
			$unions[] = "SELECT i.id, i.title, i.body, i.like_count, i.created, 'blog' as type, i.user_id, NULL as thumb
						 FROM " . $this->Tag->tablePrefix . "blogs i
						 WHERE i.id IN (" . implode(',', $blogids) . ")";
			
		if ( !empty($topicids) )
			$unions[] = "SELECT i.id, i.title, i.body, i.like_count, i.created, 'topic' as type, i.user_id, NULL as thumb
						 FROM " . $this->Tag->tablePrefix . "topics i
						 WHERE i.id IN (" . implode(',', $topicids) . ")";
			
		if ( !empty($albumids) )
			$unions[] = "SELECT i.id, i.title, i.description as body, i.like_count, i.created, 'album' as type, i.user_id, i.cover as thumb
						 FROM " . $this->Tag->tablePrefix . "albums i
						 WHERE i.id IN (" . implode(',', $albumids) . ")";
						 
		if ( !empty($videoids) )
			$unions[] = "SELECT i.id, i.title, i.description as body, i.like_count, i.created, 'video' as type, i.user_id, i.thumb
						 FROM " . $this->Tag->tablePrefix . "videos i
						 WHERE i.id IN (" . implode(',', $videoids) . ")";
			
		if ( !empty($unions) )
		{
			$order = ( $order == 'popular' ) ? 'like_count' : 'created';
							
			$query = implode( ' union ', $unions ) . ' order by ' . $order . ' desc limit ' . RESULTS_LIMIT;
			$items = $this->Tag->query( $query );

			$this->set('items', $items);
			$this->set('order', $order);
			$this->set('unions', count($unions));
		}		
		
		$jsnsocial_setting = $this->_getSettings();
		$tags = $tags = $this->Tag->getTags( null, $jsnsocial_setting['popular_interval'], RESULTS_LIMIT * 2 );
		
		$this->set('tag', $tag);
		$this->set('tags', $tags);
	}
	
	public function admin_index()
	{
		if ( !empty( $this->request->data['keyword'] ) )
			$this->redirect( '/admin/tags/index/keyword:' . $this->request->data['keyword'] );
			
		$cond = array();
		if ( !empty( $this->request->named['keyword'] ) )
			$cond['tag'] = $this->request->named['keyword'];
			
		$tags = $this->paginate( 'Tag', $cond );	
		
		$this->set('tags', $tags);
		$this->set('title_for_layout', 'Tags Manager');
	}
	
	public function admin_delete()
	{
		$this->_checkPermission(array('super_admin' => 1));
		
		if ( !empty( $_POST['tags'] ) )
		{					
			foreach ( $_POST['tags'] as $tag_id )
				$this->Tag->delete( $tag_id );	

			$this->Session->setFlash( 'Tags deleted' );				
		}
		
		$this->redirect( $this->referer() );
	}
}
