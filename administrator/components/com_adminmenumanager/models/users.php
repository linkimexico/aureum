<?php
/**
* @package Admin-Menu-Manager (com_adminmenumanager)
* @version 2.3.0
* @copyright Copyright (C) 2012 - 2018 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/



// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class adminmenumanagerModelUsers extends JModelList
{	
	var $parent_groups;
	protected $option = 'com_adminmenumanager';	
	
	public function __construct($config = array()){
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',					
				'username', 'a.username'				
			);
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = NULL, $direction = NULL)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);		

		$groupId = $app->getUserStateFromRequest($this->context.'.filter.group', 'filter_group_id', null, 'int');
		$this->setState('filter.group_id', $groupId);
		
		$level_id = $app->getUserStateFromRequest($this->context.'.filter.level', 'filter_level_id', null, 'int');
		$this->setState('filter.level_id', $level_id);

		// Load the parameters.
		$params	= JComponentHelper::getParams('com_adminmenumanager');
		$this->setState('params', $params);
		
		// List state information.		
		parent::populateState('a.username', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');		
		$id	.= ':'.$this->getState('filter.group_id');
		$id	.= ':'.$this->getState('filter.level_id');

		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
	
		$this->update_usergroup_levels_map();
		
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'				
			)
		);
		$query->from('`#__users` AS a');

		// Join over the group mapping table.
		$query->select('COUNT(map.group_id) AS group_count');
		$query->join('LEFT', '#__user_usergroup_map AS map ON map.user_id = a.id');
		$query->group('a.id');

		// Join over the user groups table.		
		$query->select('GROUP_CONCAT(DISTINCT g2.id SEPARATOR '.$db->Quote("-").') AS group_ids');
		$query->join('LEFT', '#__usergroups AS g2 ON g2.id = map.group_id');
		
		// Join with access levels
		$query->select('GROUP_CONCAT(DISTINCT l.level_id SEPARATOR '.$db->Quote("-").') AS level_ids');		
		$query->join('LEFT', '#__adminmenumanager_map AS l ON l.group_id = map.group_id ');
		
		// Filter the items over the group id if set.
		if ($groupId = $this->getState('filter.group_id')) {
			$query->join('LEFT', '#__user_usergroup_map AS map2 ON map2.user_id = a.id');
			$query->where('map2.group_id = '.(int) $groupId);
		}
		
		// Filter the items over the level id if set.
		if ($level_id = $this->getState('filter.level_id')) {
			$query->join('LEFT', '#__adminmenumanager_map AS map3 ON map3.group_id = map.group_id');
			$query->where('map3.level_id = '.(int) $level_id);
		}		

		// Filter the items over the search string if set.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search_id = (int)$search;
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.name LIKE '.$search.' OR a.username LIKE '.$search.' OR a.id = '.$search_id.')');				
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}
	
	function update_usergroup_levels_map(){
	
		$db = JFactory::getDBO();
		
		//empty table		
		//can not use TRUNCATE as there are sql-restrictions for that on some hosts	
		
		$query = $db->getQuery(true);
		$query->delete();
		$query->from('#__adminmenumanager_map');
		$query->where('id<>'.$db->q('0'));
		$db->setQuery((string)$query);
		$db->query();
		
		$accesslevels_array = array();
		
		//get accesslevels/usergroups				
		$query = $db->getQuery(true);
		$query->select('id, title, rules');
		$query->from('#__viewlevels');		
		$accesslevels = $db->setQuery($query);				
		$accesslevels = $db->loadObjectList();			
				
		foreach($accesslevels as $accesslevel){				
			$rules = $accesslevel->rules;
			$rules = str_replace('[','',$rules);
			$rules = str_replace(']','',$rules);
			$level_id = $accesslevel->id;
			$level_title = $accesslevel->title;			
			$usergroups_array = explode(',',$rules);			
			$accesslevels_array[] = array($level_id, $level_title, $usergroups_array);						
		}
		
		$query = $db->getQuery(true);
		$query->select('id, parent_id');
		$query->from('#__usergroups');		
		$usergroups = $db->setQuery($query);				
		$usergroups = $db->loadObjectList();	
						
		foreach($usergroups as $group){			
			$this->parent_groups = array($group->parent_id);
			$this->get_inherited_groups($group->parent_id, $usergroups);			
			$levels_inherited = $this->get_levels_from_group($group->id, $accesslevels_array);
			foreach($this->parent_groups as $parent_group){				
				$levels_inherited_temp = $this->get_levels_from_group($parent_group, $accesslevels_array);
				$levels_inherited = array_merge($levels_inherited, $levels_inherited_temp);
			}			
			$levels_inherited = array_unique($levels_inherited);			
			foreach($levels_inherited as $level_inherited){
				$level_title = '';
				for($n = 0; $n < count($accesslevels_array); $n++){			
					if($level_inherited==$accesslevels_array[$n][0]){
						$level_title = $accesslevels_array[$n][1];
					}
				}				
				
				$query = $db->getQuery(true);
				$query->insert('#__adminmenumanager_map');
				$query->set('group_id='.$db->q($group->id));
				$query->set('level_id='.$db->q($level_inherited));		
				$query->set('level_title='.$db->q($level_title));		
				$db->setQuery((string)$query);
				$db->query();
	
			}
		}			
	}
	
	function get_levels_from_group($group_id, $accesslevels_array){
		$levels = array();
		for($n = 0; $n < count($accesslevels_array); $n++){			
			if(in_array($group_id, $accesslevels_array[$n][2])){
				$levels[] = $accesslevels_array[$n][0];
			}
		}
		return $levels;
	}
	
	function get_inherited_groups($parent_id, $usergroups){			
		if($parent_id){
			foreach($usergroups as $group){	
				if($group->id==$parent_id && $group->parent_id){
					$this->parent_groups[] = $group->parent_id;
					$this->get_inherited_groups($group->parent_id, $usergroups);	
					break;			
				}
			}
		}		
	}
	
	
	
}
?>