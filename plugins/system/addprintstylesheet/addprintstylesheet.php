<?php
/**
 * @package	Add Print Stylesheet Plug-in for Joomla 3.x
 * @version	v1.3 2017-11-02
 * @author    	Neil Robertson
 * @copyright 	(c) 2017 http://webilicious.com.au
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import library dependencies
jimport('joomla.event.plugin');

// Add Print Stylesheet Plug-in
class plgSystemAddPrintStylesheet extends JPlugin
{
	// Constructor
	function __construct(&$subject, $params) {parent::__construct($subject, $params);}
	function onAfterDispatch() {
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		if ($app->isAdmin()) return;
		if ($document->getType() != 'html') return;
		// Get parameter
		$css = $this->params->get('css','');
		// Add stylesheet if parameter has been entered
		if($css) $document->addStyleSheet(JURI::base() . 'templates/'.$app->getTemplate().'/css/'.$css, "text/css", "print");	
	}
}
?>
