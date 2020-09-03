<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class AttachmentsController extends AppController {
	
	public function download($id = null)
    {
        $id = intval($id);
        $this->autoRender = false;
            
        $attachment = $this->Attachment->findById($id);
        $this->_checkExistence($attachment);
        $this->_checkPermission( array('aco' => 'attachment_download') );        
        
        // update counter
        $this->Attachment->increaseCounter($id, 'downloads');
        
        // download file
        $path = WWW_ROOT . 'uploads' . DS . 'attachments' . DS . $attachment['Attachment']['filename'];
        
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"".$attachment['Attachment']['original_filename']."\""); 
        readfile($path);
    }
	
	public function ajax_remove($id = null)
	{
		$id = intval($id);
		$this->autoRender = false;
		
		$attachment = $this->Attachment->findById($id);
        $this->_checkExistence($attachment);
		$this->_checkPermission( array( 'admins' => array($attachment['Attachment']['user_id']) ));
		
		$this->Attachment->deleteAttachment($attachment);
	}
}

?>
