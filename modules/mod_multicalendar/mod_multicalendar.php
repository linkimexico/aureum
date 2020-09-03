<?php
/**
* @Copyright Copyright (C) 2010 CodePeople, www.codepeople.net
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*
* This file is part of Multi Calendar for Joomla <www.joomlacalendars.com>.
*
* Multi Calendar for Joomla is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Multi Calendar for Joomla  is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Multi Calendar for Joomla.  If not, see <http://www.gnu.org/licenses/>.
*
**/


//don't allow other scripts to grab and execute our file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/list.inc.php' );
$mainframe = JFactory::getApplication();
$id = $params->get('the_calendar_id');
$container = "mdcmv".$id;
$language = $mainframe->getCfg('language');
$style = $params->get('cssStyle');
$views = $params->get('views');
$buttons = $params->get('buttons');
$edition = $params->get('edition');
$sample = $params->get('sample');
$otherparamsvalue = $params->get('otherparams');
$palette = $params->get('palette');
$viewdefault = $params->get('viewdefault');
$numberOfMonths = $params->get('numberOfMonths');
$start_weekday = $params->get("start_weekday");
$matches = array();
$msg = print_scripts($id,$container,$language,$style,$views,$buttons,$edition,$sample,$otherparamsvalue,$palette,$viewdefault,$numberOfMonths,$start_weekday,false,$matches);
?>
<?php echo $msg;?>
<div class="moduletable<?php echo $params->get( 'moduleclass_sfx', '' ) ?>">
<?php echo print_html($container);?>
</div>