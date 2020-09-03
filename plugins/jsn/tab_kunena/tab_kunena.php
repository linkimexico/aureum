<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class PlgJsnTab_Kunena extends JPlugin
{
	public function renderTabs($data, $config)
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_content');
		KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');
		KunenaFactory::loadLanguage('com_kunena.views', 'admin');
		KunenaFactory::loadLanguage('com_kunena.templates');
		KunenaFactory::loadLanguage('com_kunena');
		$plugin=array(JText::_($this->params->get('tabtitle','Forum Posts')));
		$content=$this->getTab($data, $config);
		if(empty($content)) return null;
		$content.='
		<style>
			.justLoaded{display:none;}
		</style>
		<script>
			function tabKunenaLoadMore(page)
			{
				
				jQuery("#tabKunenaLoadMore td").html("<img src=\"'.JURI::root().'media/plg_jsn_kunena/loading.gif\" style=\"height:50px;\"/>");
				jQuery.get("index.php?option=com_jsn&view=loadmorekunena&format=raw&uid='.$data->id.'&page="+page,function( data ){jQuery("#tabKunenaLoadMore").replaceWith(data);jQuery(".justLoaded").fadeIn(500).removeClass("justLoaded")});
				
			}
		</script>
		';
		$plugin[]=$content;
		return $plugin;
		
	}
	
	public function renderPlugin()
	{
		if(JRequest::getVar('view','')=='loadmorekunena')
		{
			$lang = JFactory::getLanguage();
			$lang->load('com_content');
			KunenaFactory::loadLanguage('com_kunena.views', 'admin');
			KunenaFactory::loadLanguage('com_kunena.libraries', 'admin');
			KunenaFactory::loadLanguage('com_kunena.templates');
			KunenaFactory::loadLanguage('com_kunena');
			$page=JRequest::getInt('page',0);
			$start=$page* (int) $this->params->get('articles_page',10);
			$rows=$this->getList(JRequest::getInt('uid'), null,$start);
			$output="";
			foreach($rows as $row)
			{
				$output.=$this->getRow($row);
			}
			if(count($rows) == $this->params->get('articles_page',10)) $output.=$this->getPagination();
			
			echo $output;
		}
		
	}
	
	private function getList($uid, $config, $start=0)
	{
		$com_path = JPATH_SITE.'/components/com_content/';
		require_once $com_path.'helpers/route.php';
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		//$query->select('a.*,c.name as cname')->from('#__kunena_topics as a,#__kunena_categories as c,#__kunena_messages as m,as t')->where('a.category_id=c.id')->where('a.id=m.thread')->where('m.userid=' . (int) $uid )->order($this->params->get('orderby','a.last_post_time DESC'))->setLimit( (int) $this->params->get('articles_page',10), $start)->group('a.id');
		
		$query->select('m.id as mid,m.parent as mparent,m.subject as msubject,m.time as mtime,t.message as tmessage, a.*,c.name as cname')
			->from('#__kunena_messages as m,#__kunena_messages_text as t,#__kunena_topics as a,#__kunena_categories as c')
			->where('m.id=t.mesid')
			->where('m.thread=a.id')
			->where('m.catid=c.id')
			->where('m.userid=' . (int) $uid )
			->order($this->params->get('orderby','m.time DESC'))
			->setLimit( (int) $this->params->get('articles_page',10), $start);
			//->group('a.id');
		
		
		/*if(is_array($this->params->get('catid',null)) && count($this->params->get('catid',null))) {
			$query->where('a.category_id IN ('.implode(',',$this->params->get('catid',null)).')');
		}*/
		$db->setQuery($query);
		$rows = $db->loadAssocList('mid');
		return $rows;
	}
	
	private function getTab($data, $config)
	{
		$rows=$this->getList($data->id, $config);
		if(count($rows)==0) {
			if($this->params->get('showEmptyTab',1))
				return JText::_('COM_JSN_NORESULT');
			else
				return null;
		}
		$output=$this->getHeader();
		foreach($rows as $row)
		{
			$output.=$this->getRow($row);
		}
		if(count($rows) == $this->params->get('articles_page',10)) $output.=$this->getPagination();
		$output.=$this->getFooter();
		return $output;
	}
	
	private function getHeader()
	{
		$return=array();
		$return[]='<table class="table table-bordered">';
		return implode('',$return);
		
	}
	
	private function getFooter()
	{
		$return=array();
		
		$return[]='</table>';
		return implode('',$return);
	}
	
	private function getPagination()
	{
		$page=JRequest::getInt('page',0)+1;
		$return=array();
		$return[]='<tr id="tabKunenaLoadMore" '.(JRequest::getVar('format','')=='raw' ? 'class="justLoaded"' : '').'><td style="text-align:center"><a class="btn btn-default" href="#" onclick="tabKunenaLoadMore('.$page.');return false;">'.JText::_('COM_CONTENT_MORE_ARTICLES').'</a></td></tr>';
		return implode('',$return);
	}
	
	private function getRow($row)
	{
		$Itemid=KunenaRoute::getItemID('index.php?option=com_kunena&view=topic&catid='.$row['category_id'].'&id='.$row['id']);
		$link = JRoute::_('index.php?option=com_kunena&view=topic&catid='.$row['category_id'].'&id='.$row['id'].'&Itemid='.$Itemid);
		
		$return=array();
		$return[]='	<tr '.(JRequest::getVar('format','')=='raw' ? 'class="justLoaded"' : '').'><td><div class="jsnitem">';
		
		if($this->params->get('showHits',1)) $return[]='<span class="badge badge-default pull-right">'.JText::sprintf('COM_CONTENT_ARTICLE_HITS', $row['hits']).'</span>';
		$return[]='<div class="jsnitem-title">';
		$return[]='<h5 style="display:inline-block;"><a href="'.$link.'">'.$row['subject'].'</a></h5>';
		if($row['locked']) $return[]=' <i class="icon icon-lock"></i>';
		if($row['attachments']) $return[]=' <i class="icon icon-paperclip icon-clip"></i>';
		if($this->params->get('showCategory',1)) $return[]=' <span class="label label-success">'.$row['cname'].'</span>';
		$return[]='</div>';
		
		
		if($this->params->get('showIntro',1)) {
			if($row['mparent']==0) $return[]='     <p>'.self::cleanText($row['tmessage'],$this->params->get('introtext_limit',300)).'</p>';
			else $return[]='     <blockquote><h6><a href="'.$link.'#'.$row['mid'].'">'.$row['msubject'].'</a></h6>'.self::cleanText($row['tmessage'],$this->params->get('introtext_limit',300)).'</blockquote>';
		}
		
		//if($this->params->get('showCreatedDate',1)) $return[]='<small class="pull-right text-muted muted" style="clear:both"><a href="'.JsnHelper::getUser($row['first_post_userid'])->getLink().'">'.JsnHelper::getUser($row['first_post_userid'])->name.'</a>  '.KunenaDate::getInstance($row['first_post_time'])->toKunena('config_post_dateformat').'</small>';
		if($this->params->get('showCreatedDate',1)) $return[]='<small class="pull-right text-muted muted" style="clear:both">'.JText::_('COM_KUNENA_POSTED_AT').' '.KunenaDate::getInstance($row['mtime'])->toKunena('config_post_dateformat').'</small>';
		
		// if($this->params->get('showLastReply',1) && $row['posts']>1) $return[]='<h6 style="clear:both">'.JText::_('COM_KUNENA_LAST_MESSAGE').'</h6><blockquote>'.self::cleanText($row['last_post_message'],$this->params->get('introtext_limit',300)).'<small><a href="'.JsnHelper::getUser($row['last_post_userid'])->getLink().'">'.JsnHelper::getUser($row['last_post_userid'])->name.'</a>  '.KunenaDate::getInstance($row['last_post_time'])->toKunena('config_post_dateformat').'</small></blockquote>';
		
		
		$return[]='	</div></td></tr>';
		
		return implode('',$return);
	}
	
	private static function cleanText($introtext,$introtext_limit)
	{
		$introtext = self::_cleanIntrotext($introtext);
		$introtext = self::truncate($introtext, (int) $introtext_limit );
		return $introtext;
	}
	
	private static function _cleanIntrotext($introtext)
	{
		$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
		$replace = '';
		return preg_replace($pattern, $replace, $introtext);
	}

	private static function truncate($html, $maxLength = 0)
	{
		$baseLength = strlen($html);

		// First get the plain text string. This is the rendered text we want to end up with.
		$ptString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = false);

		for ($maxLength; $maxLength < $baseLength;)
		{
			// Now get the string if we allow html.
			$htmlString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = true);

			// Now get the plain text from the html string.
			$htmlStringToPtString = JHtml::_('string.truncate', $htmlString, $maxLength, $noSplit = true, $allowHtml = false);

			// If the new plain text string matches the original plain text string we are done.
			if ($ptString == $htmlStringToPtString)
			{
				return $htmlString;
			}

			// Get the number of html tag characters in the first $maxlength characters
			$diffLength = strlen($ptString) - strlen($htmlStringToPtString);

			// Set new $maxlength that adjusts for the html tags
			$maxLength += $diffLength;

			if ($baseLength <= $maxLength || $diffLength <= 0)
			{
				return $htmlString;
			}
		}

		return $html;
	}
}

?>