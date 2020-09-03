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
 * @link        https://www.j2store.org
 * --------------------------------------------------------------------------------
 *
 * */
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_ADMINISTRATOR . '/components/com_j2store/library/plugins/app.php');
require_once(JPATH_SITE . '/plugins/j2store/app_retainfulcoupon/app_retainfulcoupon/library/vendor/autoload.php');

class plgJ2StoreApp_retainfulCoupon extends J2StoreAppPlugin
{
    /**
     * @var $_element
     */
    var $_element = 'app_retainfulcoupon';

    public function __construct($subject, $config)
    {
        parent::__construct($subject, $config);
        F0FTable::addIncludePath(JPATH_SITE . '/plugins/j2store/' . $this->_element . '/' . $this->_element . '/tables');
    }

    /**
     * Overriding
     * @param $row
     * @return string|null
     * @throws Exception
     */
    function onJ2StoreGetAppView($row)
    {
        if (!$this->_isMe($row)) {
            return null;
        }
        $html = $this->viewList();
        return $html;
    }

    /**
     * Validates the data submitted based on the suffix provided
     * A controller for this plugin, you could say
     * @return string
     * @throws Exception
     */
    function viewList()
    {
        $app = JFactory::getApplication();
        $id = $app->input->getInt('id', 0);
        JToolBarHelper::title(JText::_('J2STORE_APP') . '-' . JText::_('PLG_J2STORE_' . strtoupper($this->_element)), 'j2store-logo');
        JToolBarHelper::back('J2STORE_BACK_TO_DASHBOARD', 'index.php?option=com_j2store');
        JToolBarHelper::back('PLG_J2STORE_BACK_TO_APPS', 'index.php?option=com_j2store&view=apps');
        JToolBarHelper::apply('apply');
        JToolBarHelper::save();
        $vars = new JObject();
        //model should always be a plural
        $this->includeCustomModel('AppRetainfulCoupons');
        $model = F0FModel::getTmpInstance('AppRetainfulCoupons', 'J2StoreModel');
        $data = $this->params->toArray();
        $newdata = array();
        $newdata['params'] = $data;
        $form = $model->getForm($newdata);
        $vars->form = $form;
        $vars->id = $id;
        $vars->action = "index.php?option=com_j2store&view=app&task=view&id={$id}";
        $html = $this->_getLayout('default', $vars);
        return $html;
    }

