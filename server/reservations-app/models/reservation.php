<?php

class Reservation extends AppModel {
  var $name = 'Reservation';

  function getRegisteredMachines() {
    $data =  $this->query("SELECT * FROM clients ORDER BY machine_name");
    $machines = array();
    foreach( $data as $d ) {
      $machines[] = $d['clients']['machine_name'];
    }
    return $machines;
  }

  function getLogin( $username ) {
    $username = mysql_escape_string( $username );
    $data = $this->query("SELECT * FROM logins WHERE username = '$username'");
    if ( isset($data[0]['logins'] ) ) return $data[0]['logins'];
  }
  
  function getAvailability() {
  
  }
  
  function canReserve( $username ) {
    $username = mysql_escape_string( $username );
    $data = $this->query("SELECT COUNT(*) AS res_count FROM reservations WHERE username LIKE '$username' ");
    $hasReservation = $data[0]['reservations']['res_count'];
    return !$hasReservation;
  }
  
  function createReservation( $username, $machine, $start_timestamp, $length ) {
    $username = mysql_escape_string( $username );
    $machine = mysql_escape_string( $machine );
    $start_timestamp = mysql_escape_string( $start_timestamp );
    $length = mysql_escape_string( $length );
    
    $sql = "INSERT INTO reservations ( machine_name, username, starting_time, ending_time )
            VALUES ( '$machine', '$username', FROM_UNIXTIME( $start_timestamp ), DATE_ADD( FROM_UNIXTIME( $start_timestamp ), INTERVAL $length MINUTE ) )";
    $this->query( $sql );
    
    $sql = "SELECT UNIX_TIMESTAMP( ending_time ) as end_timestamp FROM reservations 
            WHERE username = '$username' AND machine_name = '$machine' AND starting_time = FROM_UNIXTIME( $start_timestamp )";
    $result = $this->query( $sql );

    $this->log( print_r( $result,1 ) );
    $this->log( $result[0][0]['end_timestamp'] );    
    return $result[0][0]['end_timestamp'];
  }
}