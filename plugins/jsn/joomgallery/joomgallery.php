<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnJoomgallery extends JPlugin
{
	public function renderTabs($data, $config)
	{
		$plugin=array(JText::_($this->params->get('tabtitle','Gallery')));
		$joomgalleryTab=new getGalleryTab();
		$joomgalleryTab->getGalleryTab();
		$joomgalleryTab->params=$this->params;
		$content=$joomgalleryTab->getDisplayTab(null,$data,null);
		if(empty($content)) return null;
    $content.='
    <style>
      .justLoaded{display:none;}
    </style>
    <script>
      function tabJoomgalleryLoadMore(page,total)
      {
        
        jQuery("#tabJoomgalleryLoadMore").html("<img src=\"'.JURI::root().'media/plg_jsn_joomgallery/loading.gif\" style=\"height:50px;\"/>");
        jQuery.get("index.php?option=com_jsn&view=loadmorejoomgallery&format=raw&uid='.$data->id.'&page="+page+"&limit="+total,function( data ){jQuery("#tabJoomgalleryLoadMore").replaceWith(data);jQuery(".justLoaded").fadeIn(500).removeClass("justLoaded")});
        
      }
    </script>
    ';
		$plugin[]=$content;
		return $plugin;
	}

  public function renderPlugin()
  {
    $data=JFactory::getUser(JRequest::getInt('uid'));
    if(JRequest::getVar('view','')=='loadmorejoomgallery')
    {
      $page=JRequest::getInt('page',0);
      $limit=JRequest::getInt('limit',0);
      JRequest::setVar('start',$page*$limit);
      JRequest::setVar('tab','gallerytab_');
      $joomgalleryTab=new getGalleryTab();
      $joomgalleryTab->getGalleryTab();
      $joomgalleryTab->params=$this->params;
      $content=$joomgalleryTab->getDisplayTab(null,$data,null);
      echo $content;
    }
  }
}

class getGalleryTab{
  var $_interface;
  var $_mainframe;
  var $_my;
  var $params;

  function getGalleryTab()
  {
    $this->_mainframe = JFactory::getApplication();
    $this->_my = JFactory::getUser();

    $params   = JComponentHelper::getParams('com_languages');
    $frontend_lang = $params->get('site', 'en-GB');
    $joom_interface_path = JPATH_SITE . "/components/com_joomgallery/interface.php" ;

    // See if JoomGallery is present
    if(!file_exists(JPATH_SITE . "/components/com_joomgallery/interface.php"))
    {
      return "Error: JoomGallery not found. Is the component installed?";
    }

    include_once($joom_interface_path);
    $this->_interface = new JoomInterface();

    if($this->_interface->getGalleryVersion() < "2.0")
    {
      return "Error: Version of JoomGallery incompatible. Please upgrade to version 2.0 or newer)";
    }

    $pluginPath = JPATH_SITE."/plugins/jsn/joomgallery/language/";
    if (file_exists($pluginPath.$frontend_lang.".php"))
    {
      include_once($pluginPath.$frontend_lang.".php");
    }
    else
    {
      include_once($pluginPath."en-GB.php");
    }
  }
/**
 * Creates and returns the header of the Gallery tab
 * conatining No. of images, and upload-link if configured
 **/
  function getMainHeader($own, $total, $showRating = 0, $ratingStats = NULL)
  {
    $return = '<p>';
    if (intval($total) == 0){
      $return.=_JG_GT_USER_NO_IMAGES."<br />";
    }
    else
    {
      $return.= sprintf(_JG_GT_TOTAL_IMAGES, $total)."<br />";
      if ($showRating)
      {
        if ($ratingStats->numvotes > 0)
        {
          $avg_rating = number_format($ratingStats->votesum / $ratingStats->numvotes, 2);
        }
        else
        {
          $avg_rating = 0;
        }
        $return .= sprintf(_JG_GT_OVERALL_RATING, $avg_rating, $ratingStats->numvotes)."<br />";
      }
    }
    $gallery_link = JRoute::_("index.php?option=com_joomgallery&view=userpanel".$this->_interface->getJoomId());
    $return .=($own)?sprintf(_JG_GT_UPLOAD_IMAGES, $gallery_link):"";
    $return.= "</p>";

    return $return;
  }

