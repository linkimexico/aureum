<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Hook extends AppModel 
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
    
    public $order = 'Hook.weight asc';
    
    public function loadAll( $controller, $action, $role_id )
    {        
        $res = array();
        $cond = array( 'OR' => array( array( 'Hook.controller' => '' ),
                                      array( 'Hook.controller' => $controller, 'Hook.action' => $action )
                                    ),
                       'enabled' => 1
        );
        
        $hooks = $this->find('all', array( 'conditions' => $cond ) );
		
        foreach ( $hooks as $hook )
		{
			$permissions = explode(',', $hook['Hook']['permission']);
			
			if ( $hook['Hook']['permission'] === '' || in_array( strval($role_id), $permissions, true ) )
			{
				$positions=explode(',',$hook['Hook']['position']);
				
				foreach($positions as $position)
				{
					$res[trim($position)][] = $hook;
				}
				
			}
            	
				
					
		}
        
        return $res;
    }
}
