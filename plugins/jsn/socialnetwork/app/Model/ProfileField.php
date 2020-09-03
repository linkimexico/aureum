<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class ProfileField extends AppModel {
	    
    public $order = 'ProfileField.weight asc';	

	public $hasMany = array( 'ProfileFieldValue' => array( 
												'className' => 'ProfileFieldValue',						
												'dependent'=> true
											)
							); 

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
	
	// get custom fields for registration page
	public function getRegistrationFields( $exclude_heading = false )
	{
		$cond = array( 'ProfileField.active' => 1, 
		               'ProfileField.registration' => 1
                     );
                     
        if ( $exclude_heading )
            $cond['ProfileField.type <> ?'] = 'heading';
            
		$custom_fields = $this->find( 'all', array( 'conditions' => $cond ) );
									
		return 	$custom_fields;
	}

}