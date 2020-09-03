<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Cache Engine Configuration
 * Default settings provided below
 *
 * File storage engine.
 *
 *   Cache::config('default', array(
 *      'engine' => 'File', //[required]
 *      'duration'=> 3600, //[optional]
 *      'probability'=> 100, //[optional]
 *      'path' => CACHE, //[optional] use system tmp directory - remember to use absolute path
 *      'prefix' => 'cake_', //[optional]  prefix every cache file with this string
 *      'lock' => false, //[optional]  use file locking
 *      'serialize' => true, // [optional]
 *      'mask' => 0666, // [optional] permission mask to use when creating cache files
 *  ));
 *
 * APC (http://pecl.php.net/package/APC)
 *
 *   Cache::config('default', array(
 *      'engine' => 'Apc', //[required]
 *      'duration'=> 3600, //[optional]
 *      'probability'=> 100, //[optional]
 *      'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *  ));
 *
 * Xcache (http://xcache.lighttpd.net/)
 *
 *   Cache::config('default', array(
 *      'engine' => 'Xcache', //[required]
 *      'duration'=> 3600, //[optional]
 *      'probability'=> 100, //[optional]
 *      'prefix' => Inflector::slug(APP_DIR) . '_', //[optional] prefix every cache file with this string
 *      'user' => 'user', //user from xcache.admin.user settings
 *      'password' => 'password', //plaintext password (xcache.admin.pass)
 *  ));
 *
 * Memcache (http://memcached.org/)
 *
 *   Cache::config('default', array(
 *      'engine' => 'Memcache', //[required]
 *      'duration'=> 3600, //[optional]
 *      'probability'=> 100, //[optional]
 *      'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *      'servers' => array(
 *          '127.0.0.1:11211' // localhost, default port 11211
 *      ), //[optional]
 *      'persistent' => true, // [optional] set this to false for non-persistent connections
 *      'compress' => false, // [optional] compress data in Memcache (slower, but uses less memory)
 *  ));
 *
 *  Wincache (http://php.net/wincache)
 *
 *   Cache::config('default', array(
 *      'engine' => 'Wincache', //[required]
 *      'duration'=> 3600, //[optional]
 *      'probability'=> 100, //[optional]
 *      'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *  ));
 *
 * Redis (http://http://redis.io/)
 *
 *   Cache::config('default', array(
 *      'engine' => 'Redis', //[required]
 *      'duration'=> 3600, //[optional]
 *      'probability'=> 100, //[optional]
 *      'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *      'server' => '127.0.0.1' // localhost
 *      'port' => 6379 // default port 6379
 *      'timeout' => 0 // timeout in seconds, 0 = unlimited
 *      'persistent' => true, // [optional] set this to false for non-persistent connections
 *  ));
 */
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * JsnApp::build(array(
 *     'Model'                     => array('/path/to/models', '/next/path/to/models'),
 *     'Model/Behavior'            => array('/path/to/behaviors', '/next/path/to/behaviors'),
 *     'Model/Datasource'          => array('/path/to/datasources', '/next/path/to/datasources'),
 *     'Model/Datasource/Database' => array('/path/to/databases', '/next/path/to/database'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions', '/next/path/to/sessions'),
 *     'Controller'                => array('/path/to/controllers', '/next/path/to/controllers'),
 *     'Controller/Component'      => array('/path/to/components', '/next/path/to/components'),
 *     'Controller/Component/Auth' => array('/path/to/auths', '/next/path/to/auths'),
 *     'Controller/Component/Acl'  => array('/path/to/acls', '/next/path/to/acls'),
 *     'View'                      => array('/path/to/views', '/next/path/to/views'),
 *     'View/Helper'               => array('/path/to/helpers', '/next/path/to/helpers'),
 *     'Console'                   => array('/path/to/consoles', '/next/path/to/consoles'),
 *     'Console/Command'           => array('/path/to/commands', '/next/path/to/commands'),
 *     'Console/Command/Task'      => array('/path/to/tasks', '/next/path/to/tasks'),
 *     'Lib'                       => array('/path/to/libs', '/next/path/to/libs'),
 *     'Locale'                    => array('/path/to/locales', '/next/path/to/locales'),
 *     'Vendor'                    => array('/path/to/vendors', '/next/path/to/vendors'),
 *     'Plugin'                    => array('/path/to/plugins', '/next/path/to/plugins'),
 * ));
 *
 */

/**
 * Custom Inflector rules, can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */

CakePlugin::loadAll();

/**
 * You can attach event listeners to the request lifecyle as Dispatcher Filter . By Default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *      'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *      'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 *      array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *      array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
    'AssetDispatcher',
    'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
JsnApp::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
    'engine' => 'FileLog',
    'types' => array(/*'notice', 'info', */'debug'),
    'file' => 'debug',
));
CakeLog::config('error', array(
    'engine' => 'FileLog',
    'types' => array(/*'warning', */'error', 'critical', 'alert', 'emergency'),
    'file' => 'error',
));


/* General */

define('ROOT_ADMIN_ID', 1);
define('RESULTS_LIMIT', 24);

define('PHOTO_WIDTH', 1000);
define('PHOTO_HEIGHT', 800);
define('PHOTO_THUMB_WIDTH', 200);
define('PHOTO_THUMB_HEIGHT', 150);
define('GROUP_AVATAR_WIDTH', 200);
define('GROUP_AVATAR_HEIGHT', 300);
define('AVATAR_WIDTH', 170);
define('AVATAR_HEIGHT', 170);
define('AVATAR_THUMB_WIDTH', 45);
define('AVATAR_THUMB_HEIGHT', 45);
define('COVER_WIDTH', 1000);
define('COVER_HEIGHT', 286);
define('IMAGE_WIDTH', 482);
define('IMAGE_HEIGHT', 320);
define('PHOTO_QUALITY', 100);
 
define('APP_USER', 'user');
define('APP_FRIEND', 'friend');
define('APP_BLOG', 'blog');
define('APP_ALBUM', 'album');
define('APP_PHOTO', 'photo');
define('APP_TOPIC', 'topic');
define('APP_VIDEO', 'video');
define('APP_EVENT', 'event');
define('APP_GROUP', 'group');
define('APP_CONVERSATION', 'conversation');
define('APP_PAGE', 'page');

define('PLUGIN_USER_ID', 1);
define('PLUGIN_BLOG_ID', 2);
define('PLUGIN_ALBUM_ID', 3);
define('PLUGIN_VIDEO_ID', 4);
define('PLUGIN_TOPIC_ID', 5);
define('PLUGIN_GROUP_ID', 6);
define('PLUGIN_EVENT_ID', 7);
define('PLUGIN_CONVERSATION_ID', 8);
define('PLUGIN_PAGE_ID', 9);

define('ROLE_ADMIN', 1);
define('ROLE_MEMBER', 2);
define('ROLE_GUEST', 3);

define('PRIVACY_EVERYONE', 1);
define('PRIVACY_FRIENDS', 2);
define('PRIVACY_ME', 3);

define('PRIVACY_PUBLIC', 1);
define('PRIVACY_PRIVATE', 2);
define('PRIVACY_RESTRICTED', 3);

/* Event */
define('RSVP_AWAITING', 0);
define('RSVP_ATTENDING', 1);
define('RSVP_NOT_ATTENDING', 2);
define('RSVP_MAYBE', 3);

/* Group */

define('GROUP_USER_INVITED', 0);
define('GROUP_USER_MEMBER', 1);
define('GROUP_USER_REQUESTED', 2);
define('GROUP_USER_ADMIN', 3);

function summary( $str, $chars = 100 )
{
	return substr( strip_tags($str), 0, $chars );
}

function possession( $actor, $owner = null )
{	
	if ( Configure::read('Config.language') != 'eng' )
        return h($owner['name']);
        
    if ( empty( $owner ) || $actor['id'] == $owner['id'] )
        return ( $actor['gender'] == 'Male' ) ? __('his') : __('her');
	
	return h($owner['name']) . '\'s';
}

function cleanJsString( $str )
{
	return addslashes(str_replace( array('"', "\n") , array('', ''), $str));
}

function seoUrl( $string, $limit = 70 ) 
{    
    $string = Inflector::slug( strtolower($string), '-' );
    
    if ( strlen($string) > $limit ) {
        $string = substr($string, 0, $limit);
    }
    
    return $string;
}

function addDir( $path, &$zip, $dest )
{			
    $zip->addEmptyDir( $dest );
    $nodes = glob( $path . DS . '*' );
	
    foreach ($nodes as $node) 
    {
        if ( is_dir( $node ) ) {
            addDir( $node, $zip, $dest . DS . basename( $node ) );
        } else if ( is_file( $node ) )  {
            $zip->addFile( $node, $dest . DS . basename( $node ) );
        }
    }	 
}