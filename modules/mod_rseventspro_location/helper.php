<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class modRseventsProLocation {

	public static function getLocation() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$id		= JFactory::getApplication()->input->getInt('id',0);
		
		$query->clear()
			->select($db->qn('l.id'))->select($db->qn('l.name'))->select($db->qn('l.url'))
			->select($db->qn('l.address'))->select($db->qn('l.coordinates'))
			->from($db->qn('#__rseventspro_locations','l'))
			->join('left', $db->qn('#__rseventspro_events','e').' ON '.$db->qn('e.location').' = '.$db->qn('l.id'))
			->where($db->qn('e.id').' = '.$id)
			->where($db->qn('l.published').' = 1');
		
		$db->setQuery($query);
		return $db->loadObject();
	}
}