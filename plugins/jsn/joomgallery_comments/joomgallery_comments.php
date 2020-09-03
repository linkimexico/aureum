<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnJoomgallery_comments extends JPlugin
{
	public function renderTabs($data, $config)
	{
		require_once(JPATH_SITE.'/plugins/jsn/joomgallery/joomgallery.php');
		$plugin=array(JText::_($this->params->get('tabtitle','Gallery Comments')));
		$joomgalleryTab=new getGalleryCommentsTab();
		$joomgalleryTab->getGalleryCommentsTab();
		$joomgalleryTab->params=$this->params;
		$content=$joomgalleryTab->getDisplayTab(null,$data,null);
		//if(empty($content)) return null;
		$content.='
		    <style>
		      .justLoaded{display:none;}
		    </style>
		    <script>
		      function tabJoomgalleryCommentsLoadMore(page,total)
		      {
		        
		        jQuery("#tabJoomgalleryCommentsLoadMore").html("<img src=\"'.JURI::root().'media/plg_jsn_joomgallery/loading.gif\" style=\"height:50px;\"/>");
		        jQuery.get("index.php?option=com_jsn&view=loadmorejoomgallerycomments&format=raw&uid='.$data->id.'&page="+page+"&limit="+total,function( data ){jQuery("#tabJoomgalleryCommentsLoadMore").replaceWith(data);jQuery(".justLoaded").fadeIn(500).removeClass("justLoaded")});
		        
		      }
		    </script>
		    ';
		$plugin[]=$content;
		return $plugin;
	}

	public function renderPlugin()
  {
    $data=JFactory::getUser(JRequest::getInt('uid'));
    if(JRequest::getVar('view','')=='loadmorejoomgallerycomments')
    {
      $page=JRequest::getInt('page',0);
      $limit=JRequest::getInt('limit',0);
      JRequest::setVar('start',$page*$limit);
      JRequest::setVar('tab','commentstab_');
      $joomgalleryTab=new getGalleryCommentsTab();
      $joomgalleryTab->getGalleryCommentsTab();
      $joomgalleryTab->params=$this->params;
      $content=$joomgalleryTab->getDisplayTab(null,$data,null);
      echo $content;
    }
  }
}

?>