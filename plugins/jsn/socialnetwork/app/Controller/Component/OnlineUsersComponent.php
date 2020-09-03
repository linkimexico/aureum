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

class OnlineUsersComponent extends Component 
{
    public function run( &$controller, $settings )
    {
        $controller->loadModel('User');               
        $online = $controller->User->getOnlineUsers( $settings['num_online_users'] );
        $controller->set('online', $online);
    }
    
    public function install() {}
    
    public function upgrade() {}
    
    public function uninstall() {}
} 