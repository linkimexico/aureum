<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Plugin extends AppModel 
{
    public $validate = array(   
                        'name' =>   array(   
                            'rule' => 'notEmpty',
                            'message' => 'Name is required'
                        ),
                        'key' =>   array(   
                            'rule' => 'notEmpty',
                            'message' => 'Key is required'
                        )                                      
    );
    
    public $order = 'Plugin.weight asc';
    
    public function loadAll( $role_id )
    {        
        $plugins = Cache::read('plugins_' . $role_id);        
		        
        if (empty($plugins))
        {
            $plugins = $this->find('all', array( 'conditions' => array('enabled' => 1, 'menu' => 1) ) );
    
            foreach ( $plugins as $key => $plugin )
            {
                $permissions = explode(',', $plugin['Plugin']['permission']);
                
                if ( $plugin['Plugin']['permission'] !== '' && !in_array( strval($role_id), $permissions, true ) )
                    unset($plugins[$key]);
            }
            
            Cache::write('plugins_' . $role_id, $plugins, '_cache_group_');
        }
        
        return $plugins;
    }
}
