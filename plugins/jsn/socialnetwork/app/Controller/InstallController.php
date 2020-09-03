<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;
class InstallController extends AppController {
    
    public  $uses = array();
    private $db_link;
    
    public function beforeFilter() {}
    
    public function index()
    {       
        $this->_checkConfigFile();
    }
    
    // db settings
    public function ajax_step1()
    {       
        $this->_checkConfigFile();
        $this->layout = '';
        
        $db_serialized = $this->_connectDb( $this->request->data );
        $this->set( 'db_serialized', $db_serialized );
        
        // run sql query
        $sql = file_get_contents( APP . 'Config' . DS . 'install' . DS . 'install.txt'  );
        $sql = str_replace( '{PREFIX}', $this->request->data['db_prefix'], trim( $sql ) );        
        $queries = explode( ';', $sql ); 
        
        foreach ( $queries as $query )
        {
            if ( !empty( $query ) )
            {
                mysql_query( $query, $this->db_link );
                if ( mysql_error() ) 
                {
                    echo '<span id="jsnsocialError">' . mysql_error() . '</span>';
                    return;
                }
            }
        }
        
        $this->render('step2');
    }

    // site settings
    public function ajax_step2()
    {
        $this->_checkConfigFile();
        $this->layout = '';
        
        if ( empty( $this->request->data['site_name'] ) || empty( $this->request->data['site_email'] ) || empty( $this->request->data['timezone'] ) )
        {
            echo '<span id="jsnsocialError">All fields are required</span>';
            return;
        }
                
        $db = unserialize( $this->request->data['db_serialized'] );
        $db_serialize = $this->_connectDb( $db );   
        
        mysql_query("UPDATE " . $db['db_prefix'] . "settings SET value = '" . mysql_real_escape_string( $this->request->data['site_name'] ) . "' WHERE field = 'site_name'", $this->db_link);
        mysql_query("UPDATE " . $db['db_prefix'] . "settings SET value = '" . mysql_real_escape_string( $this->request->data['site_email'] ) . "' WHERE field = 'site_email'", $this->db_link);
        mysql_query("UPDATE " . $db['db_prefix'] . "settings SET value = '" . mysql_real_escape_string( $this->request->data['timezone'] ) . "' WHERE field = 'timezone'", $this->db_link);
        
        if ( mysql_error() ) 
        {
            echo '<span id="jsnsocialError">' . mysql_error() . '</span>';
            return;
        }
        
        $this->set( 'db_serialized', $db_serialize );       
        $this->render('step3');
    }
    
    // admin settings
    public function ajax_step3()
    {
        $this->_checkConfigFile();  
        $this->layout = ''; 
        
        if ( empty( $this->request->data['name'] ) || empty( $this->request->data['email'] ) || empty( $this->request->data['password'] )
             || empty( $this->request->data['password2'] ) || !isset( $this->request->data['timezone'] )
        )
        {
            echo '<span id="jsnsocialError">All fields are required</span>';
            return;
        }
        
        if ( $this->request->data['password'] != $this->request->data['password2'] )
        {
            echo '<span id="jsnsocialError">Passwords do not match</span>';
            return;
        }

        $db = unserialize( $this->request->data['db_serialized'] );
        $db_serialize = $this->_connectDb( $db );

        // create config file        
        $filename = APP . 'Config/config.php';
        $ciper    = rand( 11111111111111111111, 99999999999999999999 );
        $salt     = md5( $ciper . $_SERVER['HTTP_HOST'] ); 
        
        $content = '<?php
$CONFIG = array( "host"     => \'' . $db['db_host'] . '\',
                 "login"    => \'' . $db['db_username'] . '\',
                 "password" => \'' . $db['db_password'] . '\',
                 "database" => \'' . $db['db_name'] . '\',
                 "port"     => \'' . $db['db_socket'] . '\',
                 "prefix"   => \'' . $db['db_prefix'] . '\',
                 "salt"     => \'' . $salt . '\',
                 "cipher"   => \'' . $ciper . '\'
);';
        
        if ( file_put_contents($filename, $content) === FALSE )
        {
            echo '<span id="jsnsocialError">Cannot create file config</span>';
            return;
        }   
        
        // create admin account
        $password = md5( $this->request->data['password'] . $salt );
        $code     = md5( $this->request->data['email'] . microtime() );
        
        mysql_query("INSERT INTO " . $db['db_prefix'] . "users ( id, name, email, password, role_id, code, timezone, gender, birthday, created )
                     VALUES (" . ROOT_ADMIN_ID . ", '" . 
                             mysql_real_escape_string( $this->request->data['name'] ) . "','" .
                             mysql_real_escape_string( $this->request->data['email'] ) . "','" .
                             $password . "'," .
                             ROLE_ADMIN . ",'" .
                             $code . "','" .
                             mysql_real_escape_string( $this->request->data['timezone'] ) . "',
                             'Male',
                             NOW(),
                             NOW())", $this->db_link); 
        
        if ( mysql_error() ) 
        {
            echo '<span id="jsnsocialError">' . mysql_error() . '</span>';
            return;
        }    
                        
        $this->render('finish');
    }
    
    private function _connectDb( $data )
    {
        $host = $data['db_host'];
        
        if ( !empty( $data['db_socket'] ) )
            $host .= ':' . $data['db_socket'];
        
        $this->db_link = mysql_connect( $host , $data['db_username'], $data['db_password'] );
        
        if ( !$this->db_link )
        {
            echo '<span id="jsnsocialError">' . mysql_error() . '</span>';
            return;
        }
        
        $db_selected = mysql_select_db( $data['db_name'] );
        
        if ( !$db_selected ) 
        {
            echo '<span id="jsnsocialError">' . mysql_error() . '</span>';
            return;
        }
        
        $db_array = array( 'db_host'     => $data['db_host'],
                           'db_socket'   => $data['db_socket'],
                           'db_username' => $data['db_username'],
                           'db_password' => $data['db_password'],
                           'db_name'     => $data['db_name'],
                           'db_prefix'   => $data['db_prefix']
        );
        
        return serialize( $db_array );
    }
    
    private function _checkConfigFile()
    {
        // check for config file
        if ( file_exists( APP . 'Config' . DS . 'config.php' ) )
        {
            $this->redirect( '/' );
            return;
        }   
    }
}
 
