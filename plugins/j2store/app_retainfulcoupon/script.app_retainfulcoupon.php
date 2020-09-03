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
jimport('joomla.filesystem.file');

class plgJ2Storeapp_retainfulcouponInstallerScript
{

    /**
     * Check and create dependencies
     * @param $type
     * @param $parent
     * @return bool
     */
    function preflight($type, $parent)
    {
        $db = JFactory::getDbo();
        $prefix = $db->getPrefix();
        $tables = $db->getTableList();
        if (!in_array($prefix . 'j2store_retainfulcoupons', $tables)) {
            $query = "CREATE TABLE `#__j2store_retainfulcoupons` (
                      `j2store_retainfulcoupon_id` int(11) NOT NULL AUTO_INCREMENT,
                      `coupon` varchar(255) NOT NULL,
                      `is_used` enum('0','1') NOT NULL DEFAULT '0',
                      `order_id` varchar(255) NOT NULL,
                      `created_to` varchar(500) NOT NULL,
                      `created_on` datetime DEFAULT NULL,
                      `coupon_amount` varchar(255) NOT NULL,
                      `coupon_type` enum('0','1') NOT NULL DEFAULT '0',
					  PRIMARY KEY (`j2store_retainfulcoupon_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
            $this->_executeQuery($query);
        }
    }

    /**
     * Execute the query
     * @param $query
     */
    private function _executeQuery($query)
    {

        $db = JFactory::getDbo();
        $db->setQuery($query);
        try {
            $db->execute();
        } catch (Exception $e) {

            //do nothing. we dont want to fail the install process.
        }
    }

}
