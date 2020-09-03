<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php')) {
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
	require_once JPATH_SITE.'/modules/mod_rseventspro_attendees/helper.php';

	JHtml::stylesheet('mod_rseventspro_attendees/style.css', array('relative' => true, 'version' => 'auto'));

	$guests = modRseventsProAttendees::getGuests();
	$suffix	= $params->get('moduleclass_sfx');
	$input	= JFactory::getApplication()->input;
	$option = $input->get('option');
	$layout = $input->get('layout');

	if ($option == 'com_rseventspro' && $layout == 'show' && !empty($guests)) {
		require(JModuleHelper::getLayoutPath('mod_rseventspro_attendees'));
	}
}