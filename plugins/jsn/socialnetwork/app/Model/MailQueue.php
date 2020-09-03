<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class MailQueue extends AppModel {	
	
	public $validate = array(  
			'subject' => array( 	 
				'rule' => 'notEmpty'
			),
			'email' => 	array( 	 
				'email' => array(
					  'rule' => 'email',
					  'allowEmpty' => false
				)
			)
	);
}
