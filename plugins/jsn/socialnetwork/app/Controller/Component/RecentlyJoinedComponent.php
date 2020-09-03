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

class RecentlyJoinedComponent extends Component 
{
    public function run( &$controller, $settings )
    {      
        $controller->loadModel( 'User' );
        $users = $controller->User->getLatestUsers( $settings['num_new_members'] );
        $controller->set('new_users', $users);
    }
    
    public function install() {}
    
    public function upgrade() {}
    
    public function uninstall() {}
} 