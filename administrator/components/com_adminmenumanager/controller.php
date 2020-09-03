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

jimport('joomla.application.component.controller');

class adminmenumanagerController extends JControllerLegacy{

	public $version = '2.3.2';
	public $amm_config;		
	private $amm_demo_seconds_left;	
	private $amm_version_type = 'free';	//free trial or pro	
	private $helper;
	private $menu_items;
	private $menu_level = 0;	
	private $menu_ordertotal = 0;	

	function display($cachable = false, $urlparams = false){		
		
		$app = JFactory::getApplication();	
		
		//display css
		//not via addDocument else the icon is set to 14 px
		if(JRequest::getVar('layout', '')!='csv'){
			echo '<link rel="stylesheet" href="components/com_adminmenumanager/css/adminmenumanager9.css" type="text/css" />';
			echo '<div class="amm';
			$version = new JVersion;
			if($version->RELEASE >= '3.0'){
				echo ' joomla3';
			}
			echo '">';	
		}			
				
		// Set a default view if none exists			
		if(!JRequest::getVar('view')){						
			JRequest::setVar('view', 'menuitems');								
		}		
		
		//set toolbar
		JToolBarHelper::title('Admin Menu Manager', 'amm_icon');			
		
		if(JRequest::getVar('layout', '')!='csv'){
			$version = new JVersion;
			if($version->RELEASE >= '3.0'){
				//bootstrap selects
				JHtml::_('bootstrap.tooltip');
				JHtml::_('behavior.multiselect');
				JHtml::_('formbehavior.chosen', 'select');
			}else{	
				//make sure mootools is loaded					
				JHTML::_('behavior.mootools');
				
				//load the submenu
				$this->addSubmenu(JRequest::getWord('view', 'adminmenumanager'));
			}
			
			//display messages
			$this->display_header();
		}
		
		parent::display();
		
		if(JRequest::getVar('layout', '')!='csv'){
			echo '</div>';	
		}
		
		//display footer
		if(JRequest::getVar('layout', '')!='csv'){
			$this->display_footer();
		}
				
	}	
	
	function __construct(){	
		
		$this->helper = $this->get_helper();		
		$this->amm_config = $this->helper->get_config();
		
		parent::__construct();		
	}
	
