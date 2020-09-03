<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class ProfileFieldValue extends AppModel 
{	
	public $belongsTo = array( 'ProfileField' );
	
	public function getValues( $uid, $profile_fields_only = false, $show_heading = false )
	{
		$cond = array( 'ProfileField.active' => 1 );
        
        if ( $profile_fields_only )
            $cond['ProfileField.profile'] = 1;
        
        if ( $show_heading )
            $cond['OR'] = array( 'ProfileFieldValue.user_id' => $uid, 'ProfileField.type' => 'heading' );
        else
            $cond['ProfileFieldValue.user_id'] = $uid;
            
		$vals = $this->find( 'all', array( 'conditions' => $cond, 'order' => 'ProfileField.weight' ) );
							
		return $vals;
	}
}
