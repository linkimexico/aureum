<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Conversation extends AppModel {

	public $belongsTo = array( 'User', 
							   'LastPoster' => array(
							   		'className' => 'User', 
							   		'foreignKey' => 'lastposter_id'
							)	);
	
	public $hasMany = array( 'Comment' => array( 
											'className' => 'Comment',	
											'foreignKey' => 'target_id',
											'conditions' => array( 'Comment.type' => APP_CONVERSATION ),						
											'dependent'=> true
										),
						  	 'ConversationUser' => array( 
						  					'className' => 'ConversationUser',												  			
						  					'dependent'=> true
										),
						); 
						
	public $validate = array(	
				'subject' => 	array( 	 
					'rule' => 'notEmpty',
					'message' => 'Subject is required'
				),
				'message' => 	array( 	 
					'rule' => 'notEmpty',
					'message' => 'Message is required'
				)
	);
}
