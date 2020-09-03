<?php
/**
* @package Admin-Menu-Manager (com_adminmenumanager)
* @version 2.3.2
* @copyright Copyright (C) 2012 - 2019 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/



// no direct access
defined('_JEXEC') or die('Restricted access');

class adminmenumanagerViewUsers extends JViewLegacy{
	
	protected $items;	
	protected $state;
	protected $pagination;	
	protected $user_index;
	protected $group_level_index;
	
	public function display($tpl = null){	
		
		$controller = new adminmenumanagerController();	
		$this->assignRef('controller', $controller);
		
		$helper = $controller->get_helper();
		$this->assignRef('helper', $helper);
				
		$this->state		= $this->get('State');	
		$this->items		= $this->get('Items');			
		$this->pagination = $this->get('Pagination');	
		$this->user_index = $this->get_userindex($this->items);
		$this->group_level_index = $this->get_group_level_index();		
		
		$groups_title_order_back = $this->get_groups_title_order();
		$this->assignRef('groups_title_order_back', $groups_title_order_back);
		
		//get levels in order
		$levels_title_order = $this->get_levels_title_order();
		$this->assignRef('levels_title_order', $levels_title_order);
		
		//include mod_menu language. Reuse or die ;-)#
		$lang = JFactory::getLanguage();
		$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);				
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);	
		
		//toolbar	
		JToolBarHelper::custom( 'users_export', 'amm_export', 'export', $this->controller->amm_strtolower(JText::_('JTOOLBAR_EXPORT')).' .csv', false, false );			

		if($helper->joomla_version >= '3.0'){
			//sidebar
			$this->add_sidebar($controller);	
		}
		
		parent::display($tpl);
	}
	
	
	function get_groups(){
		$database = JFactory::getDBO();
		$database->setQuery(
			'SELECT a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level' .
			' FROM #__usergroups AS a' .
			' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' GROUP BY a.id' .
			' ORDER BY a.lft ASC'
		);
		$groups = $database->loadObjectList();
		foreach ($groups as &$group) {
			$group->text = str_repeat('- ',$group->level).$group->text;
		}
		return $groups;
	}
	
	function get_levels(){
		$database = JFactory::getDBO();
		$database->setQuery("SELECT id AS value, title AS text "
		."FROM #__viewlevels "
		."ORDER BY ordering ASC "		
		);
		$accesslevels = $database->loadObjectList();
		return $accesslevels;		
	}
	
	function get_groups_title_order(){
		$database = JFactory::getDBO();
		$database->setQuery(
			"SELECT a.id AS group_id, a.title AS group_title ".
			"FROM #__usergroups AS a ".				
			"ORDER BY a.title ASC "
		);
		$groups = $database->loadObjectList();	
		$groups_array = array();
		foreach($groups as $group){
			$groups_title_order[] = array($group->group_id, $group->group_title);
		}	
		return $groups_title_order;
	}
	
	function get_levels_title_order(){
		$database = JFactory::getDBO();
		$database->setQuery(
			"SELECT a.id AS level_id, a.title AS level_title ".
			"FROM #__viewlevels AS a ".				
			"ORDER BY a.title ASC "
		);
		$levels_title_order = $database->loadObjectList();				
		return $levels_title_order;
	}
	
	function get_users_groups($user_id){
		$groups = array();
		foreach($this->user_index as $user_group_row){
			if($user_id==$user_group_row->user_id){
				$groups[] = $user_group_row->group_id;
			}
		}
		return $groups;
	}
	
	static function get_userindex($current_users){
	
		$db = JFactory::getDBO();
		
		//only get those users we need for performance
		$user_id_string = '0';		
		foreach($current_users as $users){						
			$user_id_string .= ','.$users->id;			
		}
		
		$query = $db->getQuery(true);
		$query->select('user_id, group_id');
		$query->from('#__user_usergroup_map');		
		$query->where('user_id IN ('.$user_id_string.')');
		$users_usergroups = $db->setQuery($query);				
		$users_usergroups = $db->loadObjectList();
			
		return $users_usergroups;		
	}
	
	static function get_group_level_index(){
	
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('group_id, level_id, level_title');
		$query->from('#__adminmenumanager_map');		
		$group_level_index = $db->setQuery($query);				
		$group_level_index = $db->loadObjectList();	
		
		return $group_level_index;	
	}
	
	function get_groups_levels($groups){
		$levels = array();		
		foreach($this->group_level_index as $group_level_row){
			if(in_array($group_level_row->group_id, $groups)){
				$levels[] = $group_level_row->level_id;
			}
		}
		$levels = array_unique($levels);
		return $levels;
	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_adminmenumanager&view=users');			
				
		$helper->add_submenu();		
		
		JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('JLIB_RULES_GROUPS').' -',
			'filter_group_id',
			JHtml::_('select.options', $this->get_groups(), 'value', 'text', $this->state->get('filter.group_id'))
		);
		
		JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('MOD_MENU_COM_USERS_LEVELS').' -',
			'filter_level_id',
			JHtml::_('select.options', $this->get_levels(), 'value', 'text', $this->state->get('filter.level_id'))
		);	
		
		$this->sidebar = JHtmlSidebar::render();
	}
	
	protected function getSortFields(){
	
		return array(
			'a.name' => JText::_('COM_USERS_HEADING_NAME'),
			'a.username' => JText::_('JGLOBAL_USERNAME'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
	
	
}
?>