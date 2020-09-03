<?php

defined('_JEXEC') or die('Restricted access');

$db 	=JFactory::getDBO();
header('Content-type:text/javascript;charset=UTF-8');
$sql = "select * from `#__dc_mv_events` where calid=".JRequest::getVar( 'id' );
$db->setQuery( $sql );
$rows = $db->loadObjectList();


    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=events".date("Y-M-D_H.i.s").".ics");

    echo "BEGIN:VCALENDAR\n";
    echo "PRODID:-//Joomla Calendars//Booking Calendar Contact Form for WordPress//EN\n";
    echo "VERSION:2.0\n";
    

    for ($i=0;$i<count($rows);$i++)
    {
        $event = $rows[$i];
    
        echo "BEGIN:VEVENT\n";
        echo "UID:uid".md5($event->id)."@".$_SERVER["SERVER_NAME"]."\n";
        echo "DTSTAMP:".gmdate("Ymd")."T".gmdate("His")."Z\n";
        echo "DTSTART:".gmdate("Ymd",strtotime($event->starttime))."T".gmdate("His",strtotime($event->starttime))."Z\n";
        echo "DTEND:".gmdate("Ymd",strtotime($event->endtime))."T".gmdate("His",strtotime($event->endtime))."Z\n";
        //echo "CREATED:".gmdate("Ymd",strtotime($event->starttime))."T".gmdate("His",strtotime($event->starttime))."Z\n";
        echo "DESCRIPTION:".str_replace("<br>",'\n',str_replace("<br />",'\n',str_replace("\n",'\n',$event->description)))."\n";
        echo "LAST-MODIFIED:".gmdate("Ymd")."T".gmdate("His")."Z\n";
        echo "LOCATION:".str_replace("\n",'\n',$event->location)."\n";
        echo "SEQUENCE:0\n";
        echo "STATUS:CONFIRMED\n";
        echo "SUMMARY:".str_replace("\n",'\n',$event->title)."\n";
        echo "TRANSP:OPAQUE\n";
        echo "END:VEVENT\n";
    
    
    }
    echo 'END:VCALENDAR';
    exit;
?>