  function listRows($rows, $numcols=1)
  {
    $elem_width =  floor(99 / $numcols);
    $this->_interface->getPageHeader();

    if (empty($rows))
    {
      return "";
    }

    $return = "";
    $return.= "\n<div class=\"gallerytab\">\n";
    $return.= "<div class=\"jg_row sectiontableentry2\" >";
    $rowcount = 0;
    $itemcount = 0;

    foreach ( $rows as $row1 )
    {
      if (($itemcount % $numcols == 0) && ($itemcount != 0))
      {
        $return.="</div> <div class=\"jg_row sectiontableentry". ($rowcount % 2 +1) ."\">\n";
        $rowcount++;
      }
      $return .= "<div class=\"jg_element_cat\" style=\"width:$elem_width%\">\n";
      $return .= "  ".$this->_interface->displayThumb($row1);
      $return .= "  <div class =\"jg_catelem_txt\">\n";
      $return .= "    ".$this->_interface->displayDesc($row1);
      $return .= "  </div>\n";
      $return.= "</div>\n";
      $itemcount++;
    }
    $return.= "</div>\n</div>";
    return $return;
  }


/**
 * Main function, displays the Gallery-Tab in Community Builder
 **/
  function getDisplayTab($tab, $user, $ui)
  {
    $database = JFactory::getDBO();
    $params = $this->params;

    // Set up JG-Interface options:
    if($params->get('hidebackend', '0'))
    {
      $this->_interface->addConfig("hidebackend", "true");
    }
    $this->_interface->addConfig("categoryfilter", $params->get('categoryfilter',''));
    $this->_interface->addConfig("showcategory", $params->get('showCategory','1')==1);
    $this->_interface->addConfig("showhits", $params->get('showhits','1')==1);
    $this->_interface->addConfig("shownumcomments", $params->get('shownumcomments','1')==1);
    $this->_interface->addConfig("showrate", $params->get('showrate','1')==1);
	$this->_interface->addConfig("showdownloads", $params->get('showdownloads','1')==1);
    $this->_interface->addConfig("showauthor", "0");

    //String to return for output
    $return="";
    /*if($tab->description != null)
    {
      $return .= "\t\t<div class=\"tab_Description\">".unHtmlspecialchars(getLangDefinition($tab->description))."</div>\n";
    }*/
    $total=$this->_interface->getNumPicsOfUser($user->id);

    // Hide tab if no images and it is configured to hide:
    if(($total == 0) && ($params->get('showEmptyTab','1') == 0))
    {
      return null;
    }
    // Display overall user rating, if wanted
    $showRatingStats = $params->get('showUserRating', 1);
    $ratingStats     = array();
    if($showRatingStats)
    {
      $query = "SELECT
                  sum(imgvotes) AS numvotes, sum(imgvotesum) AS votesum
                FROM
                  #__joomgallery
                WHERE
                  owner = $user->id";
      $database->setQuery($query);
      $ratingStats = $database->loadObject();
    }

    // Get number of columns, if not setted in backend take the JG setting category view
    $numcols = intval($params->get('numcols', 0));
    if ($numcols < 1)
    {
      $numcols = $this->_interface->getJConfig('jg_colnumb');
    }

    // Get number of images per page, if not setted in backend take the JG setting category view
    $picsperpage = $params->get('picsperpage','');
    if (!$picsperpage)
    {
      $picsNumber = $this->_interface->getJConfig('jg_perpage');
    }
    else
    {
      $picsNumber = $picsperpage;
    }
    $pagingParams = $this->_getPaging(array(),array("gallerytab_"));
    if ($pagingParams["gallerytab_limitstart"] === null)
    {
      $pagingParams["gallerytab_limitstart"] = "0";
    }
    if ($picsNumber > $total)
    {
      $pagingParams["gallerytab_limitstart"] = "0";
    }

    // Include Header (Information) on first page:
    $showUploadLink = (($params->get('uploadLinkEnabled', '1')==1) && ($user->id == $this->_my->id));
    if ($pagingParams["gallerytab_limitstart"] == "0")
    {
      $return .= $this->getMainHeader($showUploadLink, $total, $showRatingStats, $ratingStats);
    }
    // output nothing else when there are no pics:
    if ($total == 0)
    {
      return $return;
    }
    $limitstart = $pagingParams["gallerytab_limitstart"]?$pagingParams["gallerytab_limitstart"]:"0";
    $rows = $this->_interface->getPicsOfUser($user->id, null, $params->get('sortBy','jg.catid ASC'), $picsNumber,$limitstart);

    $return .= $this->listRows($rows, $numcols);

    // Pagination, if configured to show:
    $showPagination = ($params->get('pagination','1')=='1');
    if ($showPagination &&($picsNumber < $total))
    {
      /*$return .= "<div style='width:95%;text-align:center;'>"
      .$this->_writePaging($pagingParams,"gallerytab_",$picsNumber,$total)
      ."</div>";*/
      $return .= $this->_writePaging($pagingParams,"gallerytab_",$picsNumber,$total,'');
    }

    return $return;
  }
  
