<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Category extends AppModel {

	public $order = 'Category.weight asc';
						
	public $validate = array(	
							'name' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Name is required'
							),
							'type' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Type is required'
							)
	);
	
	/*
	 * Get all categories based on $type
	 * @param string $type
	 * @return array $categories
	 */
					
	public function getCategories( $type ) 
	{
		$categories = $this->find( 'threaded', array( 'conditions' => array( 'Category.type' => $type, 'Category.active' => 1 ) ) );
		
		return $categories;
	}

	/*
	 * Get all categories for drop down list
	 * @param string $type
	 * @return array $categories
	 */
	
	public function getCategoriesList( $type, $role_id = null )
	{		
	    $categories = $this->find( 'threaded', array( 'conditions' => array( 'Category.type' => $type, 'Category.active' => 1 ) ) );
        
        $re = array();
        foreach ( $categories as $cat )
        {
            $removed = false;
            
            if ( !empty($role_id) && !empty($cat['Category']['create_permission']) )
            {
                $roles = explode(',', $cat['Category']['create_permission']);
                
                if (!in_array($role_id, $roles))
                    $removed = true;
            }

            if ( !$removed )
            {
                if ( $cat['Category']['header'] )
                {
                    $subs = array();
                    foreach ( $cat['children'] as $subcat )
                        $subs[$subcat['Category']['id']] = $subcat['Category']['name'];
                    
                    $re[$cat['Category']['name']] = $subs;
                }
                else
    	            $re[$cat['Category']['id']] = $cat['Category']['name'];
    	    }
        }
		
		return $re;
	}
}
 