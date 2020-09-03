<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modRseventsProTags {

	public static function getTags($params) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$order		= $params->get('order', 'RANDOM');
		
		$query->select($db->qn('id'))
			->select($db->qn('name'))
			->from($db->qn('#__rseventspro_tags'))
			->where($db->qn('published').' = '.$db->q(1));
		
		if ($order == 'RANDOM') {
			$query->order('RAND()');
		} else {
			$query->order($db->qn('name').' '.$db->escape($order));
		}
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}