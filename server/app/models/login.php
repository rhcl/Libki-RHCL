<?php

class Login extends AppModel {
  var $name = 'Login';

  var $validate = array(
    'username' => array(
      'required' => true,
      'rule' => 'isUnique',
      'message' => 'This username has already been taken.'
    ),
  );

  function toggle_troublemaker( $username ) {
    if ( DEBUG ) $this->log( "toggle_troublemaker( $username )" );
    $login = $this->findByUsername( $username );
    $this->id = $login['Login']['id'];

    $this->log( "Troublemaker status: " . $login['Login']['troublemaker'] );
    if ( $login['Login']['troublemaker'] ) {
      $this->saveField( 'troublemaker', '0' );
      $this->log('Setting to 0');
    } else {
      $this->saveField( 'troublemaker', '1' );
      $this->log('Setting to 1');
    }
  }

  function kick_user ( $username ) {
    $this->create_stat( $username, 'Kicked' );
    $ret = $this->query("UPDATE logins SET status = 'Kicked' WHERE username = '$username'");
  }

  function reset_password ( $username ) {
    $this->create_stat( $username, 'Password reset' );
    $ret = $this->query("UPDATE logins SET password = '' WHERE username = '$username'");
  }

  function delete_user ( $username ) {
    $this->create_stat( $username, 'Deleted' );
    $ret = $this->query("DELETE FROM logins WHERE username = '$username'");
  }

  function unpause ( $username ) {
    $this->create_stat( $username, 'Logged in' );
    $ret = $this->query("UPDATE logins SET status = 'Logged in' WHERE username = '$username'");
  }

  function set_staff ( $username ) {
    $this->create_stat( $username, 'Set as staff' );
    $ret = $this->query("UPDATE logins SET status = 'Staff user account' WHERE username = '$username'");
  }

  function log_out ( $username ) {
    $this->create_stat( $username, 'Logged out' );
    $ret = $this->query("UPDATE logins SET status = 'Logged out' WHERE username = '$username'");
  }
  
  function set_units ( $username, $amount ) {
    $this->create_stat( $username, "Units updated to $amount" );
    
    $sign = $amount[0];
    if ( $sign == "+" || $sign == "-" ) {
      $ret = $this->query("UPDATE logins SET units = units + $amount WHERE username = '$username'");
    } else {
      $ret = $this->query("UPDATE logins SET units = $amount WHERE username = '$username'");
    }
  }

  function set_message ( $username, $message ) {
    $this->create_stat( $username, 'Message sent' );
    $ret = $this->query("UPDATE logins SET message = '$message' WHERE username = '$username'");
  }
  
  function set_notes( $username, $notes ) {
    $ret = $this->query("UPDATE logins SET notes = '$notes' WHERE username = '$username'");
  }    

  function create_stat ( $username, $status ) {
    $ret = $this->query("INSERT INTO statistics ( username, machine, status, time ) VALUES ( '$username', ( SELECT machine FROM logins WHERE username = '$username' ), '$status', NOW() )");
  }
  
  function md5_base64( $data ) {
    return preg_replace( '/=+$/', '', base64_encode( pack( 'H*', md5( $data ) ) ) );
  }
}