<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
JsnApp::uses('Model', 'Model');

class AppModel extends Model {
		
	public $recursive = 0;
	
	/*
	 * Initialize the model with an array of empty fields
	 * @return array $res
	 */
	public function initFields()
	{
		$res[$this->name] = array_fill_keys( array_keys( $this->schema() ), '' );
		return $res;
	}
	
	public function increaseCounter($id, $field = 'comment_count')
	{
		$this->query("UPDATE $this->tablePrefix$this->table SET $field=$field+1 WHERE id=" . intval($id)); 
	}
	
	public function decreaseCounter($id, $field = 'comment_count')
	{
	    $this->query("UPDATE $this->tablePrefix$this->table SET $field=$field-1 WHERE id=" . intval($id)); 
	}
}
