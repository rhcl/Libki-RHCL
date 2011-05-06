<?php

class LoginsController extends AppController {
  var $uses = array( 'Login', 'Setting' );
  var $helpers = array('Javascript');

  function beforeFilter() {
    $this->checkSession();
  }

  function index( $page = 1, $status = null, $username = null ) {

    if ( !empty( $_REQUEST['username'] ) ) {
      $username = $_REQUEST['username'];
      
      $this->log( "LoginsController::index: Username $username passed in for search" );
    }
    
    if ( !empty( $_REQUEST['status'] ) ) {
      $status = $_REQUEST['status'];
      $this->log( "LoginsController::index: Status $status passed in for search" );
      if ( $status == "Any" ) { $status = ''; }
    }

    $machine_name_filter = '';  
    if ( !empty( $_REQUEST['machine_name_filter'] ) ) {
      $machine_name_filter = $_REQUEST['machine_name_filter'];
      $this->log( "LoginsController::index: Machine name filter $machine_name_filter passed in for search" );
      if ( $machine_name_filter == "Any" ) { $machine_name_filter = null; }
    }
    
    $this->set( 'machine_name_filters', $this->get_machine_name_filters() );
    $this->set( 'use_koha_integration', $this->Setting->get_setting('use_koha_integration') );
    $this->set( 'koha_intranet_url', $this->Setting->get_setting('koha_intranet_url') );

    $this->pageTitle = 'View Active Users';
    $this->set( 'page', $page );
    $this->set( 'nextpage', $page + 1);
    $this->set( 'prevpage', $page - 1);
    $this->set( 'status', $status );
    $this->set( 'username', $username );
    $this->set( 'machine_name_filter', $machine_name_filter );
    
#    $conditions = "status LIKE '$status%' AND username LIKE '$username%' AND machine LIKE '$machine_name_filter%'";
    $conditions = "username LIKE '$username%' AND status LIKE '$status%' AND machine LIKE '$machine_name_filter%'";
    $this->set( 'logins', $this->Login->findAll( $conditions, $fields = null, $order = 'username', $limit = 20, $page ) );    
    
  }

  function set_units_popup ( $username ) {
    $this->layout = 'popup';    
    
    $this->set( 'username', $username );
  }
  
  function send_message_popup ( $username ) {
    $this->layout = 'popup';    
    
    $this->set( 'username', $username );
  }

  function update_notes_popup( $username ) {
    $this->layout = 'popup';
    
    $this->set( 'username', $username );

    $login = $this->Login->findByUsername( $username );
    $notes = $login['Login']['notes'];

    $this->set( 'notes', $notes );
  }
  
  function set_units ( $username = null, $amount = null ) {
    if ( $_REQUEST['username'] ) { $username = $_REQUEST['username']; }
    if ( ! empty( $_REQUEST['amount'] ) ) { $amount = $_REQUEST['amount']; }

    $this->Login->set_units( $username, $amount );

    $login = $this->Login->findByUsername( $username );
    $units = $login['Login']['units'];
        

    $this->layout = 'popup';      
    $this->set( 'username', $username );
    $this->set( 'amount', $units );

    
  }
  
  function send_message ( $username = null, $message = null ) {
    if ( $_REQUEST['username'] ) { $username = $_REQUEST['username']; }
    if ( $_REQUEST['message'] ) { $message = $_REQUEST['message']; }

    $this->layout = 'popup';      
    $this->set( 'username', $username );
    $this->set( 'message', $message );

    $this->Login->set_message( $username, $message );
    
  }

  function update_notes( $username = null, $notes = null ) {
    if ( $_REQUEST['username'] ) { $username = $_REQUEST['username']; }
    if ( $_REQUEST['notes'] ) { $notes = $_REQUEST['notes']; }
    
    $this->layout = 'popup';
    
    $this->set( 'username', $username );
    $this->set( 'notes', $notes );
    
    $this->Login->set_notes( $username, $notes );
  }

  function kick_user ( $username  ) {
    $this->Login->kick_user( $username );
    $this->flash_to_index();
  }
  
  function reset_password ( $username ) {
    $this->Login->reset_password( $username );
    $this->flash_to_index();
  }
  
  function delete_user ( $username ) {
    $this->Login->delete_user( $username );
    $this->flash_to_index();
  }
  
  function unpause ( $username ) {
    $this->Login->unpause( $username );
    $this->flash_to_index();
  }

  function set_staff ( $username ) {
    $this->Login->set_staff( $username );
    $this->flash_to_index();
  }

  function log_out ( $username ) {
    $this->Login->log_out( $username );
    $this->flash_to_index();
  }

  function toggle_troublemaker ( $username ) {
    $this->log( "toggle_troublemaker ( $username )" );
    $this->Login->toggle_troublemaker( $username );
    $this->flash_to_index();
  }

  function create() {
    $next_guest_id = $this->Setting->get_setting( 'next_guest_id' );
    $this->set( 'next_guest_id', $next_guest_id );

    if ( empty( $this->data['Login'] ) ) {
      
      $this->render();

    } else {
      $user = $this->Login->findByUsername( $this->data['Login']['username'] );
        
      if ( empty ( $this->data['Login']['username'] ) ) {
        $this->Login->invalidate('username'); // Populates tagErrorMsg('Login/username')        
      }

#      if ( empty ( $this->data['Login']['password'] ) ) {
#        $this->Login->invalidate('password');
#      }

      if ( empty ( $this->data['Login']['units'] ) ) {
        $this->Login->invalidate('units');        
      }

      // Invalidate the field to trigger the HTML Helper's error messages
      if ( ! empty( $user['Login']['username'] ) ) {
        $this->Login->invalidate('username_unique'); // Populates tagErrorMsg('User/username_unique')
      }
        
      ## MD5 the password, but only if there *is* a password.
      if ( $this->data['Login']['password'] ) {
        $this->data['Login']['password'] = $this->md5_base64( $this->data['Login']['password'] );
      }
        
      // Try to save as normal, shouldn't work if the field was invalidated.
      if ( $this->Login->save( $this->data ) ) {
        if ( $this->beginsWith( $this->data['Login']['username'], 'guest' ) ) {
          $this->Setting->write_setting( 'next_guest_id', $next_guest_id + 1 );
        }
        
        $this->redirect('/logins/');
      } else {
        $this->data['Login']['password'] = '';
        $this->render();
      }
    }
  }

  function get_machine_name_filters() {
    if ( ! isset( $_SESSION['machine_name_filters'] ) ) {
      $filters = array();
    
      $setting = $this->Setting->findByName('machine_name_filters');
      if ( $setting ) {
        $value = $setting['Setting']['value'];
    
        $pairs = explode( '::', $value );
    
        foreach ( $pairs as $pair ) {
          $arr = explode( '->', $pair );
        
          $filters[ $arr[1] ] = $arr[0];
        }

        $_SESSION['machine_name_filters'] = $filters;
      
        return $filters;
      }
    } else {
      return $_SESSION['machine_name_filters'];
    }
  }

  function flash_to_index() {
    $this->flash('Your Command Has Been Processed.', '/logins/index', $pause = 0);  
  }

  function md5_base64( $data ) {
    return preg_replace( '/=+$/', '', base64_encode( pack( 'H*', md5( $data ) ) ) );
  }
  
  function beginsWith( $str, $sub ) {
    return (strncmp($str, $sub, strlen($sub)) == 0);
  }
}

?>