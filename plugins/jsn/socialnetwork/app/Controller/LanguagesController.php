<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class LanguagesController extends AppController 
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkPermission( array('super_admin' => true) ); 
    } 
        
    public function admin_index()
    {
        $langs = $this->Language->find( 'all' );
        $installed_langs = array();
        
        foreach ( $langs as $lang )
            $installed_langs[] = $lang['Language']['key'];
        
        $all_langs  = scandir( APP . 'Locale' );      
        $not_installed_langs = array();
            
        foreach ( $all_langs as $lang )
            if ( !in_array( $lang, $installed_langs ) && !in_array($lang, array( '.', '..' ) ) && is_dir(APP . 'Locale' . DS . $lang) && strlen($lang) == 3 )
                $not_installed_langs[] = $lang;
        
        $this->set('languages', $langs);
        $this->set('not_installed_languages', $not_installed_langs);
    }    
    
    public function admin_do_install( $key )
    {
        if ( file_exists( APP . 'Locale' . DS . $key ) )
        {
            if ( $this->Language->save( array( 'name' => ucfirst($key), 'key' => $key ) ) )
            {
                Cache::delete('site_langs');
                
                $this->Session->setFlash('Language has been successfully installed');
            }
            else
                $this->Session->setFlash( 'An error has occured', 'default', array( 'class' => 'error-message') );
        }
        else
            $this->Session->setFlash( 'Cannot read theme info file', 'default', array( 'class' => 'error-message') );
        
        $this->redirect( $this->referer() );
    }
    
    public function admin_do_uninstall( $id )
    {
        $language = $this->Language->findById( $id );
        $this->_checkExistence( $language );
        
        if ( !$language['Language']['key'] != 'eng' )
        {
            $this->Language->delete( $id );   
            Cache::delete('site_langs');
                     
            $this->Session->setFlash('Language has been successfully uninstalled');
        }
        else
            $this->Session->setFlash( 'Core language cannot be uninstalled', 'default', array( 'class' => 'error-message') );
        
        $this->redirect( $this->referer() );
    }

    public function admin_ajax_edit( $id = null )
    {
        $language = $this->Language->findById( $id );
        $this->_checkExistence($language);        
      
        $this->set('language', $language);         
    }

    public function admin_ajax_save()
    {    
        $this->autoRender = false;
        $this->Language->id = $this->request->data['id'];
        
        $this->Language->set( $this->request->data );
        $this->_validateData( $this->Language );
        
        $this->Language->save( $this->request->data );     
        Cache::delete('site_langs');
           
        $this->Session->setFlash('Language has been successfully updated');
        
        $response['result'] = 1;
        echo json_encode($response);
    }
}
    