	function _getPaging( $additionalParams = null, $postfixArray = null ) {
		$return					=	array();

		if ( $additionalParams === null ) {
			$additionalParams	=	array();
		}
		if ( $postfixArray === null ) {
			$postfixArray		=	array();
		}

		if (!is_array($postfixArray)) {
			if (is_string($postfixArray)) {
				$postfixArray = array($postfixArray);
			} else {
				$postfixArray = array("");
			}
		}
		
		foreach ($postfixArray as $postfix) {
			if(isset($_GET['tab']) && $_GET['tab']==$postfix)
				$limitstart						=	(isset($_GET['start']) ? $_GET['start'] : null);
			else
				$limitstart=0;
			$return[$postfix."limitstart"]	=	( $limitstart === null ? null : (int) $limitstart );
		}
		return $return;
	}

	function _writePaging( $pagingParams, $postfix, $limit, $total, $task = 'userProfile' )
	{
		/*$pageHtml='';
		if(isset($_GET['tab']) && $_GET['tab']==$postfix){
			$limitstart						=	(isset($_GET['start']) ? $_GET['start'] : null);
			$pageHtml='
				<script>
				jQuery(document).ready(function(){
					if(jQuery("#'.$postfix.'profileTabJoomgallery").length>0) jQuery("#'.$postfix.'profileTabJoomgallery").parent().click();
				});
				</script>
			';
		}
		else
			$limitstart=0;
		$page = new JPagination($total, $limitstart, $limit);
		$pageHtml.=$page->getListFooter();
		//$pageHtml=str_replace('limitstart=','tab='.$postfix.'&amp;limitstart=',$pageHtml);
		$pageHtml=str_replace('?start','?tab='.$postfix.'&amp;start',$pageHtml);
		$pageHtml=str_replace('class="limit','style="display:none;" class="limit',$pageHtml);
    return $pageHtml;*/

		$page=JRequest::getInt('page',0)+1;
    if($total<$page*$limit) return;
    $return=array();
    $return[]='<div style="text-align:center" id="tabJoomgallery'.$task.'LoadMore" '.(JRequest::getVar('format','')=='raw' ? 'class="justLoaded"' : '').'><a class="btn btn-default" href="#" onclick="tabJoomgallery'.$task.'LoadMore('.$page.','.$limit.');return false;">'._JG_GT_LOADMORE.'</a></div>';
    return implode('',$return);
    
	}
}

/**
 * The GalleryTagsTab class
 *
 * Outputs the images a user is tagged in
 */
class getGalleryTagsTab extends getGalleryTab
{
  function getGalleryTagsTab()
  {
    $this->getGalleryTab();
  }

  function getHeader($total)
  {
    $return = '';

    if (intval($total) == 0)
    {
      $return.="<p>"._JG_GT_USER_NO_TAGGED."</p>";
    }
    else {
        $return.= "<p>".sprintf(_JG_GT_TOTAL_TAGGED, $total) ."</p>";
    }

    return $return;
  }

