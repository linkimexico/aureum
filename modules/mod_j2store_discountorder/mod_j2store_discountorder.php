<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
if (!defined('F0F_INCLUDED'))
{
    include_once JPATH_LIBRARIES . '/f0f/include.php';
}
require_once JPATH_ADMINISTRATOR."/components/com_j2store/helpers/j2store.php";
$moduleclass_sfx = $params->get('moduleclass_sfx','');
$enable_coupon = $params->get('enable_coupon',1);
$enable_voucher = $params->get('enable_voucher',1);
JFactory::getLanguage()->load('com_j2store', JPATH_ADMINISTRATOR);
require( JModuleHelper::getLayoutPath('mod_j2store_discountorder', $params->get('layout', 'default')));