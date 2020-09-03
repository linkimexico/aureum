<?php
/**
 * @version		$Id: example.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
require_once( JPATH_SITE.'/components/com_multicalendar/DC_MultiViewCal/php/list.inc.php' );

/**
 * Multi Calendar Content Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.5
 */
function MultiCalendar_replacer( &$matches ) {
    global $arrayJS_list,$zscripts;
    $mainframe  =& JFactory::getApplication();
    $id = (int)$matches[1];
    $container = "plg".mt_rand();
    $language = $mainframe->getCfg('language');
    $style = $matches[5];
    $views = "";
    $buttons = "";
    $edition = $matches[6];
    $sample = "";
    $otherparamsvalue = $matches[15];
    $palette = intval($matches[14]);
    $viewdefault = $matches[3];
    $numberOfMonths = $matches[8];
    $start_weekday = $matches[4];
    $msg = print_scripts($id,$container,$language,$style,$views,$buttons,$edition,$sample,$otherparamsvalue,$palette,$viewdefault,$numberOfMonths,$start_weekday,true,$matches);
    if ($msg=="")
        return print_html($container);
    else
        return $msg;
}
class plgContentMulticalendar extends JPlugin
{

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Example prepare content method
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		// define the regular expression for the bot
		$regex = "#\{multicalendar\:(.*?)\:(\d{5})\:(.*?)\:(\d{1})\:(.*?)\:(\d{1})\:(\d{3})\:(\d{1,2})\:(\d{1})\:(.*?)\:(\d{1})\:(.*?)\:(.*?):(.*?):(.*?)\}#s";


		$article->text = preg_replace_callback( $regex, 'MultiCalendar_replacer', $article->text );
		return true;
	}
}