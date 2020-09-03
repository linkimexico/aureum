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

class adminmenumanagerViewMenuitems extends JViewLegacy{
	
	protected $items;	
	protected $state;
	protected $pagination;		
	
	public function display($tpl = null){	
		
		$ds = DIRECTORY_SEPARATOR;
		
		$controller = new adminmenumanagerController();	
		$this->assignRef('controller', $controller);
		
		$helper = $controller->get_helper();
		$this->assignRef('helper', $helper);//needed for free version
				
		$this->state = $this->get('State');	
		$this->items = $this->get('Items');			
		$this->pagination = $this->get('Pagination');
		
		//include languages
		$lang = JFactory::getLanguage();
		$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_languages', JPATH_ADMINISTRATOR, null, false);
				
		$menus_options = $this->get_menus_options();	
		$this->assignRef('menus_options', $menus_options);
		
		$menuitems_total = $this->get_menuitems_total();	
		$this->assignRef('menuitems_total', $menuitems_total);		
		
		$last_items = $this->get_last_items_array();	
		$this->assignRef('last_items', $last_items);
		
		$access_via_accessmanager = $helper->check_if_access_via_accessmanager();
		$this->assignRef('access_via_accessmanager', $access_via_accessmanager);
		
		$groups_levels = $helper->get_groups_levels();	
		$this->assignRef('groups_levels', $groups_levels);	
		
		$default_access_type = 'default_access_'.$controller->amm_config['based_on'];
		$this->assignRef('default_access_type', $default_access_type);	
		
		$menu = $this->state->get('filter.menu');
		if($menu==''){
			$menu = $this->get_default_menu();
		}	
		$menuitems = $helper->get_menuitems($menu, 0);	
		$menuitems_nested = $this->menuitems_nested($menuitems);
		$this->assignRef('menuitems_nested', $menuitems_nested);
		
		$last_menuitem = $this->get_last_menuitem($menu);	
		$this->assignRef('last_menuitem', $last_menuitem);
		
		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as $item){
			$this->ordering[$item->parentid][] = $item->id;
		}
		
		// Levels filter.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('J1'));
		$options[]	= JHtml::_('select.option', '2', JText::_('J2'));
		$options[]	= JHtml::_('select.option', '3', JText::_('J3'));
		$options[]	= JHtml::_('select.option', '4', JText::_('J4'));
		$options[]	= JHtml::_('select.option', '5', JText::_('J5'));
		$options[]	= JHtml::_('select.option', '6', JText::_('J6'));
		$options[]	= JHtml::_('select.option', '7', JText::_('J7'));
		$options[]	= JHtml::_('select.option', '8', JText::_('J8'));
		$options[]	= JHtml::_('select.option', '9', JText::_('J9'));
		$options[]	= JHtml::_('select.option', '10', JText::_('J10'));

		$this->assign('filter_levels', $options);
		
		//toolbar	
		JToolBarHelper::custom('menuitem','new.png','new_f2.png',JText::_('JTOOLBAR_NEW'),false,false);	
		JToolBarHelper::divider();
		JToolBarHelper::custom('menuitems_copy','copy.png','copy_f2.png',JText::_('JLIB_HTML_BATCH_COPY'),false,false);			
		JToolBarHelper::publish('menuitems_publish', 'JTOOLBAR_PUBLISH', true);
		JToolBarHelper::unpublish('menuitems_unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();	
		JToolBarHelper::custom( 'menuitems_import', 'amm_import', 'import', JText::_('COM_ADMINMENUMANAGER_IMPORT'), false, false );
		JToolBarHelper::custom( 'menuitems_export', 'amm_export', 'export', JText::_('JTOOLBAR_EXPORT'), false, false );	
		JToolBarHelper::divider();		
		JToolBarHelper::custom('menuitem_delete','delete.png','delete_f2.png',JText::_('JTOOLBAR_DELETE'),false,false);							

		if($helper->joomla_version >= '3.0'){
			//sidebar
			$this->add_sidebar($controller);	
		}
		
		parent::display($tpl);
	}
	
