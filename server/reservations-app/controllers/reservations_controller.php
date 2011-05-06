<?php

class ReservationsController extends AppController {
  var $helpers = array('Javascript', 'Libki');
  
  function index() {
    $machines = $this->Reservation->getRegisteredMachines();
    $this->set( 'registered_machines', $machines );
  }

  function make_reservation( $machine, $start_timestamp ) {
    $this->layout = 'popup';
    
    $this->set( 'machine', $machine );
    $this->set( 'start_timestamp', $start_timestamp );
  }
  
  function confirm_reservation( $username = '', $password = '', $machine = '', $start_timestamp = '' ) {
    if ( empty( $username ) ) $username = $_REQUEST['username'];
    if ( empty( $password ) ) $password = $_REQUEST['password'];
    if ( empty( $machine ) ) $machine = $_REQUEST['machine'];
    if ( empty( $start_timestamp ) ) $start_timestamp = $_REQUEST['start_timestamp'];      

    $login = $this->Reservation->getLogin( $username );

    if ( $this->md5_base64( $password ) == $login['password'] ) {
      
      ## Check to see if user already has a reservation
      if ( $this->Reservation->canReserve( $username ) ) {
      
        ## Create the reservation
        $end_timestamp = $this->Reservation->createReservation( $username, $machine, $start_timestamp, $length = $login['units'] );
  
      } else {
        ## User already has a current reservation.
        $this->set( 'error', 'has_reservation' );
      }
    } else {
      ## Invalid username/password combination
      $this->set( 'error', 'invalid_user' );
    }
    
    $this->set( 'username', $username );
    $this->set( 'machine', $machine );
    $this->set( 'start_timestamp', $start_timestamp );
    $this->set( 'end_timestamp', $end_timestamp );
    $this->set( 'length', $login['units'] );
  }
  
  function md5_base64( $data ) {
    return preg_replace( '/=+$/', '', base64_encode( pack( 'H*', md5( $data ) ) ) );
  }    

}

?>
