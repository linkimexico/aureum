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
defined('_JEXEC') or die;

class J2StoreTableNextOrderCoupon extends F0FTable
{
    public function __construct($table, $key, $db, $config = array())
    {
        $table = '#__j2store_retainfulcoupons';
        $key = 'j2store_retainfulcoupon_id';
        parent::__construct($table, $key, $db);
    }
}	
