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

class adminmenumanagerViewMenuitemsexport extends JViewLegacy{		
	
	public function display($tpl = null){	
	
		$db = JFactory::getDBO();
		$controller = new adminmenumanagerController();	
		$this->assignRef('controller', $controller);
		
		$csv_string = $this->get_csv_string();
		$this->assignRef('csv_string', $csv_string);
		
		//include languages
		$lang = JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);	
		
		//toolbar
		JToolBarHelper::custom( 'back_to_menuitems', 'amm_back', 'back.png', JText::_('JTOOLBAR_BACK'), false, false);	//does not show icon in Joomla 3.
								

		parent::display($tpl);
	}
	
	function get_csv_string(){
		
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		
		require_once(JPATH_ROOT.'/administrator/components/com_adminmenumanager/helpers/adminmenumanager.php');
		$helper = new adminmenumanagerHelper();
			
		$cid = $app->getUserState( "com_adminmenumanager.cid", '');		
		$cid = explode(',', $cid);
		
		$return = '';	
		
		if (!is_array($cid) || count($cid) < 1) {
			//include languages
			$lang = JFactory::getLanguage();
			$lang->load('com_menu', JPATH_ADMINISTRATOR, null, false);
			echo JText::_('COM_MENUS_NO_ITEM_SELECTED');
			exit();
		}
		
		if (count($cid)){			
			//select menuitem(s)			
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__adminmenumanager_menuitems');
			$query->where('id IN (' . implode(',', $cid) . ')');
			$query->order('ordertotal');
			$rows = $db->setQuery($query);				
			$rows = $db->loadObjectList();			
			
			$delimiter = ',';
			$quote = '"';
			$newline = "\r\n";		
			
			$return .= $quote.'id'.$quote;	
			$return .= $delimiter;
			$return .= $quote.'title'.$quote;
			$return .= $delimiter;
			$return .= $quote.'icon'.$quote;
			$return .= $delimiter;
			$return .= $quote.'url'.$quote;
			$return .= $delimiter;	
			$return .= $quote.'published'.$quote;
			$return .= $delimiter;
			$return .= $quote.'parentid'.$quote;
			$return .= $delimiter;
			$return .= $quote.'level'.$quote;
			$return .= $delimiter;
			$return .= $quote.'ordering'.$quote;
			$return .= $delimiter;
			$return .= $quote.'ordertotal'.$quote;	
			$return .= $delimiter;
			$return .= $quote.'accessgroup'.$quote;	
			$return .= $delimiter;
			$return .= $quote.'accesslevel'.$quote;	
			$return .= $delimiter;
			$return .= $quote.'type'.$quote;	
			$return .= $delimiter;
			$return .= $quote.'target'.$quote;	
			$return .= $delimiter;
			$return .= $quote.'width'.$quote;	
			$return .= $delimiter;
			$return .= $quote.'height'.$quote;	
			$return .= $delimiter;
			$return .= $quote.'constant'.$quote;	
			$return .= $delimiter;
			$return .= $quote.'use_constant'.$quote;	
			$return .= $newline;
			
			for($i=0; $i < count( $rows ); $i++) {
				$row = $rows[$i];
				
				$title = $helper->string_export($row->title);	
				$url = $helper->string_export($row->url);	
				
				$return .= $quote.$row->id.$quote;	
				$return .= $delimiter;
				$return .= $quote.$title.$quote;
				$return .= $delimiter;
				$return .= $quote.$row->icon.$quote;
				$return .= $delimiter;
				$return .= $quote.$url.$quote;
				$return .= $delimiter;	
				$return .= $quote.$row->published.$quote;
				$return .= $delimiter;
				$return .= $quote.$row->parentid.$quote;
				$return .= $delimiter;
				$return .= $quote.$row->level.$quote;
				$return .= $delimiter;
				$return .= $quote.$row->ordering.$quote;
				$return .= $delimiter;	
				$return .= $quote.$row->ordertotal.$quote;
				$return .= $delimiter;	
				$return .= $quote.$row->accessgroup.$quote;
				$return .= $delimiter;	
				$return .= $quote.$row->accesslevel.$quote;
				$return .= $delimiter;	
				$return .= $quote.$row->type.$quote;
				$return .= $delimiter;	
				$return .= $quote.$row->target.$quote;
				$return .= $delimiter;	
				$return .= $quote.$row->width.$quote;
				$return .= $delimiter;	
				$return .= $quote.$row->height.$quote;
				$return .= $delimiter;	
				$return .= $quote.$row->constant.$quote;
				$return .= $delimiter;	
				$return .= $quote.$row->use_constant.$quote;
								
				$return .= $newline;
			}			
		}
		return $return;
	}
	
	
	
}
?>