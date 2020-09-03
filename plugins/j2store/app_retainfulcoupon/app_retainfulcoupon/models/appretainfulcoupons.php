<?php
/**
 * --------------------------------------------------------------------------------
 * APP - Retainful next order coupon
 * --------------------------------------------------------------------------------
 * @package     Joomla  3.x
 * @subpackage  J2 Store
 * @author      Sathyaseelan, J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2019 J2Store . All rights reserved.
 * @license     GNU/GPL license: v3 or later
 * @link        http://j2store.org
 * --------------------------------------------------------------------------------
 *
 * */
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_ADMINISTRATOR . '/components/com_j2store/library/appmodel.php');
class J2StoreModelAppRetainfulCoupons extends J2StoreAppModel
{
	
	public $_element = 'app_retainfulcoupon';
    public function getPlugin(){
        $db = JFactory::getDBo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__extensions')->where('type='.$db->q('plugin'))->where('element='.$db->q($this->_element))->where('folder='.$db->q('j2store'));
        $db->setQuery($query);
        return $db->loadObject();
    }

    public function getPluginParams(){
        $plugin_data = JPluginHelper::getPlugin('j2store', $this->_element);
        $params = new JRegistry;
        $params->loadString($plugin_data->params);
        return $params;
    }
}