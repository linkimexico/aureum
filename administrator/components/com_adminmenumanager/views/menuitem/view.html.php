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

jimport( 'joomla.application.component.view');

class adminmenumanagerViewMenuitem extends JViewLegacy{
	
	protected $items;	
	protected $state;
	protected $pagination;		
	
	public function display($tpl = null){	
	
		$db = JFactory::getDBO();
		$ds = DIRECTORY_SEPARATOR;
		
		$controller = new adminmenumanagerController();	
		$this->assignRef('controller', $controller);
		
		$helper = $controller->get_helper();
		$this->assignRef('helper', $helper);
		
		//get menu id
		$id = intval(JRequest::getVar('id', ''));		
				
		//get data
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__adminmenumanager_menuitems');
		$query->where('id='.$id);			
		$menuitems = $db->setQuery($query, 0, 1);			
		$menuitems = $db->loadObjectList();	
		
		//set defaults for new
		$menuitem = (object)'';			
		$menuitem->id = 0;
		$menuitem->title = '';
		$menuitem->icon = '';	
		$menuitem->menu = intval(JRequest::getVar('menu', ''));
		$menuitem->url = '';
		$menuitem->published = '1';		
		$menuitem->parentid = '0';	
		$menuitem->level = '1';	
		$menuitem->type = '0';	
		$menuitem->target = '0';	
		$menuitem->width = '800';
		$menuitem->height = '600';
		$menuitem->constant = '';
		$menuitem->use_constant = '0';
		if($controller->get_version_type()=='free'){
			$menuitem->access = '1';
		}else{
			$default_access_type = 'default_access_'.$controller->amm_config['based_on'];
			$menuitem->access = $controller->amm_config[$default_access_type];
		}
			
		foreach($menuitems as $temp){
			$menuitem = $temp;	
			if($controller->amm_config['based_on']=='group'){				
				$menuitem->access = $temp->accessgroup;
			}else{
				$menuitem->access = $temp->accesslevel;
			}		
		}	
		$this->assignRef('menuitem', $menuitem);
		
		//include mod_menu language. Reuse or die ;-)#
		$lang = JFactory::getLanguage();
		$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_weblinks', JPATH_ADMINISTRATOR, null, false);
		$lang->load('plg_editors-xtd_article', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_installer', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_languages', JPATH_ADMINISTRATOR, null, false);
		
		$menus = $this->get_menus();	
		$this->assignRef('menus', $menus);
		
		$menuitems = $helper->get_menuitems($menuitem->menu, $menuitem->id);	
		$this->assignRef('menuitems', $menuitems);
		
		$default_template = $helper->template;	
		$this->assignRef('default_template', $default_template);
		
		$icons = $this->get_icons($default_template);	
		$this->assignRef('icons', $icons);
		
		$joomla_menus = $helper->get_joomla_menus();	
		$this->assignRef('joomla_menus', $joomla_menus);		
		
		$groups_levels = $helper->get_groups_levels();	
		$this->assignRef('groups_levels', $groups_levels);	
		
		//make components arrays for parents and children
		$helper->get_components();			
		$this->assignRef('components_array_parents', $helper->components_array_parents);
		$this->assignRef('components_array_children', $helper->components_array_children);		
		
		//toolbar		
		JToolBarHelper::apply('menuitem_apply', 'JToolbar_Apply');
		JToolBarHelper::save('menuitem_save', 'JToolbar_Save');		
		JToolBarHelper::save2new('menuitem_save_and_new');
		if($menuitem->id){
			JToolBarHelper::save2copy('menuitem_save_as_copy');
		}
		JToolBarHelper::cancel('cancel', 'JToolbar_Close');		
		
		if($helper->joomla_version >= '3.0'){
			//sidebar
			$this->add_sidebar($controller);	
		}						

		parent::display($tpl);
	}
	
	function get_menus(){	
						
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('id, name');
		$query->from('#__adminmenumanager_menus');		
		$menus = $db->setQuery((string)$query);				
		$menus = $db->loadObjectList();		
		
		return $menus;
	}	
	
