<?php
/**
 * @version		$Id: multicalendar.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Editor MultiCalendar buton
 *
 * @package Editors-xtd
 * @since 1.5
 */
class plgButtonMultiCalendar extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
    
	/**
	 * Display the button
	 *
	 * @return array A two element array of ( imageName, textToInsert )
	 */
	public function onDisplay($name)
	{
		$app = JFactory::getApplication();

		$doc = JFactory::getDocument();
		$template = $app->getTemplate();

		$link = 'index.php?option=com_multicalendar&task=insert&view=insert&amp;e_name='.$name;
		
		JHTML::_('behavior.modal');

		$button = new JObject();
		$button->class = 'btn';
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('MultiCalendar'));
		$button->set('name', 'blank');
		$button->set('options', "{handler: 'iframe', size: {x: 450, y: 480}}");

		return $button;
	}
}