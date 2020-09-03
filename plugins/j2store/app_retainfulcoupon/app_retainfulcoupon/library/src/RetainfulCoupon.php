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

namespace Noc;
class RetainfulCoupon
{
    var $_domain = "https://api.retainful.com/v1/";
    //var $_domain = "https://c13061yiw2.execute-api.us-east-2.amazonaws.com/production/v1/";
    var $_element = 'app_retainfulcoupon';

    public function __construct($properties = array())
    {
        if (isset($properties['domain']) && $properties['domain']) {
            $this->_domain = $properties['domain'];
        }
    }

    public function setDomain($domain)
    {
        $this->_domain = $domain;
    }

    public function getDomain()
    {
        return $this->_domain;
    }

    /**
     * Get the details form API
     * @param $url
     * @param $fields
     * @return bool|string
     */
    function remoteGet($url, $fields = array())
    {
        $curl = curl_init();
        $fields_string = "";
        if (!empty($fields)) {
            $fields_string = '?' . http_build_query($fields);
        }
        $app_url = $this->_domain . $url . $fields_string;
        curl_setopt($curl, CURLOPT_URL, $app_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Origin: ' . $this->siteURL()));
        curl_setopt($curl, CURLOPT_REFERER, $this->siteURL());
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $this->logResponse($response, $url, $fields);
        return $response;
    }

    /**
     * get site url
     * @return string
     */
    function siteURL()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['SERVER_NAME'] . '/';
        return $protocol . $domainName;
    }

    /**
     * Log the response
     * @param $response
     * @param $url
     * @param $fields
     */
    function logResponse($response, $url, $fields)
    {
        $file = JPATH_ROOT . "/cache/" . $this->_element . ".log";
        $date = \JFactory::getDate();
        $f = fopen($file, 'a');
        fwrite($f, "\n\n" . $date->format('Y-m-d H:i:s'));
        fwrite($f, "\n" . $url . ': \n' . $response);
        fwrite($f, "Data " . json_encode($fields));
        fclose($f);
    }
}