	function get_menus_options(){	
						
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('id, name');
		$query->from('#__adminmenumanager_menus');	
		$query->order('ordering');	
		$menus = $db->setQuery((string)$query);				
		$menus = $db->loadObjectList();	
		
		$options = array();
		foreach($menus as $menu){
			$options[] = JHtml::_('select.option', $menu->id, $menu->name);					
		}	
			
		return $options;		
	}
	
	function get_menuitems_total(){
		
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__adminmenumanager_menuitems');		
		$menuitems = $db->setQuery((string)$query);				
		$menuitems = $db->loadColumn();
		$menuitems_total = count($menuitems);
		return $menuitems_total;
	}
	
	function get_last_items_array(){	
		
		$db = JFactory::getDBO();
		
		//get all menuitems from this menu
		$query = $db->getQuery(true);
		$query->select('id, parentid, level');
		$query->from('#__adminmenumanager_menuitems');
		//$query->where('menu='.$this->state->get('filter.menu'));		
		$query->order('ordertotal');
		$items = $db->setQuery($query);				
		$items = $db->loadObjectList();	
		
		//get array of parents
		$parents = array(0);//0 is root
		foreach($items as $item){
			$parents[] = $item->parentid;
		}		
		$parents = array_unique($parents);			
		
		//loop parents to get last item of each parent
		$last_items = array();
		foreach($parents as $parent){
			$last_item = 0;
			foreach($items as $item){
				if($item->parentid==$parent){
					$last_item = $item->id;
				}
			}
			$last_items[] = $last_item;
		}		
		return $last_items;
	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_adminmenumanager&view=menuitems');			
				
		$helper->add_submenu();			
		
		JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_ADMINMENUMANAGER_MENU').' -',
			'filter_menu',
			JHtml::_('select.options', $this->menus_options, 'value', 'text', $this->state->get('filter.menu'))
		);
		
		JHtmlSidebar::addFilter(
			'- '.JText::_('JSELECT').' '.JText::_('COM_MENUS_FIELD_VALUE_PARENT').' -',
			'filter_parent',
			JHtml::_('select.options', $this->menuitems_nested, 'value', 'text', $this->state->get('filter.parent'))
		);
		
		JHtmlSidebar::addFilter(
			JText::_('COM_MENUS_OPTION_SELECT_LEVEL'),
			'filter_level',
			JHtml::_('select.options', $this->filter_levels, 'value', 'text', $this->state->get('filter.level'))
		);	
		
		JHtmlSidebar::addFilter(
			'- '.JText::_('JLIB_HTML_SELECT_STATE').' -',
			'filter_state',
			JHtml::_('select.options', $this->getStateOptions(), 'value', 'text', $this->state->get('filter.state'))
		);
				
		$this->sidebar = JHtmlSidebar::render();
	}
	
	protected function getSortFields(){
	
		return array(
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'a.published' => JText::_('JSTATUS'),
			'a.ordertotal' => JText::_('JGRID_HEADING_ORDERING'),
			'gl.title' => JText::_('JFIELD_ACCESS_LABEL'),
			'a.type' => JText::_('COM_INSTALLER_HEADING_TYPE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
	
	static function getStateOptions(){
		$options	= array();
		$options[]	= JHtml::_('select.option',	'1',	JText::_('JPUBLISHED'));
		$options[]	= JHtml::_('select.option',	'0',	JText::_('JUNPUBLISHED'));		
		return $options;
	}
	
	function menuitems_nested($menuitems){
		
		$options = array();
		foreach($menuitems as $menuitem){			
			$levels =  str_repeat("- ", $menuitem->level);
			$options[]	= JHtml::_('select.option',	$menuitem->id, $levels.' '.$menuitem->title);			
		}
		return $options;
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
	
	
	function get_last_menuitem($menu){
	
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__adminmenumanager_menuitems');	
		$query->where('menu='.$db->q($menu));
		$query->order('ordertotal desc');		
		$menuitems = $db->setQuery($query, 0, 1);
		$menuitems = $db->loadObjectList();
								
		$last_menuitem = 0;
		foreach($menuitems as $temp){				
			$last_menuitem = $temp->id;				
		}	
		
		return $last_menuitem;
	}
	
	
	
	
	
	
	
	
}
?>