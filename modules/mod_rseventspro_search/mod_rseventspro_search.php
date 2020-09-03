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
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/route.php';
	require_once JPATH_SITE.'/components/com_rseventspro/helpers/html.php';
	require_once JPATH_SITE.'/modules/mod_rseventspro_search/helper.php';

	// Load tooltips
	rseventsproHelper::tooltipLoad();
	// Load jQuery
	rseventsproHelper::loadjQuery();
	
	$suffix		= $params->get('moduleclass_sfx');
	$layout		= $params->get('layout','ajax');
	$links		= (int) $params->get('links',0);
	$categories	= (int) $params->get('categories',0);
	$locations	= (int) $params->get('locations',0);
	$start		= (int) $params->get('start',0);
	$end		= (int) $params->get('end',0);
	$archive	= (int) $params->get('archive',0);
	$price		= (int) $params->get('price',0);
	$priceFilter= false;
	$maxPrice	= 0;

	$locationslist	= JHTML::_('select.genericlist', modRseventsProSearch::getLocations(), 'rslocations[]', 'size="5" multiple="multiple" class="mod-rsepro-chosen"', 'value', 'text' ,0);
	$archivelist	= JHTML::_('select.genericlist', modRseventsProSearch::getYesNo(), 'rsarchive', 'class="input-small"', 'value', 'text' ,0);
	$categorieslist = JHTML::_('select.genericlist', modRseventsProSearch::getCategories(), 'rscategories[]', 'size="5" multiple="multiple" class="mod-rsepro-chosen"', 'value', 'text' ,0);

	// Load language
	JFactory::getLanguage()->load('com_rseventspro');

	// Add stylesheets
	JHtml::stylesheet('mod_rseventspro_search/style.css', array('relative' => true, 'version' => 'auto'));
	JHtml::script('mod_rseventspro_search/scripts.js', array('relative' => true, 'version' => 'auto'));
	
	if (file_exists(JPATH_SITE.'/media/com_rseventspro/js/bootstrap-slider.js') && file_exists(JPATH_SITE.'/media/com_rseventspro/css/bootstrap-slider.css')) {
		if ($price) {		
			JHtml::stylesheet('com_rseventspro/bootstrap-slider.css', array('relative' => true, 'version' => 'auto'));
			JHtml::script('com_rseventspro/bootstrap-slider.js', array('relative' => true, 'version' => 'auto'));
			$maxPrice = (int) modRseventsProSearch::maxPrice();
			$priceFilter = true;
		}
	}
	
	// Get the Itemid
	$itemid = $params->get('itemid');
	$itemid = !empty($itemid) ? $itemid : RseventsproHelperRoute::getEventsItemid();

	require JModuleHelper::getLayoutPath('mod_rseventspro_search',$layout);
}