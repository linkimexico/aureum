<?php
/**
* @package Admin-Menu-Manager (com_adminmenumanager)
* @version 2.3.0
* @copyright Copyright (C) 2012 - 2018 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/


// no direct access
defined('_JEXEC') or die;

class adminmenumanagerHelper{	

	public $amm_config;
	public $backend_usergroups = array(8);
	public $template;
	public $menu_items;
	public $menu_items_filtered = array();
	public $menu_item_id;	
	public $components_array_parents = array();
	public $components_array_children = array();
	public $joomla_version;
	
	function __construct(){	
	
		$app = JFactory::getApplication();
		
		$this->amm_config = $this->get_config();
		
		$template_object = $app->getTemplate(true);		
		$this->template = $template_object->template;	
		
		$version = new JVersion;
		$this->joomla_version = $version->RELEASE;		
	}	
	
	function get_config(){	
			
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('config');
		$query->from('#__adminmenumanager_config');		
		$query->where('id='.$db->q('amm'));		
		$raw = $db->setQuery($query, 0, 1);				
		$raw = $db->loadResult();
		
		$registry = new JRegistry;
		$registry->loadString($raw);
		$config = $registry->toArray();	
			
		return $config;			
	}
	
	function get_groups_levels(){	
						
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		if($this->amm_config['based_on']=='level'){
			$query->select('id, title');
			$query->from('#__viewlevels');
			$query->order($this->amm_config['level_sort']);	
		}else{
			$query->select('a.id as id, a.title as title, COUNT(DISTINCT b.id) AS hyrarchy');
			$query->from('#__usergroups AS a');
			$query->leftJoin('#__usergroups AS b ON a.lft > b.lft AND a.rgt < b.rgt');
			$this->get_backend_usergroups();
			$backend_usergroups = implode(',', $this->backend_usergroups);
			$query->where('a.id in ('.$backend_usergroups.')');			
			$query->group('a.id');
			$query->order('a.lft');			
		}	
		$groups_levels = $db->setQuery((string)$query);				
		$groups_levels = $db->loadObjectList();			
		
		return $groups_levels;
	}
	
	function get_backend_usergroups(){
	
		$db = JFactory::getDBO();
		
		//get main asset		
		$query = $db->getQuery(true);
		$query->select('rules');
		$query->from('#__assets');		
		$query->where('id='.$db->q('1'));		
		$asset = $db->setQuery($query);				
		$asset = $db->loadResult();	
		
		//make into array
		$registry = new JRegistry;
		$registry->loadString($asset);
		$asset_array = $registry->toArray();
		
		//get configured backend groups			
		$temp = $asset_array['core.login.admin'];
		foreach($temp as $k => $v){		  
			if($v=='1'){							
				$this->set_backend_usergroup($k);
			}
		}
		
		//get groups inherited from super user	
		$temp = $asset_array['core.admin'];			
		foreach($temp as $k => $v){		  
			if($v=='1'){							
				$this->set_backend_usergroup($k);
			}
		}
	}
	
	function set_backend_usergroup($group){
	
		$db = JFactory::getDBO();
			
		$this->backend_usergroups[] = $group;	

		//get child groups				
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__usergroups');
		$query->where('parent_id='.$group);
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();
			
		foreach($rows as $row){		
			//recurse to get all children
			$this->set_backend_usergroup($row->id);
		}	
	}
	
	function check_if_access_via_accessmanager(){
	
		$db = JFactory::getDBO();
		$access_via_accessmanager = 0;
		$ds = DIRECTORY_SEPARATOR;
		
		if(file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'controller.php')){
			//access-manager is installed
						
			//get am config
			$query = $db->getQuery(true);
			$query->select('config');
			$query->from('#__accessmanager_config');
			$query->where('id='.$db->q('am'));			
			$rows = $db->setQuery($query);				
			$rows = $db->loadObjectList();				
			
			//check am config if am is active and if amm rights are active
			//check if set first in case table does not exists
			if(isset($rows[0])){
				foreach($rows as $row){
					if(strpos($row->config, '"am_enabled":1') && strpos($row->config, '"adminmenumanager_active":"true"')){
						//check access-manager version type
						$file = JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'controller.php';
						if (!$fp = @fopen($file, "r")) {		
							echo 'can not read from access manager controller file';
							exit;
						}						
						$lines = fread($fp, 1500);																
						fclose($fp);
						if(!strpos($lines, '$am_version_type = \'free\';')){
							$access_via_accessmanager = 1;
						}
					}
				}	
			}	
		}
		
		return $access_via_accessmanager;
	}
	
	function get_joomla_menuitems(){
	
		//include languages
		$lang = JFactory::getLanguage();		
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);
		$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);
	
		$menuitems = array();		
		// id, title, icon, menu, url, published, parentid, level, ordering, ordertotal, accessgroup, accesslevel, type, target, width, height
		// title, icon, url, parentid, level, type
		
		//site
		$n = 1;
		if($this->joomla_version >= '3.0'){
			$system_header = JText::_('MOD_MENU_SYSTEM');
			$system_header_constant = 'MOD_MENU_SYSTEM';
		}else{
			$system_header = JText::_('JSITE');
			$system_header_constant = 'JSITE';
		}
		$menuitems[$n] = array($system_header, '', '#', 0, 1, 0, $system_header_constant,0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_CONTROL_PANEL'), $this->icon('cpanel'), 'index.php', 1, 2, 0, 'MOD_MENU_CONTROL_PANEL',0);
		$n++;
		if($this->joomla_version < '3.0'){
			$menuitems[$n] = array(JText::_('MOD_MENU_USER_PROFILE'), $this->icon('user'), 'my-profile', 1, 2, 0, 'MOD_MENU_USER_PROFILE', 0);
			$n++;
		}
		if($this->joomla_version >= '3.0'){
			$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', 1, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
			$n++;
		}
		$menuitems[$n] = array(JText::_('MOD_MENU_CONFIGURATION'), $this->icon('config'), 'index.php?option=com_config', 1, 2, 0, 'MOD_MENU_CONFIGURATION',0);
		$n++;
		if($this->joomla_version >= '3.0'){
			$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', 1, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
			$n++;
		}
		if($this->joomla_version < '3.0'){
			$menuitems[$n] = array(JText::_('MOD_MENU_MAINTENANCE'), $this->icon('maintenance'), '#', 1, 2, 0, 'MOD_MENU_MAINTENANCE',0);
			$maintenance_parent = $n;
			$maintenance_level = 3;
			$n++;			
		}else{
			$maintenance_parent = 1;
			$maintenance_level = 2;
		}
		$menuitems[$n] = array(JText::_('MOD_MENU_GLOBAL_CHECKIN'), $this->icon('checkin'), 'index.php?option=com_checkin', $maintenance_parent, $maintenance_level, 0, 'MOD_MENU_GLOBAL_CHECKIN',0);
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', $maintenance_parent, $maintenance_level, 2, 'COM_ADMINMENUMANAGER_LINE',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_CLEAR_CACHE'), $this->icon('clear'), 'index.php?option=com_cache', $maintenance_parent, $maintenance_level, 0, 'MOD_MENU_CLEAR_CACHE',0);
		$n++;		
		$menuitems[$n] = array(JText::_('MOD_MENU_PURGE_EXPIRED_CACHE'), $this->icon('purge'), 'index.php?option=com_cache&view=purge', $maintenance_parent, $maintenance_level, 0, 'MOD_MENU_PURGE_EXPIRED_CACHE',0);
		$n++;
		if($this->joomla_version >= '3.0'){
			$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', 1, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
			$n++;
		}
		$menuitems[$n] = array(JText::_('MOD_MENU_SYSTEM_INFORMATION'), $this->icon('info'), 'index.php?option=com_admin&view=sysinfo', 1, 2, 0, 'MOD_MENU_SYSTEM_INFORMATION',0);
		$n++;
		if($this->joomla_version < '3.0'){
			$menuitems[$n] = array(JText::_('MOD_MENU_LOGOUT'), $this->icon('logout'), 'logout', 1, 2, 0, 'MOD_MENU_LOGOUT',0);
			$n++;
		}
		
		//users
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_USERS_USERS'), '', '#', 0, 1, 0, 'MOD_MENU_COM_USERS_USERS',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_USERS_USER_MANAGER'), $this->icon('user'), 'index.php?option=com_users&view=users', 12, 2, 0, 'MOD_MENU_COM_USERS_USER_MANAGER',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_USERS_ADD_USER'), $this->icon('newarticle'), 'index.php?option=com_users&view=user&layout=edit', 13, 3, 0, 'MOD_MENU_COM_USERS_ADD_USER',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_USERS_GROUPS'), $this->icon('groups'), 'index.php?option=com_users&view=groups', 12, 2, 0, 'MOD_MENU_COM_USERS_GROUPS',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_USERS_ADD_GROUP'), $this->icon('newarticle'), 'index.php?option=com_users&view=group&layout=edit', 15, 3, 0, 'MOD_MENU_COM_USERS_ADD_GROUP',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_USERS_LEVELS'), $this->icon('levels'), 'index.php?option=com_users&view=levels', 12, 2, 0, 'MOD_MENU_COM_USERS_LEVELS',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_USERS_ADD_LEVEL'), $this->icon('newarticle'), 'index.php?option=com_users&view=level&layout=edit', 17, 3, 0, 'MOD_MENU_COM_USERS_ADD_LEVEL',0);
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', 12, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
		$n++;
		/*new*/
		$menuitems[$n] = array(JText::_('MOD_MENU_FIELDS'), $this->icon('fields'), 'index.php?option=com_fields&context=com_users.user', 12, 2, 0, 'MOD_MENU_FIELDS',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_FIELDS_GROUP'), $this->icon('fields'), 'index.php?option=com_fields&view=groups&context=com_users.user', 12, 2, 0, 'MOD_MENU_FIELDS_GROUP',0);
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', 12, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_USERS_NOTES'), $this->icon('user-note'), 'index.php?option=com_users&view=notes', 12, 2, 0, 'MOD_MENU_COM_USERS_NOTES',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_USERS_ADD_NOTE'), $this->icon('newarticle'), 'index.php?option=com_users&view=note&layout=edit', 20, 3, 0, 'MOD_MENU_COM_USERS_ADD_NOTE',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_USERS_NOTE_CATEGORIES'), $this->icon('category'), 'index.php?option=com_categories&view=categories&extension=com_users.notes', 12, 2, 0, 'MOD_MENU_COM_USERS_NOTE_CATEGORIES',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_CONTENT_NEW_CATEGORY'), $this->icon('newarticle'), 'index.php?option=com_categories&view=category&layout=edit&extension=com_users', 22, 3, 0, 'MOD_MENU_COM_CONTENT_NEW_CATEGORY',0);
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', 12, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
		$n++;
		/*new*/
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_PRIVACY'), $this->icon('privacy'), 'index.php?option=com_privacy', 12, 2, 0, 'MOD_MENU_COM_PRIVACY',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_ACTIONLOGS'), $this->icon('action'), 'index.php?option=com_actionlogs', 12, 2, 0, 'MOD_MENU_COM_ACTIONLOGS',0);
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', 12, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);		
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', 12, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
		$menuitems[$n] = array(JText::_('MOD_MENU_MASS_MAIL_USERS'), $this->icon('massmail'), 'index.php?option=com_users&view=mail', 12, 2, 0, 'MOD_MENU_MASS_MAIL_USERS',0);
		$n++;
		
		//menus
		$menuitems[$n] = array(JText::_('MOD_MENU_MENUS'), '', '#', 0, 1, 0, 'MOD_MENU_MENUS',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_MENU_MANAGER'), $this->icon('menumgr'), 'index.php?option=com_menus&view=menus', 26, 2, 0, 'MOD_MENU_MENU_MANAGER',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_MENU_MANAGER_NEW_MENU'), $this->icon('newarticle'), 'index.php?option=com_menus&view=menu&layout=edit', 27, 3, 0, 'MOD_MENU_MENU_MANAGER_NEW_MENU',0);
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', 26, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);	
		$n++;		
		$joomla_menus = $this->get_joomla_menus();
		foreach($joomla_menus as $joomla_menu){			
			$menuitems[$n] = array($joomla_menu->title, $this->icon('menu'), 'index.php?option=com_menus&view=items&menutype='.$joomla_menu->menutype, 26, 2, 0, '',0);
			$n++;
			$menuitems[$n] = array(JText::_('MOD_MENU_MENU_MANAGER_NEW_MENU'), $this->icon('newarticle'), 'index.php?option=com_menus&view=item&layout=edit&menutype='.$joomla_menu->menutype, $n-1, 3, 0, 'MOD_MENU_MENU_MANAGER_NEW_MENU',0);
			$n++;
		}
		
		//content
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_CONTENT'), '', '#', 0, 1, 0, 'MOD_MENU_COM_CONTENT',0);
		$c = $n;
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_CONTENT_ARTICLE_MANAGER'), $this->icon('article'), 'index.php?option=com_content', $c, 2, 0, 'MOD_MENU_COM_CONTENT_ARTICLE_MANAGER',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_CONTENT_NEW_ARTICLE'), $this->icon('newarticle'), 'index.php?option=com_content&view=article&layout=edit', $n-1, 3, 0, 'MOD_MENU_COM_CONTENT_NEW_ARTICLE',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_CONTENT_CATEGORY_MANAGER'), $this->icon('category'), 'index.php?option=com_categories&extension=com_content', $c, 2, 0, 'MOD_MENU_COM_CONTENT_CATEGORY_MANAGER',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_CONTENT_NEW_CATEGORY'), $this->icon('newarticle'), 'index.php?option=com_categories&view=category&layout=edit&extension=com_content', $n-1, 3, 0, 'MOD_MENU_COM_CONTENT_NEW_CATEGORY',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_COM_CONTENT_FEATURED'), $this->icon('featured'), 'index.php?option=com_content&view=featured', $c, 2, 0, 'MOD_MENU_COM_CONTENT_FEATURED',0);
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', $c, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
		$n++;
		/*new*/
		$menuitems[$n] = array(JText::_('MOD_MENU_FIELDS'), $this->icon('fields'), 'index.php?option=com_fields&context=com_content.article', 12, 2, 0, 'MOD_MENU_FIELDS',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_FIELDS_GROUP'), $this->icon('fields'), 'index.php?option=com_fields&view=groups&context=com_content.article', 12, 2, 0, 'MOD_MENU_FIELDS_GROUP',0);
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', 12, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_MEDIA_MANAGER'), $this->icon('media'), 'index.php?option=com_media', $c, 2, 0, 'MOD_MENU_MEDIA_MANAGER',0);
		$n++;
		
		//components
		$menuitems[$n] = array(JText::_('MOD_MENU_COMPONENTS'), '', '#', 0, 1, 0, 'MOD_MENU_COMPONENTS',0);
		$co = $n;
		$n++;		
		$this->get_components();		
		$components_array_parents = $this->components_array_parents;
		$components_array_children = $this->components_array_children;
		for($c = 0; $c < count($this->components_array_parents); $c++){			
			$menuitems[$n] = array(htmlspecialchars($this->components_array_parents[$c][4]), $this->components_array_parents[$c][3], $this->components_array_parents[$c][2], $co, 2, 0, htmlspecialchars($this->components_array_parents[$c][5]),0);
			$p = $n;
			$n++;						
			for($m = 0; $m < count($this->components_array_children); $m++){
				if($this->components_array_children[$m][5]==$this->components_array_parents[$c][0]){					
					$menuitems[$n] = array(htmlspecialchars($this->components_array_children[$m][4]), $this->components_array_children[$m][3], $this->components_array_children[$m][2], $p, 3, 0, htmlspecialchars($this->components_array_children[$m][6]),0);
					$n++;	
				}						
			}													
		}
		
		//extensions
		$menuitems[$n] = array(JText::_('MOD_MENU_EXTENSIONS_EXTENSIONS'), '', '#', 0, 1, 0, 'MOD_MENU_EXTENSIONS_EXTENSIONS',0);
		$e = $n;
		$n++;		
		$menuitems[$n] = array(JText::_('MOD_MENU_EXTENSIONS_EXTENSION_MANAGER'), $this->icon('install'), 'index.php?option=com_installer', $e, 2, 0, 'MOD_MENU_EXTENSIONS_EXTENSION_MANAGER',0);
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', $e, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_EXTENSIONS_MODULE_MANAGER'), $this->icon('module'), 'index.php?option=com_modules', $e, 2, 0, 'MOD_MENU_EXTENSIONS_MODULE_MANAGER',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_EXTENSIONS_PLUGIN_MANAGER'), $this->icon('plugin'), 'index.php?option=com_plugins', $e, 2, 0, 'MOD_MENU_EXTENSIONS_PLUGIN_MANAGER',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_EXTENSIONS_TEMPLATE_MANAGER'), $this->icon('themes'), 'index.php?option=com_templates', $e, 2, 0, 'MOD_MENU_EXTENSIONS_TEMPLATE_MANAGER',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_EXTENSIONS_LANGUAGE_MANAGER'), $this->icon('language'), 'index.php?option=com_languages', $e, 2, 0, 'MOD_MENU_EXTENSIONS_LANGUAGE_MANAGER',0);
		$n++;
		
		//help
		$menuitems[$n] = array(JText::_('MOD_MENU_HELP'), '', '#', 0, 1, 0, 'MOD_MENU_HELP',0);
		$h = $n;
		$n++;		
		$menuitems[$n] = array(JText::_('MOD_MENU_HELP_JOOMLA'), $this->icon('help'), 'index.php?option=com_admin&view=help', $h, 2, 0, 'MOD_MENU_HELP_JOOMLA',0);
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', $h, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_HELP_SUPPORT_OFFICIAL_FORUM'), $this->icon('help-forum'), 'http://forum.joomla.org', $h, 2, 0, 'MOD_MENU_HELP_SUPPORT_OFFICIAL_FORUM',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_HELP_DOCUMENTATION'), $this->icon('help-docs'), 'http://docs.joomla.org', $h, 2, 0, 'MOD_MENU_HELP_DOCUMENTATION',0);
		$n++;
		$menuitems[$n] = array(JText::_('COM_ADMINMENUMANAGER_LINE'), '', '', $h, 2, 2, 'COM_ADMINMENUMANAGER_LINE',0);
		$n++;
		if($this->joomla_version < '3.0'){
			$menuitems[$n] = array(JText::_('MOD_MENU_HELP_LINKS'), $this->icon('links'), '#', $h, 2, 0, 'MOD_MENU_HELP_LINKS',0);
			$p = $n;
			$l = 3;
			$n++;
		}else{
			$p = $h;
			$l = 2;
		}
		$menuitems[$n] = array(JText::_('MOD_MENU_HELP_EXTENSIONS'), $this->icon('help-jed'), 'http://extensions.joomla.org/', $p, $l, 0, 'MOD_MENU_HELP_EXTENSIONS',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_HELP_TRANSLATIONS'), $this->icon('help-trans'), 'http://community.joomla.org/translations.html', $p, $l, 0, 'MOD_MENU_HELP_TRANSLATIONS',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_HELP_RESOURCES'), $this->icon('help-jrd'), 'http://resources.joomla.org/', $p, $l, 0, 'MOD_MENU_HELP_RESOURCES',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_HELP_COMMUNITY'), $this->icon('help-community'), 'http://community.joomla.org/', $p, $l, 0, 'MOD_MENU_HELP_COMMUNITY',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_HELP_SECURITY'), $this->icon('help-security'), 'http://developer.joomla.org/security.html', $p, $l, 0, 'MOD_MENU_HELP_SECURITY',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_HELP_DEVELOPER'), $this->icon('help-dev'), 'http://developer.joomla.org/', $p, $l, 0, 'MOD_MENU_HELP_DEVELOPER',0);
		$n++;
		$menuitems[$n] = array(JText::_('MOD_MENU_HELP_SHOP'), $this->icon('help-shop'), 'http://shop.joomla.org/', $p, $l, 0, 'MOD_MENU_HELP_SHOP',0);
			
		return $menuitems;
	}
	
	function icon($filename){
		$ds = DIRECTORY_SEPARATOR;
		$return = '';		
		if(file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'templates'.$ds.$this->template.$ds.'images'.$ds.'menu'.$ds.'icon-16-'.$filename.'.png')){
			$return = 'templates/'.$this->template.'/images/menu/icon-16-'.$filename.'.png';
		}
		
		return $return;
	}	
	
	function get_menuitems($menu, $menu_item_id){	
	
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__adminmenumanager_menuitems');
		$query->where('menu='.$menu);		
		$query->order('ordertotal ASC');		
		$menuitems = $db->setQuery((string)$query);				
		$menuitems = $db->loadObjectList();
		
		$this->menu_items_filtered = array();
		$this->menu_items = $menuitems;
		$this->menu_item_id = $menu_item_id;
		
		$this->look_for_children(0);
		return $this->menu_items_filtered;		
	}
	
	function look_for_children($parent){		
		foreach($this->menu_items as $menuitem){
			if($menuitem->parentid==$parent && $menuitem->id!=$this->menu_item_id){			
				$this->menu_items_filtered[] = $menuitem;
				$this->look_for_children($menuitem->id);
			}
		}		
	}
	
	function get_joomla_menus(){
	
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('menutype, title');
		$query->from('#__menu_types');		
		$joomla_menus = $db->setQuery((string)$query);				
		$joomla_menus = $db->loadObjectList();		
		
		return $joomla_menus;		
	}
	
	function get_components(){
	
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);		
		$query->select('m.id, m.title, m.link, m.img, m.parent_id, m.level, e.element');
		$query->from('#__menu AS m');
		$query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
		$query->where('m.client_id=1');
		$query->order('m.lft');
		$components = $db->setQuery((string)$query);				
		$components = $db->loadObjectList();
		
		//reset array
		$this->menu_items_filtered = array();
		$this->menu_items = $components;		
		$this->look_for_children_components(1);			
		
		//get elements for loading language .sys files
		//make array for sorting	
		$elements_done = array();		
		$lang = JFactory::getLanguage();
		$current_language = $lang->getTag();
		$components_array_parents = array();
		$components_array_children = array();
		foreach($this->menu_items_filtered as $menu_item){
			if($menu_item->parent_id==1 && !in_array($menu_item->element, $elements_done)){
				//load the english file
				$lang->load($menu_item->element.'.sys', JPATH_ADMINISTRATOR, 'en-GB', false);	
				//load the current used language file, if present, overwriting the above
				$lang->load($menu_item->element.'.sys', JPATH_ADMINISTRATOR, $current_language, false);	
				$elements_done[] = 	$menu_item->element;	
			}
			if($menu_item->level=='1'){
				$components_array_parents[] = array($menu_item->id, $menu_item->level, $menu_item->link, $menu_item->img, JText::_(strip_tags($menu_item->title)), $menu_item->title);
			}else{
				$components_array_children[] = array($menu_item->id, $menu_item->level, $menu_item->link, $menu_item->img, JText::_(strip_tags($menu_item->title)), $menu_item->parent_id, $menu_item->title);
			}
		}
		
		//sort array by order
		$column[] = '';//reset column if you using this elsewhere
		foreach($components_array_parents as $sortarray){
			$column[] = $sortarray[4];	
		}
		$sort_order = SORT_ASC;//define as a var or else ioncube goes mad
		array_multisort($column, $sort_order, $components_array_parents);	
		
		//return $this->menu_items_filtered;	
		$this->components_array_parents = $components_array_parents;
		$this->components_array_children = $components_array_children;		
	}
	
	function look_for_children_components($parent){	
		$ds = DIRECTORY_SEPARATOR;	
		foreach($this->menu_items as $menuitem){
			if($menuitem->parent_id==$parent && $menuitem->element!='' && file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.$menuitem->element)){
				//make sure only menuitems are shown of components still installed (no orphans from bad uninstalls)	
				//rework icon class to image path if needed
				if(strpos($menuitem->img, 'lass:')){
					$temp = str_replace('class:', '', $menuitem->img);
					//fix namechanges					
					switch ($temp) {
					case 'banners':
						$temp = 'banner';
						break;
					case 'banners-cat':
						$temp = 'banner-categories';
						break;
					case 'banners-cat':
						$temp = 'banner-client';
						break;
					case 'banners-clients':
						$temp = 'banner-client';
						break;
					case 'banners-tracks':
						$temp = 'banner-tracks';
						break;
					case 'contact':
						$temp = 'contacts';
						break;
					case 'contact-cat':
						$temp = 'contacts-categories';
						break;
					case 'messages-add':
						$temp = 'new-privatemessage';
						break;					
					case 'messages-read':
						$temp = 'messages';
						break;
					case 'finder':
						$temp = 'search';
						break;
					case 'weblinks':
						$temp = 'links';
						break;
					case 'weblinks-cat':
						$temp = 'links-cat';
						break;
					case 'joomlaupdate':
						$temp = 'install';
						break;
					}					
					$menuitem->img = 'templates/'.$this->template.'/images/menu/icon-16-'.$temp.'.png';
				}		
				$this->menu_items_filtered[] = $menuitem;
				$this->look_for_children_components($menuitem->id);
			}
		}		
	}
	
	function detect_utf_encoding($file){

		$text = file_get_contents($file);
		$first2 = substr($text, 0, 2);
		$first3 = substr($text, 0, 3);
		$first4 = substr($text, 0, 3);	
		$return = '';	
		
		if($first3==chr(0xEF).chr(0xBB).chr(0xBF)){
			$return = 'UTF-8';
		}elseif($first4==chr(0x00).chr(0x00).chr(0xFE).chr(0xFF)){
			$return = 'UTF-32BE';
		}elseif($first4==chr(0xFF).chr(0xFE).chr(0x00).chr(0x00)){
			$return = 'UTF-32LE';
		}elseif($first2==chr(0xFE).chr(0xFF)){
			$return = 'UTF-16BE';
		}elseif($first2==chr(0xFF).chr(0xFE)){
			$return = 'UTF-16LE';
		}else{
			$return = mb_detect_encoding($text);
		}
		return $return;
	}
	
	function search_toolbar($show_search, $show_ordering, $show_orderdirection, $show_limitbox, $search, $sortfields, $list_dir, $limitbox){		
		
		$return = '';
		//search
		if($show_search){
			if($this->joomla_version >= '3.0'){			
				$return .= '<div class="filter-search btn-group pull-left">';
			}
			$return .= '<input type="text" name="filter_search" id="filter_search" value="'.$search.'" class="text_area"  />';
			if($this->joomla_version >= '3.0'){
				$return .= '</div>';
			}
			if($this->joomla_version >= '3.0'){
				$return .= '<div class="btn-group pull-left hidden-phone">';
				$return .= '<button class="btn hasTooltip" type="submit" title="'.JText::_('JSEARCH_FILTER_SUBMIT').'">';
				$return .= '<i class="icon-search"></i></button>';
				$return .= '<button class="btn hasTooltip" type="button" title="'.JText::_('JSEARCH_FILTER_CLEAR').'" onclick="document.id(\'filter_search\').value=\'\';this.form.submit();">';
				$return .= '<i class="icon-remove"></i></button>';
				$return .= '</div>';				
			}else{
				$return .= '&nbsp;<button onclick="this.form.submit();">'.JText::_('JSEARCH_FILTER_SUBMIT').'</button>';
				$return .= '&nbsp;<button onclick="document.adminForm.filter_search.value=\'\';this.form.submit();">'.JText::_('JSEARCH_FILTER_CLEAR').'</button>';				
			}
		}
		
		//show_limitbox
		if($show_orderdirection && $this->joomla_version >= '3.0'){		
			$return .= '<div class="btn-group pull-right hidden-phone">';
			$return .= '<label for="limit" class="element-invisible">'.JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC').'</label>';
			$return .= $limitbox;
			$return .= '</div>';
		}
			
		//orderdirection
		if($show_orderdirection && $this->joomla_version >= '3.0'){		
			$return .= '<div class="btn-group pull-right hidden-phone">';
			$return .= '<label for="directionTable" class="element-invisible">'.JText::_('JFIELD_ORDERING_DESC').'</label>';
			$return .= '<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">';
			$return .= '<option value="">'.JText::_('JFIELD_ORDERING_DESC').'</option>';
			$return .= '<option value="asc"';
			if($list_dir == 'asc'){
				$return .= ' selected="selected"';
			}			
			$return .= '>'.JText::_('JGLOBAL_ORDER_ASCENDING').'</option>';
			$return .= '<option value="desc"';
			if($list_dir == 'desc'){
				$return .= ' selected="selected"';
			}			
			$return .= '>'.JText::_('JGLOBAL_ORDER_DESCENDING').'</option>';
			$return .= '</select>';
			$return .= '</div>';
		}
		
		//ordering
		if($show_ordering && $this->joomla_version >= '3.0'){		
			$return .= '<div class="btn-group pull-right">';
			$return .= '<label for="sortTable" class="element-invisible">'.JText::_('JGLOBAL_SORT_BY').'</label>';
			$return .= '<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">';
			$return .= '<option value="">'.JText::_('JGLOBAL_SORT_BY').'</option>';
			$return .= $sortfields;
			$return .= '</select>';
			$return .= '</div>';			
		}	
		
		return $return;
	}
	
	public static function string_export($string){	
		
		$return = str_replace('"', '{douBle-qu0te}', $string);
		$return = str_replace(",", '{c0mMa}', $return);	
		$return = str_replace(array("\r\n", "\r", "\n"), '', $return);
		return $return;
	}
	
	public static function string_import($string){		
		
		$return = str_replace('{douBle-qu0te}', '"', $string);
		$return = str_replace("{c0mMa}", ',', $return);			
		return $return;
	}
	
		
	

}
?>