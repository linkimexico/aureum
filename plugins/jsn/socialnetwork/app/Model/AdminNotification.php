<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class AdminNotification extends AppModel {	

	public $belongsTo = array( 'User' );
	
	public $validate = array( 'user_id' => array( 'rule' => 'notEmpty'),
							  'text' => array( 'rule' => 'notEmpty' ),
							  'url' => array( 'rule' => 'notEmpty' )
						 );
							  
	public $order = 'AdminNotification.id desc';
	
}