	function get_icons($default_template){
		
		$db = JFactory::getDBO();
		$ds = DIRECTORY_SEPARATOR;
		
		$icons = array('-*'.$this->controller->amm_strtolower(JText::_('MOD_MENU_COMPONENTS')));
		$query = $db->getQuery(true);
		$query->select('img');
		$query->from('#__menu');
		$query->where('img NOT LIKE "%class:%"');		
		$temp = $db->setQuery((string)$query);				
		$temp = $db->loadColumn();		
		$temp = array_unique($temp);
		
		$icons = array_merge($icons, $temp);
		$icons[] = '-===end-td-tr';	
		
		jimport( 'joomla.filesystem.folder' );
		$backend_templates = JFolder::folders(JPATH_ROOT.$ds.'administrator'.$ds.'templates');			
		
		if($default_template){
			if(file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'templates'.$ds.$default_template.$ds.'images'.$ds.'menu')){
				$default_template_icons = JFolder::files(JPATH_ROOT.$ds.'administrator'.$ds.'templates'.$ds.$default_template.$ds.'images'.$ds.'menu');
				$default_template_icons = $this->icons_clean_array($default_template_icons, $default_template);
				if($default_template_icons){
					$icons[] = '-*'.$default_template.' template<br />(default template)';
					$icons = array_merge($icons, $default_template_icons);
					$icons[] = '-===end-td-tr';	
				}	
			}	
		}
		
		for($n = 0; $n < count($backend_templates); $n++){			
			if($backend_templates[$n]!=$default_template){
				if(file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'templates'.$ds.$backend_templates[$n].$ds.'images'.$ds.'menu')){
					$template_icons = JFolder::files(JPATH_ROOT.$ds.'administrator'.$ds.'templates'.$ds.$backend_templates[$n].$ds.'images'.$ds.'menu');
					$template_icons = $this->icons_clean_array($template_icons, $backend_templates[$n]);
					if($template_icons){
						$icons[] = '-*'.$backend_templates[$n].' template';
						$icons = array_merge($icons, $template_icons);	
						$icons[] = '-===end-td-tr';
					}	
				}	
			}
		}		
		return $icons;
	}
	
	function icons_clean_array($icons, $template_name){
	
		$clean_array = array();
		for($n = 0; $n < count($icons); $n++){
			if(strpos($icons[$n], 'con-16-')){
				$clean_array[] = 'templates/'.$template_name.'/images/menu/'.$icons[$n];
			}
		}
		return $clean_array;
	}
	
	function get_target_options($controller){
		$options = array();
		$options[] = JHtml::_('select.option', '0', JText::_('COM_ADMINMENUMANAGER_SAME_WINDOW'));
		$options[] = JHtml::_('select.option', '1', $controller->amm_strtolower(JText::_('JBROWSERTARGET_NEW')));
		$options[] = JHtml::_('select.option', '2', $controller->amm_strtolower(JText::_('JBROWSERTARGET_MODAL')));
		$options[] = JHtml::_('select.option', '3', $controller->amm_strtolower(JText::_('JBROWSERTARGET_POPUP')));
		return $options;
	}
	
	function add_sidebar($controller){
	
		JHtmlSidebar::setAction('index.php?option=com_adminmenumanager&view=configuration');	
				
		$controller->add_submenu();			
		
		$this->sidebar = JHtmlSidebar::render();
	}
	
	function icon_background($file, $type=''){
		$ds = DIRECTORY_SEPARATOR;
		$return = '';	
		if($type=='component'){
			if(file_exists(JPATH_ADMINISTRATOR.$ds.$file)){
				$return = ' rel="'.$file.'" style="background-image: url('.$file.');"';
			}
		}else{	
			if(file_exists(JPATH_ADMINISTRATOR.$ds.'templates'.$ds.$this->default_template.$ds.'images'.$ds.'menu'.$ds.$file)){
				$return = ' style="background-image: url(templates/'.$this->default_template.'/images/menu/'.$file.');"';
			}
		}
		return $return;
	}
	
	
	
	
	
	
}
?>