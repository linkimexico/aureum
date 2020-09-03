<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2016 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (file_exists(JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php')) {
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/rseventspro.php';
	require_once JPATH_SITE.'/modules/mod_rseventspro_tags/helper.php';

	// Get tags
	$tags = modRseventsProTags::getTags($params);

	// Load language
	JFactory::getLanguage()->load('com_rseventspro');

	// Get the Itemid
	$itemid = $params->get('itemid');
	$itemid = !empty($itemid) ? $itemid : '';

	JHtml::stylesheet('mod_rseventspro_tags/style.css', array('relative' => true, 'version' => 'auto'));
	
	$suffix	= $params->get('moduleclass_sfx');
	$links	= $params->get('links',0);
	
	require JModuleHelper::getLayoutPath('mod_rseventspro_tags', $params->get('layout', 'default'));
}