    /**
     * Save the newly generated coupon to our DB for further process
     * @param $order
     * @param $new_status
     */
    public function onJ2storeAfterOrderstatusUpdate($order, $new_status)
    {
        $is_app_connected = $this->params->get('is_retainful_connected',0);
        if ($is_app_connected) {
            $app_id = $this->params->get('retainful_app_id', '');
            $used_coupon_code = "";
            $create_coupon_on = $this->params->get('coupon_payment_status', array('4', '1'));
            if (!empty($order->getOrderDiscounts())) {
                foreach ($order->getOrderDiscounts() as $discount) {
                    if ($discount->discount_type == 'next_order_coupon') {
                        $used_coupon_code = $discount->discount_code;
                        $next_order_coupon = F0FTable::getAnInstance('NextOrderCoupon', 'J2StoreTable')->getClone();
                        $is_found = $next_order_coupon->load(array('coupon' => $discount->discount_code));
                        if ($is_found) {
                            $next_order_coupon->is_used = 1;
                            $next_order_coupon->store();
                        }
                    }
                }
            }
            $order_info = F0FTable::getAnInstance('Orderinfo', 'J2StoreTable')->getClone();
            $order_info->load(array('order_id' => $order->order_id));
            $first_name = (isset($order_info->shipping_first_name)) ? $order_info->shipping_first_name : ($order_info->billing_first_name) ? $order_info->billing_first_name : '';
            $last_name = (isset($order_info->shipping_last_name)) ? $order_info->shipping_last_name : ($order_info->billing_last_name) ? $order_info->billing_last_name : '';
            $order_email = $order->user_email;
            if (in_array($new_status, $create_coupon_on)) {
                $new_coupon_code = "";
                $order_id = $order->order_id;
                $coupon_details = F0FTable::getAnInstance('NextOrderCoupon', 'J2StoreTable')->getClone();
                $is_coupon_found = $coupon_details->load(array('order_id' => $order_id));
                if (!$is_coupon_found) {
                    $new_coupon_code = strtoupper(uniqid());
                    $new_coupon_code = chunk_split($new_coupon_code, 5, '-');
                    $new_coupon_code = rtrim($new_coupon_code, '-');
                    $coupon_amount = $this->params->get('coupon_amount', 0);
                    $coupon_type = $this->params->get('coupon_type', 0);
                    $date = new JDate($order->created_on, 'UTC');
                    $created_on = $date->format('Y-m-d H:i:s', true);
                    $table_columns = array('coupon' => $new_coupon_code, 'is_used' => 0, 'order_id' => $order_id, 'created_to' => $order_email, 'coupon_amount' => $coupon_amount, 'coupon_type' => $coupon_type, 'created_on' => $created_on);
                    $next_order_coupon = F0FTable::getAnInstance('NextOrderCoupon', 'J2StoreTable')->getClone();
                    $next_order_coupon->load(array('coupon' => $new_coupon_code, 'order_id' => $order->order_id));
                    $next_order_coupon->bind($table_columns);
                    $next_order_coupon->store();
                }
                $fields = array(
                    'app_id' => $app_id,
                    'firstname' => $first_name,
                    'lastname' => $last_name,
                    'email' => $order_email,
                    'order_id' => $order_id,
                    'applied_coupon' => $used_coupon_code,
                    'new_coupon' => $new_coupon_code,
                    'order_date' => strtotime($order->created_on),
                    'total' => $order->order_total
                );
                $next_order_request = new \Noc\RetainfulCoupon();
                $next_order_request->remoteGet('track', $fields);
            }
        }
    }

    /**
     * Add coupon text to email
     * @param $mail
     * @param $order
     * @param $all_tags
     */
    function onJ2storeAfterProcessTags(&$mail, $order, $all_tags)
    {
        $is_app_connected = $this->params->get('is_retainful_connected',0);
        if ($is_app_connected) {
            $next_order_request = new \Noc\RetainfulCoupon();
            $order_id = $order->order_id;
            $result = F0FTable::getAnInstance('NextOrderCoupon', 'J2StoreTable')->getClone();
            $app_id = $this->params->get('retainful_app_id', '');
            $is_coupon_found = $result->load(array('order_id' => $order_id));
            if ($is_coupon_found) {
                $new_coupon_code = $result->coupon;
                if (!empty($new_coupon_code)) {
                    $coupon_amount = $this->params->get('coupon_amount', 0);
                    $coupon_type = $this->params->get('coupon_type', 0);
                    $coupon_message = $this->params->get('coupon_message', '');
                    if (empty($coupon_message)) {
                        $coupon_message = JText::_('J2STORE_COUPONNEXTORDER_COUPON_MESSAGE_DEFAULT');
                    }
                    if ($coupon_type == 1) {
                        $currency = J2Store::currency();
                        $price = $currency->format($coupon_amount, $order->currency_code, 1);
                    } else {
                        $price = $coupon_amount . '%';
                    }
                    $coupon_url = JURI::root() . 'index.php/cart?coupon=' . $new_coupon_code;
                    $tags = array(
                        '[coupon_code]' => $new_coupon_code,
                        '[coupon_amount]' => $price,
                        '[coupon_url]' => $coupon_url,
                    );
                    foreach ($tags as $key => $value) {
                        $coupon_message = str_replace($key, $value, $coupon_message);
                    }
                    $order_info = F0FTable::getAnInstance('Orderinfo', 'J2StoreTable')->getClone();
                    $order_info->load(array('order_id' => $order->order_id));
                    $first_name = (isset($order_info->shipping_first_name)) ? $order_info->shipping_first_name : ($order_info->billing_first_name) ? $order_info->billing_first_name : '';
                    $last_name = (isset($order_info->shipping_last_name)) ? $order_info->shipping_last_name : ($order_info->billing_last_name) ? $order_info->billing_last_name : '';
                    $fields = array(
                        'app_id' => $app_id,
                        'firstname' => $first_name,
                        'lastname' => $last_name,
                        'email' => $order->user_email,
                        'new_coupon' => $new_coupon_code,
                        'order_date' => strtotime($order->created_on),
                        'email_open' => 1
                    );
                    $url = $next_order_request->_domain . 'track/pixel.gif?' . http_build_query($fields);
                    $image = '<img src="' . $url . '" width="1" height="1" />';
                    $coupon_message .= $image;
                    $mail = str_replace('[NEXT_ORDER_COUPON]', $coupon_message, $mail);
                }
            }
        }
    }

