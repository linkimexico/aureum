<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Report extends AppModel {
	
	public $belongsTo = array( 'User' );
						
	public $validate = array(	
							'reason' => 	array( 	 
								'rule' => 'notEmpty',
								'message' => 'Reason is required'
							)
	);
	
	public $order = 'Report.id desc';
}
