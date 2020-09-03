<?php
/*------------------------------------------------------------------------
# mod_j2store_categories
# ------------------------------------------------------------------------
# author    Gokila priya - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_SITE . '/modules/mod_j2store_categories/helpers/j2storecategories.php';
/**
 * Content Component Category Tree
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       1.6
 */
class Categories extends J2StoreCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__content';
		$options['extension'] = 'com_content';
		parent::__construct($options);
	}
}
