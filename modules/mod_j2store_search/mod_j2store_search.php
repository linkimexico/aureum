<?php
/*------------------------------------------------------------------------
# mod_j2store_search - J2Store Search
# ------------------------------------------------------------------------
# author    Gokila Priya - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2014 - 19 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// Include the helper functions only once
require_once __DIR__ . '/helper.php';
$app = JFactory::getApplication();
$search = $app->input->getString('search');
$categoryList = $params->get('categorylist',array());
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$param_menu_id = $params->get('display_menu_id',0);
$active_menu = $app->getMenu()->getActive();
$mitemid = (int) $params->get('menuitem_id', 0);
require(JModuleHelper::getLayoutPath('mod_j2store_search' ,$params->get('layout','default')));