  function getDisplayTab($tab, $user, $ui)
  {
    $params = $this->params;

    // Set up JG-Interface options:
    if($params->get('hidebackend', '0'))
    {
      $this->_interface->addConfig("hidebackend", "true");
    }
    $this->_interface->addConfig("categoryfilter", $params->get('categoryfilter',''));
    $this->_interface->addConfig("showcategory", $params->get('showCategory','1')==1);
    $this->_interface->addConfig("showhits", $params->get('showhits','1')==1);
    $this->_interface->addConfig("shownumcomments", $params->get('shownumcomments','1')==1);
    $this->_interface->addConfig("showrate", $params->get('showrate','1')==1);
	$this->_interface->addConfig("showdownloads", $params->get('showdownloads','1')==1);
    $this->_interface->addConfig("showcatlink", 1);

    $return="";  //String to return for output
    /*if($tab->description != null)
    {
      $return .= "\t\t<div class=\"tab_Description\">".unHtmlspecialchars(getLangDefinition($tab->description))."</div>\n";
    }*/
    $total=$this->_interface->getNumPicsUserTagged($user->id);

    // Hide tab if no pics and it is configured to hide:
    if (($total==0) && ($params->get('showEmptyTab','1')==0))
    {
      return null;
    }

    $numcols = intval($params->get('numcols', 0));
    if ($numcols < 1)
    {
      $numcols = $this->_interface->getJConfig('jg_colnumb');
    }

    // Pagination:
    $picsperpage = $params->get('picsperpage','');
    if (!$picsperpage)
    {
      $picsNumber = $this->_interface->getJConfig('jg_perpage');
    }
    else
    {
      $picsNumber = $picsperpage;
    }
    $pagingParams = $this->_getPaging(array(),array("gallerytagstab_"));
    if ($pagingParams["gallerytagstab_limitstart"] === null)
    {
      $pagingParams["gallerytagstab_limitstart"] = "0";
    }
    if ($picsNumber > $total)
    {
      $pagingParams["gallerytagstab_limitstart"] = "0";
    }

    // Include Header (Information) on first page:
    if ($pagingParams["gallerytagstab_limitstart"] == "0")
    {
      $return .= $this->getHeader($total);
    }

    // output nothing else when there are no pics:
    if ($total == 0)
    {
      return $return;
    }

    $limitstart = $pagingParams["gallerytagstab_limitstart"]?$pagingParams["gallerytagstab_limitstart"]:"0";
    $rows = $this->_interface->getPicsUserTagged($user->id, null, $params->get('sortBy','jg.catid ASC'), $picsNumber,$limitstart);
    $return .= $this->listRows($rows, $numcols);

    // Pagination, if configured to show:
    $showPagination = ($params->get('pagination','1')=='1');
    if ($showPagination &&($picsNumber < $total)) {
      /*$return .= "<div style='width:95%;text-align:center;'>"
      .$this->_writePaging($pagingParams,"gallerytagstab_",$picsNumber,$total)
      ."</div>";*/
      $return .= $this->_writePaging($pagingParams,"gallerytab_",$picsNumber,$total,'Tags');
    }

    return $return;
  }
}

/**
 * The GalleryFavouritesTab class
 *
 * Outputs a user's favourite images in JoomGallery
 */
class getGalleryFavouritesTab extends getGalleryTab
{
  function getGalleryFavouritesTab()
  {
    $this->getGalleryTab();
  }

  function getHeader($own,$total)
  {
    $return = '';

    if (intval($total) == 0)
    {
      $return.="<p>". _JG_GT_USER_NO_FAV."<br/>";
    }
    else
    {
      $return.= "<p>".sprintf(_JG_GT_TOTAL_FAV, $total)."<br/>";
    }
    $gallery_link = JRoute::_("index.php?option=com_joomgallery&view=favourites".$this->_interface->getJoomId());
    $return .=($own)?sprintf(_JG_GT_MANAGE_FAVOURITES, $gallery_link):"";
    $return.= "</p>";
    return $return;
  }

  function getDisplayTab($tab, $user, $ui)
  {
    $params = $this->params;

    // Set up JG-Interface options:
    if($params->get('hidebackend', '0'))
    {
      $this->_interface->addConfig("hidebackend", "true");
    }
    $this->_interface->addConfig("categoryfilter", $params->get('categoryfilter',''));
    $this->_interface->addConfig("showcategory", $params->get('showCategory','1')==1);
    $this->_interface->addConfig("showhits", $params->get('showhits','1')==1);
    $this->_interface->addConfig("shownumcomments", $params->get('shownumcomments','1')==1);
    $this->_interface->addConfig("showrate", $params->get('showrate','1')==1);
	$this->_interface->addConfig("showdownloads", $params->get('showdownloads','1')==1);
    $this->_interface->addConfig("showcatlink", 1);

    $ownProfile = (($user->id == $this->_my->id));

    // Hide tab if not viewed by user and not public:
    if (!$ownProfile && $params->get('showPublic', '1') == 0)
    {
      return null;
    }

    $total=$this->_interface->getNumPicsUserFavoured($user->id);

    // Hide tab if no pics and it is configured to hide:
    if (($total==0) && ($params->get('showEmptyTab','1')==0))
    {
      return null;
    }

    //String to return for output
    $return="";
    /*if($tab->description != null)
    {
      $return .= "\t\t<div class=\"tab_Description\">".unHtmlspecialchars(getLangDefinition($tab->description))."</div>\n";
    }*/

    $numcols = intval($params->get('numcols', 0));
    if ($numcols < 1)
    {
      $numcols = $this->_interface->getJConfig('jg_colnumb');
    }

    // Pagination:
    $picsperpage = $params->get('picsperpage','');
    if (!$picsperpage)
    {
      $picsNumber = $this->_interface->getJConfig('jg_perpage');
    }
    else
    {
      $picsNumber = $picsperpage;
    }

    $pagingParams = $this->_getPaging(array(),array("galleryfavstab_"));
    if ($pagingParams["galleryfavstab_limitstart"] === null)
    {
      $pagingParams["galleryfavstab_limitstart"] = "0";
    }
    if ($picsNumber > $total)
    {
      $pagingParams["galleryfavstab_limitstart"] = "0";
    }

    // Include Header (Information) on first page:
    if ($pagingParams["galleryfavstab_limitstart"] == "0")
    {
      $return .= $this->getHeader($ownProfile, $total);
    }

    // output nothing else when there are no pics:
    if ($total == 0)
    {
      return $return;
    }
    $limitstart = $pagingParams["galleryfavstab_limitstart"]?$pagingParams["galleryfavstab_limitstart"]:"0";
    $rows = $this->_interface->getPicsUserFavoured($user->id, null, $params->get('sortBy','jg.catid ASC'), $picsNumber,$limitstart);

    $return .= $this->listRows($rows, $numcols);

    // Pagination, if configured to show:
    $showPagination = ($params->get('pagination','1')=='1');
    if ($showPagination &&($picsNumber < $total))
    {
      /*$return .= "<div style='width:95%;text-align:center;'>"
      .$this->_writePaging($pagingParams,"galleryfavstab_",$picsNumber,$total)
      ."</div>";*/
      $return .= $this->_writePaging($pagingParams,"gallerytab_",$picsNumber,$total,'Favs');
    }

    return $return;
  }
}

