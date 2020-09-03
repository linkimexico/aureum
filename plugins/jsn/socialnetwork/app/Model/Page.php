<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Page extends AppModel 
{
    public $validate = array(   
                        'title' =>   array(   
                            'rule' => 'notEmpty',
                            'message' => 'Title is required'
                        ),
                        'alias' =>   array(   
                            'rule' => 'notEmpty',
                            'message' => 'Alias is required'
                        ),
                        'content' =>   array(   
                            'rule' => 'notEmpty',
                            'message' => 'Content is required'
                        )                                      
    );
    
    public $order = 'Page.weight asc';
    
    public function loadMenuPages( $role_id )
    {        
        $pages = Cache::read('pages_' . $role_id);          
                
        if (empty($pages))
        {
            $pages = $this->find('all', array( 'conditions' => array('menu' => 1) ) );
    
            foreach ( $pages as $key => $page )
            {
                $permissions = explode(',', $page['Page']['permission']);
                
                if ( $page['Page']['permission'] !== '' && !in_array( strval($role_id), $permissions, true ) )
                    unset($pages[$key]);
            }
            
            Cache::write('pages_' . $role_id, $pages, '_cache_group_');
        }
        
        return $pages;
    }
}
