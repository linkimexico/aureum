<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class ActivityComment extends AppModel 
{
	public $hasMany = array( 'Like' => 	array( 'className' => 'Like',	
											   'foreignKey' => 'target_id',
											   'conditions' => array('Like.type' => 'activity_comment'),						
											   'dependent'=> true
											 ),
							); 
		
	public $validate = array( 'user_id' => array( 'rule' => 'notEmpty'),
							  'activity_id' => array( 'rule' => 'notEmpty'),
							  'comment' => array( 'rule' => 'notEmpty')
						 );
						 
	public $belongsTo = array( 'Activity'  => array('counterCache' => true), 
							   'User' 
	);
	
	public $order = 'ActivityComment.id asc';
}
