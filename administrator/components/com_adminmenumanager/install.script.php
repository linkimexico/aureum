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

class com_adminmenumanagerInstallerScript {	

	public function postflight($type, $parent){
		
		$db = JFactory::getDBO();	
		$app = JFactory::getApplication();	
		$ds = DIRECTORY_SEPARATOR;
		$version = new JVersion;
		
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__adminmenumanager_config` (
  `id` varchar(255) NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;");
		$db->query();		
		
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__adminmenumanager_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `level_title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;");
		$db->query();
		
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__adminmenumanager_menuitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,  
  `icon` varchar(255) NOT NULL,
  `menu` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `published` int(11) NOT NULL,
  `parentid` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `ordertotal` int(11) NOT NULL,
  `accessgroup` int(11) NOT NULL,
  `accesslevel` int(11) NOT NULL,
  `type` int(1) NOT NULL,
  `target` int(1) NOT NULL,
  `width` INT( 4 ) NOT NULL DEFAULT  '800',
  `height` INT( 4 ) NOT NULL DEFAULT  '600',
  `constant` varchar(255) NOT NULL DEFAULT '',
  `use_constant` INT( 1 ) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;");
		$db->query();		
		
		
			
		$db->setQuery("SHOW COLUMNS FROM #__adminmenumanager_menuitems ");
		$columns = $db->loadColumn();	
			
		//added in version 1.2.0		
		if(!in_array('type', $columns)){
			$db->setQuery("ALTER TABLE #__adminmenumanager_menuitems ADD ".$db->qn('type')." INT( 1 ) NOT NULL, ADD ".$db->qn('target')." INT( 1 ) NOT NULL, ADD ".$db->qn('width')." INT( 4 ) NOT NULL DEFAULT  ".$db->q('800').", ADD ".$db->qn('height')." INT( 4 ) NOT NULL DEFAULT  ".$db->q('600')." ");			
			$db->query();		
		}				
		
		//added in version 2.2.0
		if(!in_array('constant', $columns)){
			$db->setQuery("ALTER TABLE #__adminmenumanager_menuitems 
			ADD  `constant` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL  DEFAULT '',
			ADD  `use_constant` INT(1)  NOT NULL DEFAULT  '0'
			");			
			$db->query();	
		}			
		
		//check if there are any menuitems
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__adminmenumanager_menuitems');		
		$rows = $db->setQuery($query);				
		$has_menuitems = $db->loadResult();
		
		if(!$has_menuitems){
			//no menuitems, so install default set
			$query = $db->getQuery(true);
			$query->insert('#__adminmenumanager_menuitems');
			$query->columns(array($db->qn('id'), $db->qn('title'), $db->qn('icon'), $db->qn('menu'), $db->qn('url'), $db->qn('published'), $db->qn('parentid'), $db->qn('level'), $db->qn('ordering'), $db->qn('ordertotal'), $db->qn('accessgroup'), $db->qn('accesslevel'), $db->qn('type'), $db->qn('target'), $db->qn('width'), $db->qn('height'), $db->qn('constant'), $db->qn('use_constant')));
			if($version->RELEASE >= '3.0'){
				$menu_manager_icon = 'components/com_adminmenumanager/images/com_adminmenumanager_menuitems.png';
			}else{
				$menu_manager_icon = 'templates/bluestork/images/menu/icon-16-menumgr.png';
			}
			$query->values($db->q('1').', '.$db->q('Menu Manager').', '.$db->q($menu_manager_icon).', '.$db->q('1').', '.$db->q('index.php?option=com_menus&view=menus').', '.$db->q('1').', '.$db->q('0').', '.$db->q('1').', '.$db->q('2').', '.$db->q('3').', '.$db->q('6').', '.$db->q('0').', '.$db->q('0').', '.$db->q('0').', '.$db->q('800').', '.$db->q('600').', '.$db->q('MOD_MENU_MENU_MANAGER').', '.$db->q('0'));
			if($version->RELEASE >= '3.0'){
				$content_manager_icon = 'templates/hathor/images/menu/icon-16-article.png';
			}else{
				$content_manager_icon = 'templates/bluestork/images/menu/icon-16-article.png';
			}
			$query->values($db->q('2').', '.$db->q('Article Manager').', '.$db->q($content_manager_icon).', '.$db->q('1').', '.$db->q('index.php?option=com_content').', '.$db->q('1').', '.$db->q('0').', '.$db->q('1').', '.$db->q('1').', '.$db->q('1').', '.$db->q('6').', '.$db->q('0').', '.$db->q('0').', '.$db->q('0').', '.$db->q('800').', '.$db->q('600').', '.$db->q('MOD_MENU_COM_CONTENT_ARTICLE_MANAGER').', '.$db->q('0'));
			if($version->RELEASE >= '3.0'){
				$content_new_icon = 'templates/hathor/images/menu/icon-16-new.png';
			}else{
				$content_new_icon = 'templates/bluestork/images/menu/icon-16-newarticle.png';
			}
			$query->values($db->q('3').', '.$db->q('Add New Article').', '.$db->q($content_new_icon).', '.$db->q('1').', '.$db->q('index.php?option=com_content&view=article&layout=edit').', '.$db->q('1').', '.$db->q('2').', '.$db->q('2').', '.$db->q('1').', '.$db->q('2').', '.$db->q('6').', '.$db->q('0').', '.$db->q('0').', '.$db->q('0').', '.$db->q('800').', '.$db->q('600').', '.$db->q('MOD_MENU_COM_CONTENT_NEW_ARTICLE').', '.$db->q('0'));			
			$db->setQuery((string)$query);
			$db->query();
		}
		
		$db->setQuery("CREATE TABLE IF NOT EXISTS `#__adminmenumanager_menus` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` varchar(150) NOT NULL,
  `ordering` INT NOT NULL DEFAULT  '1',
  PRIMARY KEY (`id`)
) DEFAULT COLLATE utf8_general_ci CHARSET=utf8;");
		$db->query();
		
		//check if there are any menu's
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__adminmenumanager_menus');		
		$rows = $db->setQuery($query);				
		$has_menus = $db->loadResult();
		
		//if no menus found, insert default menu
		if(!$has_menus){
			$query = $db->getQuery(true);
			$query->insert('#__adminmenumanager_menus');
			$query->set('name='.$db->q('default'));	
			$query->set('ordering=1');					
			$db->setQuery((string)$query);
			$db->query();
		}			
		
		//added in version 1.2.0
		$version = new JVersion;
		if($version->RELEASE < '3.0'){
			$db->setQuery("SHOW COLUMNS FROM #__adminmenumanager_menus ");
			$columns = $db->loadColumn();			
			if(!in_array('ordering', $columns)){
				$db->setQuery("ALTER TABLE #__adminmenumanager_menus ADD `ordering` INT NOT NULL DEFAULT  '1' ");			
				$db->query();	
				//create ordering for existing menus from name order
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__adminmenumanager_menus');		
				$query->order('name');
				$items = $db->setQuery($query);				
				$items = $db->loadObjectList();
				$i = 1;
				foreach($items as $item){
					$temp_id = $item->id;
					//update order					
					$query = $db->getQuery(true);		
					$query->update('#__adminmenumanager_menus');
					$query->set('ordering='.$i);				
					$query->where('id='.$temp_id);
					$db->setQuery($query);
					$db->query();						
					$i++;
				}		
				unset($items);
			}
		}
		
		
		//check if config is empty, if so insert default config
		$query = $db->getQuery(true);
		$query->select('config');
		$query->from('#__adminmenumanager_config');
		$query->where('id='.$db->q('amm'));		
		$amm_config = $db->setQuery($query, 0, 1);				
		$amm_config = $db->loadResult();		
		
		if(!$amm_config){			
			$configuration = '{"level_sort":"ordering","version_checker":"true","based_on":"group","super_user_sees_all":"true","default_access_group":"1","default_access_level":"1","access_enabled":"1","group_inheritance":"1","multilanguage":"0"}';
			
			//insert fresh config			
			$query = $db->getQuery(true);
			$query->insert('#__adminmenumanager_config');
			$query->set('id='.$db->q('amm'));
			$query->set('config='.$db->q($configuration));			
			$db->setQuery((string)$query);
			$db->query();
	
		}else{
			//there is a config already
			//update if needed
			$new_config = '';
			$config_needs_updating = 0;	


			
			//added in version 1.1.0
			if(!strpos($amm_config, '"default_access_group"')){				
				$new_config .= ',"default_access_group":"1","default_access_level":"1"';		
				$config_needs_updating = 1;				
			}	
			
			//added in version 1.2.0
			if(!strpos($amm_config, '"access_enabled"')){				
				$new_config .= ',"access_enabled":"1"';		
				$config_needs_updating = 1;				
			}
			
			//added in version 2.1.0
			if(!strpos($amm_config, '"group_inheritance"')){				
				$new_config .= ',"group_inheritance":"1"';		
				$config_needs_updating = 1;				
			}
			
			//added in version 2.2.0
			if(!strpos($amm_config, '"multilanguage"')){				
				$new_config .= ',"multilanguage":"0"';		
				$config_needs_updating = 1;				
			}		
			
			if($config_needs_updating){
				$temp = trim($amm_config);
				$config_lenght = strlen($temp);
				$open_ending = substr($temp, 0, $config_lenght-1);				
				$updated_config = $open_ending.$new_config.'}';					
			
				$query = $db->getQuery(true);		
				$query->update('#__adminmenumanager_config');
				$query->set('config='.$db->q($updated_config));						
				$query->where('id='.$db->q('amm'));
				$db->setQuery((string)$query);
				$db->query();
			}		
		}	
		
		//delete deprecated files from previous versions
		$deprecated_files = array();
		$deprecated_files[] = JPATH_ROOT.$ds.'administrator'.$ds.'modules'.$ds.'mod_adminmenumanager'.$ds.'css'.$ds.'isis.css';	
		$latest_version_css = 9;
		for($n = 1; $n < $latest_version_css; $n++){			
			$deprecated_files[] = JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_adminmenumanager'.$ds.'css'.$ds.'adminmenumanager'.$n.'.css';
		}		
		foreach($deprecated_files as $deprecated_file){
			if(file_exists($deprecated_file)){
				JFile::delete($deprecated_file);
			}
		}		
		
		$this->pi_install_module('mod_adminmenumanager', $parent);
		
		//reset version checker session var		
		$app->setUserState( "com_adminmenumanager.latest_version_message", '' );	
		
		//fix extension update url
		$update_url = '';			
		$xml_file = JPATH_SITE.'/administrator/components/com_adminmenumanager/adminmenumanager.xml';
		$version = new JVersion;
		if($version->RELEASE < '3.0'){		
			$xml = JFactory::getXML($xml_file, true);		
		}else{
			$xml = simplexml_load_file($xml_file);
		}
		foreach($xml->children() as $updateservers){			
			foreach($updateservers->children() as $updateserver){				
				$update_url = $updateserver;
			}
		}
		if($update_url){
			$query = $db->getQuery(true);		
			$query->update('#__update_sites');
			$query->set('location='.$db->q($update_url));					
			$query->where('name='.$db->q('com_adminmenumanager'));
			$db->setQuery((string)$query);
			$db->query();
		}
		
		$this->display_install_page();
						
	}	
	
	function pi_install_module($element, $parent){
		$ds = DIRECTORY_SEPARATOR;
		$installer = new JInstaller();
		$item = $parent->getParent()->getPath('source').$ds.'mod_adminmenumanager';	
		$mod_adminmenumanager_installed = $installer->install($item) ? 1 : 0;
		
		echo '<div style="color: green;">';
		if($mod_adminmenumanager_installed){	
			echo 'menu module successfully installed';
		}		
		if($this->publish_menu_module_if_new()){
			echo '<br />';
			echo 'menu module successfully published';
		}		
		echo '</div>';
	}
	
	public function uninstall($installer){
	
		$db = JFactory::getDBO();
		
		$this->pi_uninstall_module('mod_adminmenumanager');

		//delete tables
		$tables_to_drop = array();
		$tables_to_drop[] = '#__adminmenumanager_config';
		$tables_to_drop[] = '#__adminmenumanager_map';
		$tables_to_drop[] = '#__adminmenumanager_menus';
		$tables_to_drop[] = '#__adminmenumanager_menuitems';
		for($n = 0; $n < count($tables_to_drop); $n++){
			$query = $db->getQuery(true);
			$query = 'DROP TABLE IF EXISTS '.$db->quoteName($tables_to_drop[$n]);
			$db->setQuery((string)$query);
			$db->query();
		}
		
		$this->display_uninstall_page();		
    }	
	
	function pi_uninstall_module($element){

		$db = JFactory::getDBO();
			
		//get module id for uninstall
		$query = $db->getQuery(true);
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where('element='.$db->q($element));
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();
		
		//uninstall	
		$module_uninstall_success = 0;
		$module_found = 0;
		foreach($rows as $row){
			$installer = new JInstaller();			
			$module_uninstall_success = $installer->uninstall('module', $row->extension_id);
			$module_found = 1;
		}	
		
		//display message
		if($module_uninstall_success){
			echo '<p style="color: #5F9E30;">menu module succesfully uninstalled</p>';		
		}else{
			if($module_found){
				echo '<p style="color: red;">could not uninstall menu module</p>';
			}else{
				echo '<p style="color: #5F9E30;">menu module was already uninstalled</p>';
			}
		}
	}
	
	function display_install_page(){
		?>
<div style="width: 800px; text-align: left; background: url(components/com_adminmenumanager/images/icon.png) 10px 0 no-repeat;">
	<h2 style="padding: 10px 0 10px 70px;">Admin-Menu-Manager</h2>
	<div style="width: 1000px; overflow: hidden;">	
		<div style="width: 270px; float: left;">
			<p>
				Thank you for using Admin-Menu-Manager.		
			</p>
			<p>
				<input type="button" value="Go to Admin-Menu-Manager" onclick="document.location.href='index.php?option=com_adminmenumanager';" />				
			</p>
		</div>
		<div style="width: 380px; float: left;">
			<p>
				With Admin-Menu-Manager you can create custom menus for the Joomla backend.							
			</p>								
		</div>
		<div style="width: 330px; float: left;">
			<p>
				Check <a href="http://www.pages-and-items.com" target="_blank">www.pages-and-items.com</a> for:
			<ul>
				<li><a href="http://www.pages-and-items.com/extensions/admin-menu-manager" target="_blank">updates</a></li>
				<li><a href="http://www.pages-and-items.com/extensions/admin-menu-manager/faqs" target="_blank">FAQs</a></li>	
				<li><a href="http://www.pages-and-items.com/forum/43-admin-menu-manager" target="_blank">support forum</a></li>	
				<li><a href="http://www.pages-and-items.com/my-account/email-update-notifications" target="_blank">email notification service for updates and new extensions</a></li>	
				<li><a href="http://www.pages-and-items.com/extensions/admin-menu-manager/update-notifications-for-admin-menu-manager" target="_blank">subscribe to RSS feed update notifications</a></li>			
			</ul>
			</p>	
			<p>
				Follow us on <a href="http://www.twitter.com/PagesAndItems" target="_blank">Twitter</a> (only update notifications).
			</p>
		</div>
	</div>
</div>
		<?php
	}
	
	function display_uninstall_page(){
		?>
<div style="width: 500px; text-align: left;">
	<h2 style="padding-left: 10px;">Admin-Menu-Manager</h2>	
	<p>
		Thank you for having used Admin-Menu-Manager.
	</p>
	<p>
		Why did you uninstall Admin-Menu-Manager? Missing any features? <a href="http://www.pages-and-items.com/" target="_blank">Let us know</a>.		
	</p>	
	<p>
		Check <a href="http://www.pages-and-items.com/" target="_blank">www.pages-and-items.com</a> for:
		<ul>
			<li><a href="http://www.pages-and-items.com/extensions/admin-menu-manager" target="_blank">updates</a></li>
			<li><a href="http://www.pages-and-items.com/extensions/admin-menu-manager/faqs" target="_blank">FAQs</a></li>	
			<li><a href="http://www.pages-and-items.com/forum/43-admin-menu-manager" target="_blank">support forum</a></li>	
			<li><a href="http://www.pages-and-items.com/my-account/email-update-notifications" target="_blank">email notification service for updates and new extensions</a></li>	
			<li><a href="http://www.pages-and-items.com/extensions/admin-menu-manager/update-notifications-for-admin-menu-manager" target="_blank">subscribe to RSS feed update notifications</a></li>			
		</ul>
	</p>	
</div>
		<?php
	}
	
	function publish_menu_module_if_new(){
	
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('id, params');
		$query->from('#__modules');
		$query->where('module='.$db->q('mod_adminmenumanager'));
		$modules = $db->setQuery($query);				
		$modules = $db->loadObjectList();
		
		if(count($modules)==1){
			//only one module found, this could be a fresh install
			
			foreach($modules as $module){			
				if($module->params==''){
					//empty params, that must be new
					//so lets update
					$version = new JVersion;
					if($version->RELEASE >= '3.0'){
						$default_cursor = 'hand';
					}else{
						$default_cursor = 'arrow';
					}
					$params = '{"adminmenumanagermenu":"1","allow_left":"1","allow_right":"1","style":"auto","extracss":"","adminmenumanagerdisable":"1","class_sfx":"","adminmenumanagercursor":"'.$default_cursor.'"}';
					$query = $db->getQuery(true);		
					$query->update('#__modules');
					$query->set('ordering='.$db->q('99'));
					$query->set('position='.$db->q('menu'));	
					$query->set('published='.$db->q('1'));	
					$query->set('access='.$db->q('3'));
					$query->set('params='.$db->q($params));		
					$query->where('id='.$module->id);		
					$db->setQuery($query);
					$db->query();
					
					//display on each page (else nothing happens, even thou this is all backend)
					$query = $db->getQuery(true);
					$query->insert('#__modules_menu');
					$query->set('moduleid='.$module->id);		
					$query->set('menuid='.$db->q('0'));			
					$db->setQuery((string)$query);
					$db->query();
					
					return true;
					
				}
			}
		}
	}
}

?>