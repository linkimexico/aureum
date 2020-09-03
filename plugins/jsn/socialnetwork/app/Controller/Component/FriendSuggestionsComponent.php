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

class FriendSuggestionsComponent extends Component 
{
    public function run( &$controller, $settings )
    {
        $uid = $controller->Session->read('uid');        
        
        if ( !empty( $uid ) )
        { 
            $controller->loadModel('Friend');
            $friend_suggestions = $controller->Friend->getFriendSuggestions( $uid, false, $settings['num_friend_suggestions'] );
            $controller->set('friend_suggestions', $friend_suggestions);
        }
    }
    
    public function install() {}
    
    public function upgrade() {}
    
    public function uninstall() {}
} 