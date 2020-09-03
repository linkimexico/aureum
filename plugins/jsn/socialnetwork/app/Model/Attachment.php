<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class Attachment extends AppModel 
{
	public function getAttachments( $plugin_id, $target_id )
	{
		$attachments = $this->find('all', array('conditions' => array('plugin_id' => $plugin_id, 'target_id' => $target_id), 'order' => 'original_filename'));
		
		return $attachments;
	}
		
	public function deleteAttachment( $attachment )
	{
		if ( file_exists(WWW_ROOT . 'uploads' . DS . 'attachments' . DS . $attachment['Attachment']['filename']) )
			unlink(WWW_ROOT . 'uploads' . DS . 'attachments' . DS . $attachment['Attachment']['filename']);
			
		$this->delete( $attachment['Attachment']['id'] );
	}
}
 