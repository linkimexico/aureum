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

class FeaturedMembersComponent extends Component 
{
    public function run( &$controller, $settings )
    {
        $controller->loadModel( 'User' );
        $featured_users = $controller->User->getFeaturedUsers();
        $controller->set('featured_users', $featured_users);
    }
    
    public function install() {}
    
    public function upgrade() {}
    
    public function uninstall() {}
} 