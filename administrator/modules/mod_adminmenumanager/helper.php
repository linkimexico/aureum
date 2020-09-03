<?php
/**
* @package Admin-Menu-Manager (com_adminmenumanager)
* @version 2.3.0
* @copyright Copyright (C) 2012 - 2018 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/



// no direct access.
defined('_JEXEC') or die;

class ModAdminMenuManagerHelper{
	
	public $menu_items;
	public $menu_items_filtered;
	public $previous_item;
	public $levels = 'false';
	protected $total;
	public $param_disable;
	public $version;
	public $style;
	public $adminmenumanager_break_left;
	public $adminmenumanager_break_right;

	function get_menu_items($params){
	
		$db = JFactory::getDBO();
		$this->param_disable = $params->get('adminmenumanagerdisable', '');
		$amm_config = $this->get_config();		
		$r = strlen($this->levels);
		$query = $db->getQuery(true);
		$query->select('id, title, icon, url, level, parentid, type, target, width, height');
		$query->from('#__adminmenumanager_menuitems');
		$query->where('menu='.$params->get('adminmenumanagermenu', ''));
		$query->where('published=1');		
		$query->order('ordertotal');	
		$menuitems = $db->setQuery($query, 0, $r);				
		$menuitems = $db->loadObjectList();
		
		//reset in case there is more then 1 menu on the page
		$this->menu_items_filtered = array();
		$this->previous_item = 0;
		$this->menu_items = $menuitems;			
		$this->look_for_children(0);
		return $this->menu_items_filtered;	
	}
	
	function look_for_children($parent){
		$enabled = JRequest::getInt('hidemainmenu') ? false : true;							
		foreach($this->menu_items as $menuitem){
			if($menuitem->parentid==$parent && $this->total<=4){			
				$menuitem->levels = 0;
				$menuitem->up = 0;	
				$menuitem->down = 0;				
				
				if(isset($this->menu_items_filtered[$this->previous_item])){
					$this->menu_items_filtered[$this->previous_item]->levels = ($this->menu_items_filtered[$this->previous_item]->level - $menuitem->level);
					$this->menu_items_filtered[$this->previous_item]->up = ($menuitem->level < $this->menu_items_filtered[$this->previous_item]->level);
					$this->menu_items_filtered[$this->previous_item]->down	= ($menuitem->level > $this->menu_items_filtered[$this->previous_item]->level);					
				}	
				
				$this->previous_item = $menuitem->id;
				$menuitem->title = htmlspecialchars($menuitem->title);
				if(!$enabled && $this->param_disable){
					$menuitem->url = '#';
				}
				if($menuitem->url=='my-profile'){
					$user = JFactory::getUser();
					$user_id = $user->get('id');
					$menuitem->url = 'index.php?option=com_admin&task=profile.edit&id='.$user_id;
				}
				if($menuitem->url=='logout'){					
					$temp_url = 'index.php?option=com_login&task=logout&';
					$version = new JVersion;
					if($version->RELEASE >= '3.0'){
						$temp_url .= JSession::getFormToken();
					}else{
						$temp_url .= JUtility::getToken();
					}
					$temp_url .= '=1';				
					$menuitem->url = $temp_url;
				}
				$this->menu_items_filtered[$menuitem->id] = $menuitem;
				$this->total = $this->total+1;
				if($enabled || (!$enabled && !$this->param_disable)){
					$this->look_for_children($menuitem->id);
				}
			}
		}
		if(isset($this->menu_items_filtered[$this->previous_item])){
			$this->menu_items_filtered[$this->previous_item]->levels = ($this->menu_items_filtered[$this->previous_item]->level - 1);
			$this->menu_items_filtered[$this->previous_item]->up = (1 < $this->menu_items_filtered[$this->previous_item]->level);
			$this->menu_items_filtered[$this->previous_item]->down = (1 > $this->menu_items_filtered[$this->previous_item]->level);			
		}		
	}
	
	function get_config(){	
			
		$db = JFactory::getDBO();			
		
		$db->setQuery("SELECT config "
		."FROM #__adminmenumanager_config "
		."WHERE id='amm' "
		."LIMIT 1"
		);		
		$raw = $db->loadResult();	
		
		$registry = new JRegistry;
		$registry->loadString($raw);
		$config = $registry->toArray();	
			
		return $config;			
	}
	
	function has_module_before_or_after($id, $before_or_after){
		
		$db = JFactory::getDBO();
		
		if($before_or_after=='before'){
			$ordering_direction = 'ASC';
		}else{
			$ordering_direction = 'DESC';
		}
		
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__modules');
		$query->where('position='.$db->q('menu'));
		$query->where('published='.$db->q('1'));
		$query->where('client_id='.$db->q('1'));
		$query->order('ordering '.$ordering_direction);
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();
		
		$has_module_before_or_after = 0;
		foreach($rows as $row){	
			$temp_id = $row->id;	
			if($temp_id==$id){
				break;
			}
			$has_module_before_or_after = 1;			
		}	
		return $has_module_before_or_after;
	}
	
	function do_init($params, $module){
	
		$app = JFactory::getApplication();
		$ds = DIRECTORY_SEPARATOR;
		
		$template_object = $app->getTemplate(true);		
		$template = $template_object->template;		
		$this->version = new JVersion;
						
		//style
		if($this->version->RELEASE >= '3.0'){
			$style = 'isis';
		}else{
			$style = 'bluestork';
		}	
		if($params->get('ammstyle')){
			$style = $params->get('ammstyle', '');
		}
		if($style=='auto'){		
			if(file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'modules'.$ds.'mod_adminmenumanager'.$ds.'css'.$ds.$template.'.css')){
				$style = $template;
			}else{
				if($this->version->RELEASE >= '3.0'){
					$style = 'isis';
				}else{
					$style = 'bluestork';
				}
			}
		}
		$this->style = $style;
		
		
		//allow left
		$adminmenumanager_break_left = 0;
		if($params->get('allow_left', '')=='2'){		
			$adminmenumanager_break_left = 1;
			if(!$this->has_module_before_or_after($module->id, 'before')){
				//don't break left if there is no module to break with
				$adminmenumanager_break_left = 0;	
			}
		}	
		$this->adminmenumanager_break_left = $adminmenumanager_break_left;
		
		//allow right
		$adminmenumanager_break_right = 0;
		if($params->get('allow_right', '')=='2'){		
			$adminmenumanager_break_right = 1;
			if(!$this->has_module_before_or_after($module->id, 'after')){
				//don't break right if there is no module to break with
				$adminmenumanager_break_right = 0;	
			}
		}	
		$this->adminmenumanager_break_right = $adminmenumanager_break_right;
		
	}
	
	

}

?>