class getGalleryCommentsTab extends getGalleryTab
{
  function getGalleryCommentsTab()
  {
    $this->getGalleryTab();
  }

  function getHeader($total)
  {
    $return = '';

    if (intval($total) == 0)
    {
      $return.="<p>"._JG_GT_USER_NO_COMMENTS."</p>";
    }
    else {
        $return.= "<p>".sprintf(_JG_GT_TOTAL_COMMENTS, $total) ."</p>";
    }

    return $return;
  }

  function getDisplayTab($tab, $user, $ui)
  {
    $params = $this->params;

    // Set up JG-Interface options:
    if($params->get('hidebackend', '0'))
    {
      $this->_interface->addConfig("hidebackend", "true");
    }
    $this->_interface->addConfig("showcmttext", $params->get('showcmttext','1')==1);
    $this->_interface->addConfig("showcmtdate", $params->get('showcmtdate','1')==1);

    // String to return for output
    $return="";
    /*if($tab->description != null)
    {
      $return .= "\t\t<div class=\"tab_Description\">".unHtmlspecialchars(getLangDefinition($tab->description))."</div>\n";
    }*/

    $total=$this->_interface->getNumCommentsUser($user->id);

    // Hide tab if no pics and it is configured to hide:
    if (($total==0) && ($params->get('showEmptyTab','1')==0))
    {
      return null;
    }

    $numcols = intval($params->get('numcols', 0));
    if ($numcols < 1)
    {
      $numcols = $this->_interface->getJConfig('jg_colnumb');
    }

    // Pagination:
    $picsperpage = $params->get('picsperpage','');
    if (!$picsperpage)
    {
      $picsNumber = $this->_interface->getJConfig('jg_perpage');
    }
    else
    {
      $picsNumber = $picsperpage;
    }

    $pagingParams = $this->_getPaging(array(),array("commentstab_"));
    if ($pagingParams["commentstab_limitstart"] === null)
    {
      $pagingParams["commentstab_limitstart"] = "0";
    }
    if ($picsNumber > $total)
    {
      $pagingParams["commentstab_limitstart"] = "0";
    }

    // Include Header (Information) on first page:
    if ($pagingParams["commentstab_limitstart"] == "0")
    {
      $return .= $this->getHeader($total);
    }

    // Output nothing else when there are no pics:
    if ($total == 0)
    {
      return $return;
    }

    $limitstart = $pagingParams["commentstab_limitstart"]?$pagingParams["commentstab_limitstart"]:"0";
    $rows = $this->_interface->getCommentsUser($user->id, null,$params->get('sortBy',"jgco.cmtid DESC"), $picsNumber, $limitstart);

    $return .= $this->listRows($rows, $numcols);

    // Pagination, if configured to show:
    $showPagination = ($params->get('pagination','1')=='1');
    if ($showPagination && ($picsNumber < $total))
    {
      /*$return .= "<div style='width:95%;text-align:center;'>"
      .$this->_writePaging($pagingParams,"commentstab_",$picsNumber, $total)
      ."</div>";*/
      $return .= $this->_writePaging($pagingParams,"gallerytab_",$picsNumber,$total,'Comments');
    }
    return $return;
  }
}

?>