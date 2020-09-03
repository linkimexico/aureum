<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class UpgradeController extends AppController {
	    
    public function beforeFilter() 
    {
        // get the current logged in user
        $uid = $this->Session->read('uid'); 
        
        if ( $uid != ROOT_ADMIN_ID )
            die('Only root admin can run the upgrade wizard');

        // get the global settings  
        $jsnsocial_setting = $this->_getSettings();
        $this->set('jsnsocial_setting', $jsnsocial_setting);
    }
	
	public function index( $state = null )
	{		
		$content = file_get_contents( APP . 'Config' . DS . 'install' . DS . 'upgrade.xml' );
		$xml = new SimpleXMLElement($content);
		
		$this->set('latest_version', $xml->version[count($xml->version) - 1]->number);
		
		if ( $state == 'done' )
			$this->render('done');		
	}
	
	public function run()
	{
		$setting = $this->_getSettings();
		$this->loadModel('Setting');
		
		$content = file_get_contents( APP . 'Config' . DS . 'install' . DS . 'upgrade.xml' );
		$xml = new SimpleXMLElement($content);
		
		$latest_version = $xml->version[count($xml->version) - 1]->number;
		
		if ( $latest_version == $setting['version'] )
		{
		
			$this->redirect('/upgrade/index/done');
			exit;	
		}
		
		foreach ( $xml->version as $key => $version )
		{
			if ( $version->number > $setting['version'] )
			{
				$this->set('version', $version->number);						
				$this->set('latest_version', $latest_version);
				
				// run queries
				if ( !empty( $version->queries->query ) )
				{
					foreach ( $version->queries->query as $query )
					{
						$query = str_replace('{PREFIX}', $this->Setting->tablePrefix, $query);
						$this->Setting->query( $query );
					}
				}
				
				// update version
				$this->Setting->query( "UPDATE " . $this->Setting->tablePrefix . "settings SET value = '" . $version->number . "' WHERE field = 'version'" );
				
				// clear cache folders
				$models_path = CACHE . DS . 'models';			
				$files = scandir( $models_path );
				
				foreach ( $files as $file )
					if ( $file != '.' && $file != '..' )
						unlink( $models_path . DS . $file );
					
				$persistent_path = CACHE . DS . 'persistent';	
				$files = scandir( $persistent_path );
				
				foreach ( $files as $file )
					if ( $file != '.' && $file != '..' )
						unlink( $persistent_path . DS . $file );
						
				Cache::clear();
				
				return;		
			}
		}
	}
}
 