	function addSubmenu($vName = 'adminmenumanager'){	
		JSubMenuHelper::addEntry(
			JText::_('COM_ADMINMENUMANAGER_CONFIG'),
			'index.php?option=com_adminmenumanager&view=configuration',
			$vName == 'configuration'
		);
		$lang = JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);
		JSubMenuHelper::addEntry(
			JText::_('JOPTION_MENUS'),
			'index.php?option=com_adminmenumanager&view=menus',
			$vName == 'menus' || $vName == 'menu'
		);		
		JSubMenuHelper::addEntry(
			JText::_('COM_MENUS_SUBMENU_ITEMS'),
			'index.php?option=com_adminmenumanager&view=menuitems',
			$vName == 'menuitems' || $vName == 'menuitem' || $vName == 'menuitemsimport' || $vName == 'menuitemsexport'
		);	
		JSubMenuHelper::addEntry(
			JText::_('COM_MENUS_SUBMENU_ITEMS').' '.JText::_('COM_ADMINMENUMANAGER_IMPORT'),
			'index.php?option=com_adminmenumanager&view=menuitemsimport',
			$vName == 'menuitemsimport'
		);		
		JSubMenuHelper::addEntry(
			JText::_('COM_USERS_SUBMENU_USERS'),
			'index.php?option=com_adminmenumanager&view=users',
			$vName == 'users'
		);		
		JSubMenuHelper::addEntry(
			JText::_('COM_ADMINMENUMANAGER_INFO'),
			'index.php?option=com_adminmenumanager&view=info',
			$vName == 'info'
		);		
	}	
	
	function add_submenu($vName = 'adminmenumanager'){	
	
		$vName = JFactory::getApplication()->input->get('view');
		JHtmlSidebar::addEntry(
			JText::_('COM_ADMINMENUMANAGER_CONFIG'),
			'index.php?option=com_adminmenumanager&view=configuration',
			$vName == 'configuration'
		);
		$lang = JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);
		JHtmlSidebar::addEntry(
			JText::_('JOPTION_MENUS'),
			'index.php?option=com_adminmenumanager&view=menus',
			$vName == 'menus' || $vName == 'menu'
		);		
		JHtmlSidebar::addEntry(
			JText::_('COM_MENUS_SUBMENU_ITEMS'),
			'index.php?option=com_adminmenumanager&view=menuitems',
			$vName == 'menuitems' || $vName == 'menuitem' || $vName == 'menuitemsimport' || $vName == 'menuitemsexport'
		);	
		JHtmlSidebar::addEntry(
			JText::_('COM_MENUS_SUBMENU_ITEMS').' '.JText::_('COM_ADMINMENUMANAGER_IMPORT'),
			'index.php?option=com_adminmenumanager&view=menuitemsimport',
			$vName == 'menuitemsimport'
		);			
		JHtmlSidebar::addEntry(
			JText::_('COM_USERS_SUBMENU_USERS'),
			'index.php?option=com_adminmenumanager&view=users',
			$vName == 'users'
		);		
		JHtmlSidebar::addEntry(
			JText::_('COM_ADMINMENUMANAGER_INFO'),
			'index.php?option=com_adminmenumanager&view=info',
			$vName == 'info'
		);		
	}		
	
	function display_header(){					
		
		if(JRequest::getVar('layout')!='csv'){		
			$this->check_demo_time_left();				
		}		
	}						
		
	function config_save(){	
	
		$db = JFactory::getDBO();	
		
		JRequest::checkToken() or jexit('Invalid Token');	
					
		$default_access_type = 'default_access_'.$this->amm_config['based_on'];	
		$this->amm_config['access_enabled'] = JRequest::getVar('access_enabled', '1', 'post');
		$this->amm_config['based_on'] = JRequest::getVar('based_on', '', 'post');
		$this->amm_config['group_inheritance'] = JRequest::getVar('group_inheritance', '', 'post');
		$this->amm_config['super_user_sees_all'] = JRequest::getVar('super_user_sees_all', '', 'post');
		$this->amm_config['level_sort'] = JRequest::getVar('level_sort', '', 'post');		
		$this->amm_config[$default_access_type] = JRequest::getVar('default_access', '', 'post');	
		$this->amm_config['multilanguage'] = JRequest::getVar('multilanguage', '', 'post');	
		$this->amm_config['version_checker'] = JRequest::getVar('version_checker', '', 'post');
		
		$registry = new JRegistry;
		$registry->loadArray($this->amm_config);
		$config = $registry->toString();		
		
		$query = $db->getQuery(true);		
		$query->update('#__adminmenumanager_config');
		$query->set('config='.$db->q($config));
		$query->where('id='.$db->q('amm'));		
		$db->setQuery($query);
		$db->query();

		
		//redirect	
		$url = 'index.php?option=com_adminmenumanager&view=configuration';					
		$this->setRedirect($url, JText::_('COM_ADMINMENUMANAGER_CONFIG').' '.JText::_('COM_ADMINMENUMANAGER_SAVED'));
	}			

	function display_footer(){	
	
		$helper = $this->get_helper();
		
		echo '<div class="amm_clearboth"> </div>';
		echo '<div class="smallgrey" id="amm_footer">';
		echo '<table>';
		echo '<tr>';
		echo '<td class="text_right">';
		echo '<a href="http://www.pages-and-items.com" target="_blank">Admin-Menu-Manager</a>';
		echo '</td>';
		echo '<td class="five_pix">';
		echo '&copy;';
		echo '</td>';
		echo '<td>';
		echo '2012 - 2019 Carsten Engel';		
		echo '</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td class="text_right">';
		echo $this->amm_strtolower(JText::_('JVERSION'));
		echo '</td>';
		echo '<td class="five_pix">';
		echo '=';
		echo '</td>';
		echo '<td>';
		echo $this->version.' ('.$this->amm_version_type.' '.$this->amm_strtolower(JText::_('JVERSION')).')';
		if($this->amm_version_type!='trial'){
			echo ' <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="blank">GNU/GPL License</a>';
		}
		echo '</td>';
		echo '</tr>';
		//version checker
		if($this->amm_config['version_checker']){
			echo '<tr>';
			echo '<td class="text_right">';
			echo $this->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_LATEST_VERSION'));
			echo '</td>';
			echo '<td class="five_pix">';
			echo '=';
			echo '</td>';
			echo '<td>';
			$app = JFactory::getApplication();
			$latest_version_message = $app->getUserState( "com_adminmenumanager.latest_version_message", '');
			if($latest_version_message==''){
				$latest_version_message = JText::_('COM_ADMINMENUMANAGER_VERSION_CHECKER_NOT_AVAILABLE');
				$url = 'http://www.pages-and-items.com/latest_version.php?extension=adminmenumanager';		
				$file_object = @fopen($url, "r");		
				if($file_object == TRUE){
					$version = fread($file_object, 1000);
					$latest_version_message = $version;
					if($this->version!=$version){
						$latest_version_message .= ' <span class="amm_red">'.JText::_('COM_ADMINMENUMANAGER_NEWER_VERSION').'</span>';
						if($this->amm_version_type=='pro'){
							$download_url = 'http://www.pages-and-items.com/my-extensions';
						}elseif($this->amm_version_type=='trial'){
							$download_url = 'http://engelweb.nl/trialversions/';
						}else{
							$download_url = 'http://www.pages-and-items.com/extensions/admin-menu-manager';
						}
						$latest_version_message .= ' <a href="'.$download_url.'" target="_blank">'.JText::_('COM_ADMINMENUMANAGER_DOWNLOAD').'</a>';						
						if($this->amm_version_type!='pro'){
							$latest_version_message .= ' <a href="index.php?option=com_installer&view=update">'.$this->amm_strtolower(JText::_('JLIB_INSTALLER_UPDATE')).'</a>';
						}
					}else{
						$latest_version_message .= ' <span class="pi_green">'.JText::_('COM_ADMINMENUMANAGER_IS_LATEST_VERSION').'</span>';
					}
					fclose($file_object);
				}				
				$app->setUserState( "com_adminmenumanager.latest_version_message", $latest_version_message );
			}
			echo $latest_version_message;
			echo '</td>';
			echo '</tr>';
		}	
		echo '<tr>';
		echo '<td class="text_right" colspan="2">';
		echo $this->amm_strtolower(JText::_('COM_ADMINMENUMANAGER_REVIEW_B')); 
		echo '</td>';
		echo '<td>';
		if($this->amm_version_type=='pro'){
			$url_jed = '22805';
		}else{
			$url_jed = '21305';
		}		
		echo '<a href="http://extensions.joomla.org/extensions/administration/admin-navigation/'.$url_jed.'" target="_blank">';
		echo 'Joomla! Extensions Directory</a>';
		echo '</td>';
		echo '</tr>';		
		echo '</table>';		
		echo '</div>';			
	}
	
	function ajax_version_checker(){
		$message = JText::_('COM_ADMINMENUMANAGER_VERSION_CHECKER_NOT_AVAILABLE');	
		$url = 'http://www.pages-and-items.com/latest_version.php?extension=adminmenumanager';		
		$file_object = @fopen($url, "r");		
		if($file_object == TRUE){
			$version = fread($file_object, 1000);
			$message = JText::_('COM_ADMINMENUMANAGER_LATEST_VERSION').' = '.$version;
			if($this->version!=$version){
				$message .= '<div><span class="amm_red">'.JText::_('COM_ADMINMENUMANAGER_NEWER_VERSION').'</span>.</div>';
				if($this->amm_version_type=='pro'){
					$download_url = 'http://www.pages-and-items.com/my-extensions';
				}elseif($this->amm_version_type=='trial'){
					$download_url = 'http://engelweb.nl/trialversions/';
				}else{
					$download_url = 'http://www.pages-and-items.com/extensions/admin-menu-manager';
				}
				$message .= '<div><a href="'.$download_url.'" target="_blank">'.JText::_('COM_ADMINMENUMANAGER_DOWNLOAD').'</a></div>';
			}else{
				$message .= '<div><span class="pi_green">'.JText::_('COM_ADMINMENUMANAGER_IS_LATEST_VERSION').'</span>.</div>';
			}
			fclose($file_object);
		}
		
		//reset version checker session
		$app = JFactory::getApplication();
		$app->setUserState( "com_adminmenumanager.latest_version_message", '' );
		
		echo $message;
		exit;
	}
	
	function get_helper(){
		$ds = DIRECTORY_SEPARATOR;
		require_once(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_adminmenumanager'.$ds.'helpers'.$ds.'adminmenumanager.php');
		$helper = new adminmenumanagerHelper();
		return $helper;
	}
	
	function amm_strtolower($string){
		if(function_exists('mb_strtolower')){			
			$string = mb_strtolower($string, 'UTF-8');
		}
		return $string;
	}
	
	function get_version_type(){
		//so that private var is available for templates
		return $this->amm_version_type;
	}
	
	function menu_save(){	
	
		$db = JFactory::getDBO();	
			
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');
		
		//get vars
		$id = intval(JRequest::getVar('menu_id', 0, 'post'));
		$name = strip_tags(JRequest::getVar('name', '', 'post'));
		$description = strip_tags(JRequest::getVar('description', '', 'post'));	
		
		//edit	
		$query = $db->getQuery(true);		
		$query->update('#__adminmenumanager_menus');
		$query->set('name='.$db->q($name));
		$query->set('description='.$db->q($description));					
		$query->where('id='.$id);
		$db->setQuery($query);
		$db->query();		
				
		//redirect			
		if(JRequest::getVar('apply', '')){
			$url = 'index.php?option=com_adminmenumanager&view=menu&id='.$id;
		}else{
			$url = 'index.php?option=com_adminmenumanager&view=menus';
		}	
		$this->setRedirect($url, JText::_('COM_ADMINMENUMANAGER_MENU').' '.JText::_('COM_ADMINMENUMANAGER_SAVED'));
	}	
	
	function menuitem_unpublish(){
	
		$db = JFactory::getDBO();
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');	
		
		$id = intval(JRequest::getVar('menuitem_id', 0, 'post'));
		
		$query = $db->getQuery(true);		
		$query->update('#__adminmenumanager_menuitems');
		$query->set('published=0');				
		$query->where('id='.(int)$id);
		$db->setQuery((string)$query);
		$db->query();
		
		//redirect
		$url = 'index.php?option=com_adminmenumanager&view=menuitems';	
		$lang = JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);		
		$this->setRedirect($url, $this->amm_strtolower(JText::_('COM_MENUS_ITEM_FIELD_ALIAS_MENU_LABEL')).' '.$this->amm_strtolower(JText::_('JUNPUBLISHED')));

	}
	
	function menuitem_publish(){
	
		$db = JFactory::getDBO();
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');	
		
		$id = intval(JRequest::getVar('menuitem_id', 0, 'post'));
		
		$query = $db->getQuery(true);		
		$query->update('#__adminmenumanager_menuitems');
		$query->set('published=1');				
		$query->where('id='.(int)$id);
		$db->setQuery((string)$query);
		$db->query();
		
		//redirect
		$url = 'index.php?option=com_adminmenumanager&view=menuitems';
		$lang = JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);				
		$this->setRedirect($url, $this->amm_strtolower(JText::_('COM_MENUS_ITEM_FIELD_ALIAS_MENU_LABEL')).' '.$this->amm_strtolower(JText::_('JPUBLISHED')));

	}
	
	function menuitem_save(){
	
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
			
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');
		
		//get vars
		$id = intval(JRequest::getVar('menuitem_id', 0, 'post'));
		$title = strip_tags(JRequest::getVar('title', '', 'post'));		
		$menu = intval(JRequest::getVar('menu', '', 'post'));	
		$menu_ori = intval(JRequest::getVar('menu_ori', '', 'post'));	
		$url = strip_tags(JRequest::getVar('url', '', 'post'));
		$icon = strip_tags(JRequest::getVar('icon', '', 'post'));	
		$published = intval(JRequest::getVar('published', '', 'post'));	
		$access = intval(JRequest::getVar('access', '', 'post'));	
		$parentid = intval(JRequest::getVar('parentid', '', 'post'));
		$temp = $this->get_nested($title, 0, $parentid, $id);
		$type = intval(JRequest::getVar('type', 0, 'post'));
		$target = intval(JRequest::getVar('target', 0, 'post'));	
		$width = intval(JRequest::getVar('width', '800', 'post'));
		$height = intval(JRequest::getVar('height', '600', 'post'));
		if($this->amm_version_type=='free'){
			$use_constant = 0;
		}else{
			$use_constant = intval(JRequest::getVar('use_constant', 0, 'post'));
		}
		$constant = strip_tags(JRequest::getVar('constant', '', 'post'));
		
		if($this->amm_config['based_on']=='group'){
			$access_column = 'accessgroup';
		}else{
			$access_column = 'accesslevel';
		}				
		if($id==0){
			//new 					
			$query = $db->getQuery(true);
			$query->insert('#__adminmenumanager_menuitems');
			$query->set('title='.$db->q($title));
			$query->set('use_constant='.$db->q($use_constant));
			$query->set('constant='.$db->q($constant));
			$query->set('icon='.$db->q($icon));
			$query->set('menu='.$menu);	
			$query->set('url='.$db->q($url));
			$query->set('published='.$published);				
			$query->set($access_column.'='.$access);
			$query->set('parentid='.$parentid);		
			$query->set('ordering=9999');
			$query->set('type='.$type);		
			$query->set('target='.$target);	
			$query->set('width='.$width);	
			$query->set('height='.$height);					
			$db->setQuery((string)$query);
			$db->query();
			
			$id = $db->insertid(); 
		}else{
			//edit	
			
			if($menu!=$menu_ori){
				//menu item is moved to different menu
				//so reset the parent
				$parentid = 0;
				//and move all its children as well
				$this->move_children_to_other_menu($id, $menu);								
			}
			
			$query = $db->getQuery(true);		
			$query->update('#__adminmenumanager_menuitems');
			$query->set('title='.$db->q($title));
			$query->set('use_constant='.$db->q($use_constant));
			$query->set('constant='.$db->q($constant));
			$query->set('icon='.$db->q($icon));
			$query->set('menu='.$menu);	
			$query->set('url='.$db->q($url));
			$query->set('published='.$published);
			$query->set($access_column.'='.$access);	
			$query->set('parentid='.$parentid);	
			$query->set('type='.$type);		
			$query->set('target='.$target);	
			$query->set('width='.$width);	
			$query->set('height='.$height);			
			$query->where('id='.(int)$id);
			$db->setQuery((string)$query);
			$db->query();
		}
		
		//set session so that the menu-selector is correct
		$app->setUserState('com_adminmenumanager.menuitems.filter.menu', $menu);
				
		$this->rebuild_menus();	
		
		//redirect	
		if(JRequest::getVar('save_and_new', '')){	
			$url = 'index.php?option=com_adminmenumanager&view=menuitem&id=0&menu='.$menu;
		}elseif(JRequest::getVar('apply', '')){
			$url = 'index.php?option=com_adminmenumanager&view=menuitem&id='.$id;
		}else{			
			$url = 'index.php?option=com_adminmenumanager&view=menuitems';
		}
		$lang = JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);			
		$this->setRedirect($url, $this->amm_strtolower(JText::_('COM_MENUS_ITEM_FIELD_ALIAS_MENU_LABEL')).' '.JText::_('COM_ADMINMENUMANAGER_SAVED'));
	}
	
	function move_children_to_other_menu($menuitem_id, $menu){
		
		$db = JFactory::getDBO();
		
		//select all children of this parent
		$query = $db->getQuery(true);
		$query->select('id, parentid');
		$query->from('#__adminmenumanager_menuitems');
		$query->where('parentid='.$menuitem_id);		
		$menuitems = $db->setQuery((string)$query);				
		$menuitems = $db->loadObjectList();
			
		foreach($menuitems as $menuitem){
			//update each child
			$query = $db->getQuery(true);		
			$query->update('#__adminmenumanager_menuitems');			
			$query->set('menu='.$menu);			
			$query->where('id='.(int)$menuitem->id);
			$db->setQuery((string)$query);
			$db->query();
		
			//check their children
			$this->move_children_to_other_menu($menuitem->id, $menu);
		}	
	}
	
	function rebuild_menus(){
	
		$db = JFactory::getDBO();		
		
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__adminmenumanager_menus');				
		$menus = $db->setQuery((string)$query);				
		$menus = $db->loadObjectList();		
	
		foreach($menus as $menu){				
			$this->rebuild_menu($menu->id);
		}	
	}
	
	function rebuild_menu($menu){
		
		$db = JFactory::getDBO();
		
		//get all menu items of this menu
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__adminmenumanager_menuitems');
		$query->where('menu='.$menu);
		$query->order('ordering');		
		$menuitems = $db->setQuery((string)$query);				
		$menuitems = $db->loadObjectList();
		
		$this->menu_items = $menuitems;
		$this->menu_level = 0;
		$this->menu_ordertotal = 0;
			
		$this->look_for_children(0);	
			
	}
	
	function look_for_children($parent){
		$this->menu_level = $this->menu_level+1;
		$order = 0;		
		foreach($this->menu_items as $menuitem){
			if($menuitem->parentid==$parent){
				$order = $order + 1;
				$this->update_menuitem($menuitem, $order);
			}
		}
		$this->menu_level = $this->menu_level-1;	
	}	
	
	function update_menuitem($menuitem, $order){
		
		$db = JFactory::getDBO();
		
		$this->menu_ordertotal = $this->menu_ordertotal + 1;
		
		$query = $db->getQuery(true);		
		$query->update('#__adminmenumanager_menuitems');
		$query->set('level='.$this->menu_level);
		$query->set('ordering='.$order);
		$query->set('ordertotal='.$this->menu_ordertotal);			
		$query->where('id='.(int)$menuitem->id);
		$db->setQuery((string)$query);
		$db->query();
		
		$this->look_for_children($menuitem->id);
	}
	
	function menuitems_delete(){
		
		$db = JFactory::getDBO();
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');			
		
		$cid = JRequest::getVar('cid', null, 'post', 'array');		
		
		$lang = JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);
		
		if (!is_array($cid) || count($cid) < 1) {
			echo JText::_('COM_MENUS_NO_ITEM_SELECTED');
			exit();
		}
		
		if (count($cid)){			
			//delete menuitem(s)			
			$query = $db->getQuery(true);
			$query->delete();
			$query->from('#__adminmenumanager_menuitems');
			$query->where('id IN (' . implode(',', $cid) . ')');
			$db->setQuery((string)$query);
			$db->query();
		}
		
		$this->rebuild_menus();			
		$this->setRedirect("index.php?option=com_adminmenumanager&view=menuitems", JText::_('COM_MENUS_SUBMENU_ITEMS').' '.JText::_('COM_ADMINMENUMANAGER_DELETED'));
	}
	
	function menuitems_publish(){
		
		$db = JFactory::getDBO();
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');			
		
		$cid = JRequest::getVar('cid', null, 'post', 'array');		
		
		$lang = JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);
		
		if (!is_array($cid) || count($cid) < 1) {
			echo JText::_('COM_MENUS_NO_ITEM_SELECTED');
			exit();
		}
		
		if (count($cid)){			
			//publish menuitem(s)			
			$query = $db->getQuery(true);			
			$query->update('#__adminmenumanager_menuitems');
			$query->set('published=1');
			$query->where('id IN (' . implode(',', $cid) . ')');
			$db->setQuery((string)$query);
			$db->query();
		}
		
		$this->setRedirect("index.php?option=com_adminmenumanager&view=menuitems", JText::_('COM_MENUS_SUBMENU_ITEMS').' '.$this->amm_strtolower(JText::_('JPUBLISHED')));
	}
	
	function menuitems_unpublish(){
		
		$db = JFactory::getDBO();
		
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');			
		
		$cid = JRequest::getVar('cid', null, 'post', 'array');
		
		$lang = JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);		
		
		if (!is_array($cid) || count($cid) < 1) {
			echo JText::_('COM_MENUS_NO_ITEM_SELECTED');
			exit();
		}
		
		if (count($cid)){			
			//publish menuitem(s)			
			$query = $db->getQuery(true);			
			$query->update('#__adminmenumanager_menuitems');
			$query->set('published=0');
			$query->where('id IN (' . implode(',', $cid) . ')');
			$db->setQuery((string)$query);
			$db->query();
		}		
		
		$this->setRedirect('index.php?option=com_adminmenumanager&view=menuitems', JText::_('COM_MENUS_SUBMENU_ITEMS').' '.$this->amm_strtolower(JText::_('JUNPUBLISHED')));
	}
	
	function menuitems_order_save(){
	
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		
		$orders = JRequest::getVar('orders', array(), 'post', 'array');		
		$order_ids = JRequest::getVar('order_ids', array(), 'post', 'array');		
				
		for($n = 0; $n < count($order_ids); $n++){		
			$order = $orders[$n];			
			$order_id = $order_ids[$n];					
			
			$query = $db->getQuery(true);		
			$query->update('#__adminmenumanager_menuitems');
			$query->set('ordering='.$db->q($order));				
			$query->where('id='.(int)$order_id);
			$db->setQuery((string)$query);
			$db->query();				
		}	
		
		$this->rebuild_menu(JRequest::getVar('filter_menu', ''));	
		
		$app->redirect('index.php?option=com_adminmenumanager&view=menuitems', JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));	
	}
	
	function check_demo_time_left(){			
		return '';
	}	
	
	function amm_check_trial_version(){
		return true;
	}	
	
	function reorder_listitem(){
	
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();	
		
		JRequest::checkToken() or jexit('Invalid Token');			
		
		$id = intval(JRequest::getVar('menuitem_id', '', 'post'));
		$direction = JRequest::getVar('direction', '');
		
		//get data of menuitem to move
		$old_order = 0;
		$menu = 0;
		$parentid = '';
		$query = $db->getQuery(true);
		$query->select('ordering, menu, parentid');
		$query->from('#__adminmenumanager_menuitems');		
		$query->where('id='.$id);		
		$menuitems = $db->setQuery($query, 0, 1);				
		$menuitems = $db->loadObjectList();
		foreach($menuitems as $menuitem){		
			$old_order = $menuitem->ordering;	
			$menu = $menuitem->menu;	
			$parentid = $menuitem->parentid;	
		}	
		
		//calculate new order
		if($direction=='up'){
			$new_order = $old_order-1;	
		}else{
			$new_order = $old_order+1;
		}
	
		//get menuitems
		$query = $db->getQuery(true);
		$query->select('id, ordering');
		$query->from('#__adminmenumanager_menuitems');		
		$query->where('menu='.$menu);
		$query->where('parentid='.$parentid);
		$query->order('ordering');
		$menuitems = $db->setQuery($query);				
		$menuitems = $db->loadObjectList();
			
		//check which id has the new order
		$id_of_menuitem_moving = '';
		foreach($menuitems as $menuitem){			
			if($menuitem->ordering==$new_order){
				$id_of_menuitem_moving = $menuitem->id;
				break;
			}
		}
		
		//update the menuitem
		$query = $db->getQuery(true);		
		$query->update('#__adminmenumanager_menuitems');
		$query->set('ordering='.$new_order);				
		$query->where('id='.$id);
		$db->setQuery($query);
		$db->query();

		
		//update the menuitem which has to switch position
		$query = $db->getQuery(true);		
		$query->update('#__adminmenumanager_menuitems');
		$query->set('ordering='.$old_order);				
		$query->where('id='.$id_of_menuitem_moving);
		$db->setQuery($query);
		$db->query();
		
		$this->rebuild_menu($menu);	
		
		$app->redirect('index.php?option=com_adminmenumanager&view=menuitems', JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
	
	}
	
	function go_to_menuitems(){
	
		$app = JFactory::getApplication();
		
		$menu = JRequest::getVar('go_to_menu', '');
		if($menu){
			//set session so that the menu-selector is correct
			$app->setUserState('com_adminmenumanager.menuitems.filter.menu', $menu);
		}
		$url = 'index.php?option=com_adminmenumanager&view=menuitems';	
		$this->setRedirect($url);
	}
	
	function get_nested($id, $hierarchy, $parentid=0, $level=0){
		
		$db = JFactory::getDBO();
		
		$nested = 0;	
		if($hierarchy){
			$temp = 'false';
		}	
		if(!$id){
			$query = $db->getQuery(true);		
			$query->select('id');			
			$query->from('#__adminmenumanager_menuitems');	
			$groups = $this->helper->get_users_groups();			
			$access = '(';				
			for($n = 0; $n < count($groups); $n++){
				if($n){
					$access .= ' OR ';
				}				
				$access .= 'access LIKE '.$db->q('%,'.$groups[$n].',%');				
			}
			$access .= ' OR ';
			$access .= 'access=",all,"';
			$access .= ')';			
			$query->where($access);
			$query->order('ordertotal ASC');		
			$rows = $db->setQuery((string)$query);				
			$nested = $db->loadResult();
		}elseif($hierarchy){			
			$nested = strlen($temp);
			if($level){
				$nested++;
			}
		}else{			
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__adminmenumanager_menuitems');	
			$q = $this->get_nested($query, 1, 0, $level);		
			$rows = $db->setQuery($query);				
			$rows = $db->loadObjectList();
			$nested = $rows;
			if(count($rows)>=$q && $q){
				//error
				exit;
			}
		}
				
		return $nested;
	}
	
	function reorder_items(){
	
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();	
		
		JRequest::checkToken() or jexit('Invalid Token');			
		
		$id = intval(JRequest::getVar('reorder_id', '', 'post'));
		$direction = JRequest::getVar('reorder_direction', '');
		$view = JRequest::getVar('reorder_view', '');
		
		$table = 0;
		$rebuild = 0;
		if($view=='menus'){
			$table = 'adminmenumanager_menus';
			$column_ordering = 'ordering';
			$column_id = 'id';
			$rebuild = 1;
		}	
		
		if($table){			
			
			//get items
			$query = $db->getQuery(true);
			$query->select($column_id.','.$column_ordering);
			$query->from('#__'.$table);		
			$query->order($column_ordering);
			$items = $db->setQuery($query);				
			$items = $db->loadObjectList();
			
			//rebuild ordering
			if($rebuild){
				//rebuild before the swap just in case there are 2 items with the same ordering
				$this->rebuild_reordering($items, $table, $column_ordering, $column_id);
			}
			
			//rebuild into multidimensional array
			$items_array = array();
			$i = 0;
			$index_of_moving_item = 0;
			foreach($items as $item){				
				//set different column name here if needed
				$temp_id = $item->id;
				$temp_ordering = $item->ordering;				
				$items_array[] = array($temp_id, $temp_ordering);			
				if($temp_id==$id){
					$index_of_moving_item = $i;
					$old_order = $temp_ordering;
				}
				$i++;
			}			
			
			//get data of the swap item
			if($direction=='up'){
				$index_of_swap_item = $index_of_moving_item-1;			
			}else{
				$index_of_swap_item = $index_of_moving_item+1;	
			}
			if(isset($items_array[$index_of_swap_item])){
				$swap_item_id = $items_array[$index_of_swap_item][0];
				$new_order = $items_array[$index_of_swap_item][1];
			}
			
			//update the item
			$query = $db->getQuery(true);		
			$query->update('#__'.$table);
			$query->set($column_ordering.'='.$new_order);				
			$query->where($column_id.'='.$id);
			$db->setQuery($query);
			$db->query();
			
			//update the swap item
			$query = $db->getQuery(true);		
			$query->update('#__'.$table);
			$query->set($column_ordering.'='.$old_order);				
			$query->where($column_id.'='.$swap_item_id);
			$db->setQuery($query);
			$db->query();	
			
			//rebuild ordering
			if($rebuild){
				//rebuild after the swap just in case the ordering had different numbers, so they nicely match
				$this->rebuild_reordering($items, $table, $column_ordering, $column_id);
			}	
			unset($items);	
			
			$message = JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED');
		}else{
			$message = 'error saving order';
		}		
		
		$app->redirect('index.php?option=com_adminmenumanager&view='.$view, $message);	
	}
	
	function rebuild_reordering($items_object, $table, $column_ordering, $column_id){
		
		$db = JFactory::getDBO();
		
		if($items_object){
			$items = $items_object;
		}else{
			//need to get the items			
			$query = $db->getQuery(true);
			$query->select($column_id.','.$column_ordering);
			$query->from('#__'.$table);		
			$query->order($column_ordering);
			$items = $db->setQuery($query);				
			$items = $db->loadObjectList();
		}
		
		$i = 1;			
		foreach($items as $item){
					
			//set different column name here if needed
			$temp_id = $item->id;
			$temp_ordering = $item->ordering;
						
			if($temp_ordering!=$i){
				//only update if order does not match						
				$query = $db->getQuery(true);		
				$query->update('#__'.$table);
				$query->set($column_ordering.'='.$i);				
				$query->where($column_id.'='.$temp_id);
				$db->setQuery($query);
				$db->query();
			}			
			$i++;
		}		
		unset($items);	
			
		return true;		
	}
	
	function items_order_save(){
	
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		
		$orders = JRequest::getVar('orders', array(), 'post', 'array');		
		$order_ids = JRequest::getVar('order_ids', array(), 'post', 'array');		
		$view = JRequest::getVar('reorder_view', '');
		
		$table = 0;
		if($view=='menus'){
			$table = 'adminmenumanager_menus';
			$column_ordering = 'ordering';
			$column_id = 'id';
			$rebuild = 1;
		}
		
		if($table){
			for($n = 0; $n < count($order_ids); $n++){		
				$order = $orders[$n];			
				$order_id = $order_ids[$n];					
				
				$query = $db->getQuery(true);		
				$query->update('#__'.$table);
				$query->set($column_ordering.'='.$db->q($order));				
				$query->where($column_id.'='.(int)$order_id);
				$db->setQuery((string)$query);
				$db->query();				
			}	
			
			//rebuild ordering
			if($rebuild){
				//rebuild after the swap just in case the ordering had different numbers, so they nicely match
				$this->rebuild_reordering(0, $table, $column_ordering, $column_id);
			}
			
			$message = JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED');		
		}else{
			$message = 'error saving order';
		}		
		
		$app->redirect('index.php?option=com_adminmenumanager&view='.$view, $message);
	}
	
	function menuitems_export(){
		
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
			
		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');			
		
		$cid = JRequest::getVar('cid', null, 'post', 'array');						
		
		if (!is_array($cid) || count($cid) < 1) {
			$lang = JFactory::getLanguage();
			$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);	
			echo JText::_('COM_MENUS_NO_ITEM_SELECTED');
			exit();
		}		
		
		if (count($cid)){	
		
			//make array to string
			$cid_string = implode(',', $cid);
			
			//add string to session
			$app->setUserState( "com_adminmenumanager.cid", $cid_string);
			
			//redirect
			$this->setRedirect('index.php?option=com_adminmenumanager&view=menuitemsexport');	
		}			
	}
	
	function menuitems_batch_parent(){	
		$this->menuitems_batch_process('parent');
	}
	
	function menuitems_batch_constant(){
	
		$this->menuitems_batch_process('constant');
	}
	
	private function menuitems_batch_process($type){
	
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		
		JRequest::checkToken() or jexit('Invalid Token');
		
		$cid = JRequest::getVar('cid', null, 'post', 'array');	
		$access = intval(JRequest::getVar('access', '', 'post'));	
		$menu = intval(JRequest::getVar('menu', '', 'post'));	
		$parentid = intval(JRequest::getVar('parentid', '', 'post'));	
		$use_constant = intval(JRequest::getVar('use_constant', '', 'post'));	
		
		$lang = JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);	
		
		if (!is_array($cid) || count($cid) < 1) {
			echo JText::_('COM_MENUS_NO_ITEM_SELECTED');
			exit();
		}	
		
		if($this->amm_config['based_on']=='group'){
			$access_column = 'accessgroup';
		}else{
			$access_column = 'accesslevel';
		}	
		
		if(count($cid)){
			
			for($n = 0; $n < count($cid); $n++){
				//do update
				$query = $db->getQuery(true);		
				$query->update('#__adminmenumanager_menuitems');
				if($type=='access'){
					$query->set($access_column.'='.$access);
				}elseif($type=='parent'){			
					$query->set('parentid='.$parentid);	
					$query->set('ordering='.(int)($n+99999));				
				}elseif($type=='constant'){
					$query->set('use_constant='.$db->q($use_constant));					
				}	
				$query->where('id='.(int)$cid[$n]);
				$db->setQuery((string)$query);
				$db->query();
			}			
			
			//rebuild menu
			$this->rebuild_menu($menu);			
		}	
		
		//redirect			
		$this->setRedirect('index.php?option=com_adminmenumanager&view=menuitems', $this->amm_strtolower(JText::_('COM_MENUS_SUBMENU_ITEMS')).' '.JText::_('COM_ADMINMENUMANAGER_SAVED'));	
	}
	
	public function save_order_ajax_menuitems(){
		
		$db = JFactory::getDBO();
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the arrays from the Request
		$cid = $this->input->post->get('cid', null, 'array');
		$orders = $this->input->post->get('order', null, 'array');
					
		for($n = 0; $n < count($cid); $n++){		
			$order = $orders[$n];			
			$order_id = $cid[$n];					
			
			$query = $db->getQuery(true);		
			$query->update('#__adminmenumanager_menuitems');
			$query->set('ordering='.(int)$order);				
			$query->where('id='.(int)$order_id);
			$db->setQuery((string)$query);
			$db->query();				
		}	
		
		$this->rebuild_menu(JRequest::getVar('filter_menu', ''));	
		echo "1";			
		
		// Close the application
		JFactory::getApplication()->close();
	}
	
	public function save_order_ajax_menus(){
	
		$db = JFactory::getDBO();
		
		JRequest::checkToken() or jexit('Invalid Token');
		
		$cid = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		
		if(count($cid)){			
			for($n = 0; $n < count($cid); $n++){
				//do update
				$query = $db->getQuery(true);		
				$query->update('#__adminmenumanager_menus');				
				$query->set('ordering='.(int)$order[$n]);
				$query->where('id='.(int)$cid[$n]);
				$db->setQuery((string)$query);
				$db->query();
			}			
			echo "1";
		}	
		
		// Close the application
		JFactory::getApplication()->close();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>