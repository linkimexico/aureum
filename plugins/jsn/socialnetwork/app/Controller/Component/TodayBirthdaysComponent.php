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

class TodayBirthdaysComponent extends Component 
{
    public function run( &$controller, $settings )
    {
        $controller->loadModel('User');
        $birthday_users = $controller->User->getTodayBirthday();

        if ( $settings['friend_birthdays'] ) // show friends' birthdays only
        {
            $uid = $controller->Session->read('uid');
            
            $controller->loadModel('Friend');
            $friends = $controller->Friend->getFriends( $uid );
            
            foreach ( $birthday_users as $key => $val )
                if ( $val['User']['id'] != $uid && !in_array( $val['User']['id'], $friends ) )
                    unset( $birthday_users[$key] );
        }
        
        $controller->set('birthday_users', $birthday_users);        
    }
    
    public function install() {}
    
    public function upgrade() {}
    
    public function uninstall() {}
} 