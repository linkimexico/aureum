<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
JsnApp::uses('Sanitize', 'Utility');

class SettingsController extends AppController 
{
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->_checkPermission(array('super_admin' => 1));
	}

	public function admin_index()
	{		
		$uid = $this->Session->read('uid');
			
		if ( !empty( $this->request->data ) )
		{
			$setting = $this->_getSettings();
			
			foreach ($this->request->data as $key => $value)
			{
				if ( array_key_exists($key, $setting) && $setting[$key] != $value )
					$this->Setting->updateAll( array( 'value' => '"' . Sanitize::escape( $value ) . '"' ), array( 'field' => $key ) );
			}
			
			// remove logo
			if ( !empty( $this->request->data['remove_logo'] ) )
			{
				if ($setting['logo'] && file_exists(WWW_ROOT . $setting['logo']))
					unlink(WWW_ROOT . $setting['logo']);
					
				$this->Setting->updateAll( array( 'value' => '""' ), array( 'field' => 'logo' ) );
			}
			
			// site logo
			if ( isset($_FILES['Filedata']) && is_uploaded_file($_FILES['Filedata']['tmp_name']) )
			{
				$size = getimagesize($_FILES['Filedata']['tmp_name']);

				if ( empty($size) || !in_array( $size['mime'], array( 'image/png', 'image/jpeg', 'image/gif' ) ) )
				{
					$this->Session->setFlash( 'Invalid image', 'default', array( 'class' => 'error-message') );
					$this->redirect( $this->request->referer() );
					exit;
				}
					
				$filename = 'uploads' . DS . $_FILES['Filedata']['name'];
				$path = WWW_ROOT . $filename;
				
				// remove old logo if exists
				if ($setting['logo'] && file_exists(WWW_ROOT . $setting['logo']))
					unlink(WWW_ROOT . $setting['logo']);
				
				if (move_uploaded_file($_FILES['Filedata']['tmp_name'], $path))					
					$this->Setting->updateAll( array( 'value' => '"' . Sanitize::escape( $filename ) . '"' ), array( 'field' => 'logo' ) );					
			}

			$this->Session->setFlash( 'Changes have been saved' );
			$this->redirect( $this->request->referer() );
		}
		
		$this->set('title_for_layout', 'System Settings');		
	}
}