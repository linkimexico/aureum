<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;




 
JsnApp::uses('Component', 'Controller');

class PopularBlogsComponent extends Component 
{
    public function run( &$controller, $settings )
    {
        $jsnsocial_setting = $controller->_getSettings();
        $controller->loadModel('Blog');
                   
        $popular = $controller->Blog->getPopularBlogs( $settings['num_blogs'], $jsnsocial_setting['popular_interval'] );
        $controller->set('popular', $popular);
    }
    
    public function install() {}
    
    public function upgrade() {}
    
    public function uninstall() {}
} 