    /**
     * Check for coupon is valid
     * @param $coupon_obj
     * @param $order
     * @param $coupon_status
     * @throws Exception
     */
    function onJ2StoreBeforeCouponIsValid($coupon_obj, $order, &$coupon_status)
    {
        $session = JFactory::getSession();
        $session->get('applied_next_order_coupon', '', 'j2store');
        if ($session->has('applied_next_order_coupon', 'j2store')) {
            $session->clear('applied_next_order_coupon', 'j2store');
        }
        if (isset($coupon_obj->code) && !empty($coupon_obj->code)) {
            $coupon_details = F0FTable::getAnInstance('NextOrderCoupon', 'J2StoreTable')->getClone();
            $coupon_details->load(array('coupon' => $coupon_obj->code, 'is_used' => 0));
            if ($coupon_details->coupon == $coupon_obj->code) {
                $user = JFactory::getUser();
                $session->set('temp_next_order_coupon', $coupon_obj->code, 'j2store');
                $apply_at = $this->params->get('apply_coupon_to', 0);
                if ($apply_at == 1 && isset($order->user_email) && $order->user_email == $coupon_details->created_to) {
                    $coupon_status = true;
                    $session->set('applied_next_order_coupon', $coupon_obj->code, 'j2store');
                } elseif ($apply_at == 2 && $user->id) {
                    $coupon_status = true;
                    $session->set('applied_next_order_coupon', $coupon_obj->code, 'j2store');
                } elseif ($apply_at == 0) {
                    $coupon_status = true;
                    $session->set('applied_next_order_coupon', $coupon_obj->code, 'j2store');
                }
                if ($apply_at == 1 && !$coupon_status) {
                    $coupon_status = true;
                    JFactory::getApplication()->enqueueMessage('J2STORE_COUPON_NOT_APPLICABLE');
                } elseif ($apply_at == 2 && !$coupon_status) {
                    $coupon_status = true;
                    JFactory::getApplication()->enqueueMessage('J2STORE_COUPON_APPLICABLE_ONLY_FOR_LOGGED_IN_CUSTOMERS');
                }
            }
        }
    }

    /**
     * save the discount details if order_type is coupon and it is in our virtual coupon details
     * @param $order
     */
    public function onJ2StoreCalculateDiscountTotals($order)
    {
        $session = JFactory::getSession();
        if ($session->has('temp_next_order_coupon', 'j2store')) {
            $temp_coupon = $session->get('temp_next_order_coupon', '', 'j2store');
            $coupon_details = F0FTable::getAnInstance('NextOrderCoupon', 'J2StoreTable')->getClone();
            $is_coupon_found = $coupon_details->load(array('coupon' => $temp_coupon));
            $is_valid_coupon = false;
            if ($is_coupon_found) {
                $apply_at = $this->params->get('apply_coupon_to', 0);
                if ($apply_at == 1 && isset($order->user_email) && $order->user_email == $coupon_details->created_to) {
                    $is_valid_coupon = true;
                    $session->set('applied_next_order_coupon', $temp_coupon, 'j2store');
                } elseif ($apply_at == 2 && isset($order->user_email) && !empty($order->user_email)) {
                    $is_valid_coupon = true;
                    $session->set('applied_next_order_coupon', $temp_coupon, 'j2store');
                } elseif ($apply_at == 0) {
                    $is_valid_coupon = true;
                }
            }
            if ($is_valid_coupon) {
                $session->set('applied_next_order_coupon', $temp_coupon, 'j2store');
                $discount_price = ($coupon_details->coupon_type == 1) ? $coupon_details->coupon_amount : $this->getDiscountPrice($order, $coupon_details->coupon_amount);
                if ($is_coupon_found) {
                    $discount = new stdClass();
                    $discount->discount_type = 'next_order_coupon';
                    $discount->discount_entity_id = '';
                    $discount->discount_title = JText::_("NEXT_ORDER_COUPON");
                    $discount->discount_code = $temp_coupon;
                    $discount->discount_value = $coupon_details->coupon_amount;
                    $discount->discount_value_type = ($coupon_details->coupon_type == 1) ? 'fixed_cart' : 'percentage_cart';
                    $discount->discount_amount = $discount_price;
                    $discount->discount_tax = 0;
                    $order->addOrderDiscounts($discount);
                }
            }
        }
    }

