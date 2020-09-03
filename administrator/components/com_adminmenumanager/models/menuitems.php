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

class adminmenumanagerModelMenuitems extends JModelList{	
	
	protected $option = 'com_adminmenumanager';	
	public $children = array();
	
	public function __construct($config = array()){
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',				
				'status', 'a.published',
				'ordering', 'a.ordertotal',	
				'access', 'gl.title',	
				'type', 'a.type'				
			);
		}
		parent::__construct($config);
	}	
	
	protected function populateState($ordering = NULL, $direction = NULL){	
		
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$menu = $app->getUserStateFromRequest($this->context.'.filter.menu', 'filter_menu');			
		if($menu==''){
			$menu = $this->get_default_menu();
		}			
		$this->setState('filter.menu', $menu);
		
		$level = $app->getUserStateFromRequest($this->context.'.filter.level', 'filter_level');
		$this->setState('filter.level', $level);
		
		$filter_parent = $app->getUserStateFromRequest($this->context.'.filter.parent', 'filter_parent');
		$this->setState('filter.parent', $filter_parent);
		
		$state = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_state');
		$this->setState('filter.state', $state);

		// List state information.		
		parent::populateState('a.ordertotal', 'asc');
	}
	
	function get_default_menu(){
		
		$db = JFactory::getDBO();
		
		//get the first menu			
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__adminmenumanager_menus');
		$query->order('ordering');			
		$menus = $db->setQuery($query, 0, 1);				
		$menus = $db->loadObjectList();									
		foreach($menus as $temp){				
			$menu = $temp->id;				
		}	
		return $menu;
	}
	
	protected function getStoreId($id = ''){
	
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.menu');		
		$id	.= ':'.$this->getState('filter.title');		
		$id	.= ':'.$this->getState('filter.state');	

		return parent::getStoreId($id);
	}
	
	protected function getListQuery(){		
		
		$db = JFactory::getDBO();
		$helper = $this->get_helper();
		$amm_config = $helper->amm_config;
		
		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'							
			)
		);
		$query->from('`#__adminmenumanager_menuitems` AS a');
		
		//join with groups to get group title
		$query->select('gl.title AS gltitle');
		if($amm_config['based_on']=='group'){
			$query->join('LEFT', '#__usergroups AS gl ON gl.id = a.accessgroup');
		}else{
			$query->join('LEFT', '#__viewlevels AS gl ON gl.id = a.accesslevel');
		}

		// Filter the items over the search string if set.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search_id = (int)$search;
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.id = '.$search_id.')');				
			}
		}
		
		// filter menu type
		if ($menu = $this->getState('filter.menu')) {		
			$query->where('a.menu = '.$db->quote($menu));
		}
		
		//filter max level
		if ($level = $this->getState('filter.level')) {
			$query->where('a.level <= '.(int) $level);
		}
		
		//filter parent
		if ($parent = $this->getState('filter.parent')) {
			$this->get_menuitem_children((int)$parent);			
			$menuitem_children = implode(',',$this->children);
			if($menuitem_children){
				$query->where('a.id IN ('.$menuitem_children.')');	
			}					
		}
		
		// filter state
		$state = $this->getState('filter.state');
		if(is_numeric($state)){
			$query->where('a.published = '.$db->quote($state));
		}
				
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		//echo nl2br($query);
		
		return $query;
	}	
	
	function get_helper(){
		$ds = DIRECTORY_SEPARATOR;
		require_once(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_adminmenumanager'.$ds.'helpers'.$ds.'adminmenumanager.php');
		$helper = new adminmenumanagerHelper();
		return $helper;
	}
	
	function get_menuitem_children($parentid){
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__adminmenumanager_menuitems');
		$query->where('parentid='.$db->q($parentid));
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();
			
		foreach($rows as $row){				
			$this->children[] = $row->id;
			$this->get_menuitem_children($row->id);
		}			
	}
	
	
}
?>