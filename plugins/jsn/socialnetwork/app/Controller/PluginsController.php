<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class PluginsController extends AppController 
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkPermission( array('super_admin' => true) ); 
    } 
        
    public function admin_index()
    {
        // get all installed plugins
        $plugins = $this->Plugin->find( 'all' );
        $installed_plugins = array();
        
        foreach ( $plugins as $plugin )
            $installed_plugins[] = $plugin['Plugin']['key'];
        
        // get all plugins in folder
        $all_plugins  = scandir( APP . 'Config' . DS . 'plugins' );      
        $not_installed_plugins = array();        
            
        foreach ( $all_plugins as $plugin )
        {
            $plugin = substr($plugin, 0, -4);
            if ( !in_array( $plugin, $installed_plugins ) && !empty( $plugin ) )
                $not_installed_plugins[] = $plugin;
        }        
        
        $this->set('plugins', $plugins);
        $this->set('not_installed_plugins', $not_installed_plugins);
    } 
    
    public function admin_ajax_view( $id = null )
    {
        $plugin = $this->Plugin->findById( $id );
        $this->_checkExistence($plugin);        

        // get plugin info
        $content = file_get_contents( APP . 'Config' . DS . 'plugins' . DS . $plugin['Plugin']['key'] . '.xml' );
        $info = new SimpleXMLElement($content);
                
        $settings = unserialize($plugin['Plugin']['settings']);
        
        // get all roles
        $this->loadModel('Role');
        $roles = $this->Role->find('all');
        
        $this->set('roles', $roles);        
        $this->set('info', $info);      
        $this->set('plugin', $plugin);     
        $this->set('settings', $settings);     
    }
    
    public function admin_do_download( $key )
    {
        $zip = new ZipArchive;
        
        if ( $zip->open( WWW_ROOT . DS . 'uploads' . DS . 'tmp' . DS . $key . '.zip', ZipArchive::CREATE ) === TRUE ) 
        {
            // add xml file
            $plugin_dir = 'Config' . DS . 'plugins';
            $zip->addEmptyDir( $plugin_dir );
            $zip->addFile( APP . $plugin_dir . DS . $key . '.xml', $plugin_dir . DS . $key . '.xml' );
            
            // add plugin folder
            addDir( APP . 'Plugin' . DS . Inflector::camelize($key), $zip, 'Plugin' . DS . Inflector::camelize($key) );
            
            if ( !$zip->close() )
                $this->_showError('Cannot create zip file');
            
            $this->redirect( '/uploads/tmp/' . $key . '.zip' );
        }
        else
            $this->_showError('Cannot create zip file');
    }
    
    public function admin_do_install( $key )
    {
        if ( file_exists( APP . 'Config' . DS . 'plugins' . DS . $key . '.xml' ) )
        {
            $content = file_get_contents( APP . 'Config' . DS . 'plugins' . DS . $key . '.xml' );
            $info = new SimpleXMLElement($content);            
            $settings = array();
            
            if ( !empty( $info->settings ) )
                foreach ( get_object_vars($info->settings) as $key => $data )
                {
                    $data = get_object_vars($data);       
                    $settings[$key] = $data['value'];
                }                

            $this->Plugin->set( array( 'name' => $info->name, 
                                       'key' => $info->key, 
                                       'menu' => $info->menu, 
                                       'url' => $info->url, 
                                       'version' => $info->version, 
                                       'settings' => serialize($settings)
                            ) );
            
            if ( $this->Plugin->save() )
            {
                // run sql script here
                
                //Cache::delete('plugins');
                Cache::clearGroup('cache_group', '_cache_group_');
                    
                $this->Session->setFlash('Plugin has been successfully installed');
            }
            else
                $this->Session->setFlash( 'An error has occured', 'default', array( 'class' => 'error-message') );
        }
        else
            $this->Session->setFlash( 'Cannot read plugin info file', 'default', array( 'class' => 'error-message') );
        
        $this->redirect( $this->referer() );
    }
    
    public function admin_do_uninstall( $id )
    {
        $plugin = $this->Plugin->findById( $id );
        $this->_checkExistence( $plugin );

        // run plugin uninstall script here
        
        $this->Plugin->delete( $id );   
        //Cache::delete('plugins');
        Cache::clearGroup('cache_group', '_cache_group_');
        
        $this->Session->setFlash('Plugin has been successfully uninstalled');
        $this->redirect( $this->referer() );
    }
    
    public function admin_do_upgrade( $id )
    {
        $plugin = $this->Plugin->findById( $id );
        $this->_checkExistence( $plugin );
            
        if ( file_exists( APP . 'Config' . DS . 'plugins' . DS . $plugin['Plugin']['key'] . '.xml' ) )
        {
            $content = file_get_contents( APP . 'Config' . DS . 'plugins' . DS . $plugin['Plugin']['key'] . '.xml' );
            $info = new SimpleXMLElement($content);
            
            // upgrade if there's a new version
            if ( $info->version > $plugin['Plugin']['version'] )
            {
                // run upgrade script here
                
                $this->Plugin->id = $id;
                $this->Plugin->save( array( 'version' => $info->version ) );
                
                $this->Session->setFlash('Plugin has been successfully installed');            
            }
            else
                $this->Session->setFlash('Plugin is already up to date');    
        }
        else
            $this->Session->setFlash( 'Cannot read plugin info file', 'default', array( 'class' => 'error-message') );
        
        $this->redirect( $this->referer() );
    }

    public function admin_do_enable( $id )
    {
        $plugin = $this->Plugin->findById( $id );
        $this->_checkExistence( $plugin );
        
        $this->Plugin->id = $id;
        $this->Plugin->save( array( 'enabled' => 1 ) );
        
        //Cache::delete('plugins');
        Cache::clearGroup('cache_group', '_cache_group_');
        
        $this->Session->setFlash('Plugin has been successfully enabled');
        $this->redirect( $this->referer() );
    }
    
    public function admin_do_disable( $id )
    {
        $plugin = $this->Plugin->findById( $id );
        $this->_checkExistence( $plugin );
        
        $this->Plugin->id = $id;
        $this->Plugin->save( array( 'enabled' => 0 ) );
        
        //Cache::delete('plugins');
        Cache::clearGroup('cache_group', '_cache_group_');
        
        $this->Session->setFlash('Plugin has been successfully disabled');
        $this->redirect( $this->referer() );
    }
    
    public function admin_ajax_reorder()
    {
        $this->autoRender = false;
        
        $i = 1;
        foreach ($this->request->data['plugins'] as $plugin_id)
        {
            $this->Plugin->updateAll( array( 'weight' => $i ), array( 'id' => $plugin_id ) );
            $i++;
        }
        
        //Cache::delete('plugins');
        Cache::clearGroup('cache_group', '_cache_group_');
    }
    
    public function admin_ajax_save()
    {
        $this->autoRender = false;
        
        $plugin = $this->Plugin->findById( $this->request->data['id'] );
        $this->_checkExistence( $plugin );
        
        $settings = array();
        
        if ( file_exists( APP . 'Config' . DS . 'plugins' . DS . $plugin['Plugin']['key'] . '.xml' ) )
        {
            $content = file_get_contents( APP . 'Config' . DS . 'plugins' . DS . $plugin['Plugin']['key'] . '.xml' );
            $info = new SimpleXMLElement($content);            
            
            if ( !empty( $info->settings ) )
                foreach ( get_object_vars($info->settings) as $key => $data )
                    $settings[$key] = $this->request->data[$key];    
        }
        
        $this->request->data['permission'] = (empty( $this->request->data['everyone'] )) ? implode(',', $_POST['permissions']) : '';
        $this->request->data['settings'] = serialize($settings);
        
        $this->Plugin->id = $this->request->data['id'];
        $this->Plugin->save( $this->request->data );
        
        //Cache::delete('plugins');
        Cache::clearGroup('cache_group', '_cache_group_');
        $this->Session->setFlash('Plugin has been successfully updated');
    }
}
    