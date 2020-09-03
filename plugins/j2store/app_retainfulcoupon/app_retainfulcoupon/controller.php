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
defined('_JEXEC') or die ('Restricted access');
require_once(JPATH_ADMINISTRATOR . '/components/com_j2store/library/appcontroller.php');
require_once(JPATH_SITE . '/plugins/j2store/app_retainfulcoupon/app_retainfulcoupon/library/vendor/autoload.php');

class J2StoreControllerAppRetainfulCoupon extends J2StoreAppController
{
    var $_element = 'app_retainfulcoupon';

    function __construct()
    {
        parent::__construct();
        F0FModel::addIncludePath(JPATH_SITE . '/plugins/j2store/' . $this->_element . '/' . $this->_element . '/models');
        F0FModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_j2store/models');
        F0FTable::addIncludePath(JPATH_SITE . '/plugins/j2store/' . $this->_element . '/' . $this->_element . '/tables');
        F0FTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_j2store/tables');
        JFactory::getLanguage()->load('plg_j2store_' . $this->_element, JPATH_ADMINISTRATOR);
    }

    protected function onBeforeGenericTask($task)
    {
        $this->configProvider->get($this->component . '.views.' . F0FInflector::singularize($this->view) . '.acl.' . $task, '');
        return $this->allowedTasks($task);
    }

    function allowedTasks($task)
    {
        $allowed = array(
            'checkAppID'
        );
        $status = false;
        if (in_array($task, $allowed)) {
            $status = true;
        }
        return $status;
    }

    function checkAppID()
    {
        $app = JFactory::getApplication();
        $app_id = $app->input->get('app_id', '');
        $json = array();
        if (empty($app_id)) {
            $json['error'] = JText::_('J2STORE_NEXTORDER_APP_ID_REQUIRED');
        } else {
            $next_order_request = new \Noc\RetainfulCoupon();
            $response = $next_order_request->remoteGet('app/' . $app_id);
            if ($response) {
                $decoded_response = json_decode($response);
                if ($decoded_response->success) {
                    $json['success'] = JText::_('J2STORE_NEXTORDER_APP_ID_CONNECTED_SUCCESSFULLY');
                } else {
                    $json['error'] = JText::_('J2STORE_NEXTORDER_APP_ID_NOT_CONNECTED_SUCCESSFULLY');
                }
            }

        }
        echo json_encode($json);
        $app->close();
    }

    function save()
    {
        $app = JFactory::getApplication();
        $data = $app->input->getArray($_POST);
        $data['params'] = $app->input->post->get('params', array(), 'array');
        $app_id = $data['params']['retainful_app_id'];
        $request_status = false;
        if (!empty($app_id)) {
            $next_order_request = new \Noc\RetainfulCoupon();
            $response = $next_order_request->remoteGet('app/' . $app_id);
            $decoded_response = json_decode($response);
            if ($decoded_response->success) {
                $request_status = true;
            }
        }
        if ($request_status) {
            $data['params']['is_retainful_connected'] = 1;
        } else {
            $data['params']['is_retainful_connected'] = 0;
        }
        $save_params = new JRegistry ();
        $save_params->loadArray($data['params']);
        $json = $save_params->toString();

        $db = JFactory::getDbo();

        $query = $db->getQuery(true)->update($db->qn('#__extensions'))->set($db->qn('params') . ' = ' . $db->q($json))->where($db->qn('element') . ' = ' . $db->q($this->_element))->where($db->qn('folder') . ' = ' . $db->q('j2store'))->where($db->qn('type') . ' = ' . $db->q('plugin'));

        $db->setQuery($query);
        $db->execute();
        if ($data ['appTask'] == 'apply' && isset ($data ['app_id'])) {
            $url = 'index.php?option=com_j2store&view=apps&task=view&id=' . $data ['app_id'];
        } else {
            $url = 'index.php?option=com_j2store&view=apps';
        }
        $cache = JFactory::getCache();
        $cache->clean();
        $this->setRedirect($url);
    }
}

