<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

$tmp_host=explode(':',JFactory::getApplication()->getCfg('host'));
switch(count($tmp_host)){
	case 2:
		$host=$tmp_host[0];
		if(is_numeric($tmp_host[1]))
		{
			$port=$tmp_host[1];
			$socket='';
		}
		else
		{
			$port='';
			$socket=$tmp_host[1];
		}
		
	break;
	default:
		$host=$tmp_host[0];
		$port='';
		$socket='';
	break;
}

$CONFIG = array( "host"     => $host,
                 "login"    => JFactory::getApplication()->getCfg('user'),
                 "password" => JFactory::getApplication()->getCfg('password'),
                 "database" => JFactory::getApplication()->getCfg('db'),
                 "port"     => $port,
                 "unix_socket"	=> $socket,
                 "prefix"   => JFactory::getApplication()->getCfg('dbprefix').'jsnsocial_',
                 "salt"     => '54a786242cc005ff6a52a05c07538c3f',
                 "cipher"   => '-3769236825052201984'
);