    /**
     * it calculate discount amount
     * @param float $price
     * @param object $item
     * @param unknown $add_totals
     * @param object $order
     */
    function onJ2storeGetDiscountedPrice(&$price, &$item, $add_totals, $order)
    {
        $session = JFactory::getSession();
        if ($session->has('applied_next_order_coupon', 'j2store')) {
            $coupon_code = $session->get('applied_next_order_coupon', 'next_order_coupon', 'j2store');
            $coupon_details = F0FTable::getAnInstance('NextOrderCoupon', 'J2StoreTable')->getClone();
            $is_coupon_found = $coupon_details->load(array('coupon' => $coupon_code));
            if ($is_coupon_found) {
                if ($coupon_details->coupon_type == 0) {
                    $discount_price = ($price / 100) * $coupon_details->coupon_amount;
                } else {
                    $discount_price = $this->getFlatDiscountPrice($price, $item, $order, $coupon_details->coupon_amount);
                }
                $discount_price = min($price, $discount_price);
                $price = max($price - $discount_price, 0);
            }
        }
    }

    /**
     * Calculate price for discount
     * @param $discounting_amount
     * @param $cartitem
     * @param $order
     * @param $coupon_amount
     * @return mixed
     */
    function getFlatDiscountPrice($discounting_amount, $cartitem, $order, $coupon_amount)
    {
        $sub_total = $order->subtotal;
        $product_helper = J2Store::product();
        $params = J2Store::config();
        if ($params->get('config_including_tax', 0)) {
            $actual_price = ($cartitem->orderitem_price + $cartitem->orderitem_option_price);
            $price_for_discount = $product_helper->get_price_including_tax(($actual_price * $cartitem->orderitem_quantity), $cartitem->orderitem_taxprofile_id);
            $discount_percent = ($price_for_discount) / $sub_total;
        } else {
            $actual_price = ($cartitem->orderitem_price + $cartitem->orderitem_option_price);
            $price_for_discount = $product_helper->get_price_excluding_tax(($actual_price * $cartitem->orderitem_quantity), $cartitem->orderitem_taxprofile_id);
            $discount_percent = ($price_for_discount) / $sub_total;
        }
        return min(($coupon_amount * $discount_percent) / $cartitem->orderitem_quantity, $discounting_amount);

    }

    /**
     * Add custom tag for email
     * @param $tags
     */
    function onJ2storeAfterAdditionalTags(&$tags)
    {
        $tags['[NEXT_ORDER_COUPON]'] = JText::_('J2STORE_NEXTCOUPON_EMAIL_TAG');
    }

    /**
     * Calculate discount price
     * @param $order
     * @param $discount
     * @return float|int
     */
    function getDiscountPrice($order, $discount)
    {
        return ($discount / 100) * $order->subtotal;
    }

    /**
     * After removing the order remove the session var
     */
    function onJ2storeAfterEmptyCart()
    {
        $session = JFactory::getSession();
        if ($session->has('applied_next_order_coupon', 'j2store')) {
            $session->clear('applied_next_order_coupon', 'j2store');
        }
        if ($session->has('temp_next_order_coupon', 'j2store')) {
            $session->clear('temp_next_order_coupon', 'j2store');
        }
    }
}
