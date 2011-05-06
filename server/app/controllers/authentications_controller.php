<?php

class AuthenticationsController extends AppController {

  function index() {
  }


  function login() {
    ## Don't show the error message if no data has been submitted
    $this->set( 'error', false );

    #$this->log( 'Username is ' . $this->Session->data['Authentication']['username'] );
    ## if a user has submitted form data:
    if ( ! empty( $this->data ) ) {
      ## First, let's see of there are any users in the database with the username
      ## supplied by the user using the form:
      
      $someone = $this->Authentication->findByUsername( $this->data['Authentication']['username'] );
      
      ## At this point, $someone is full of user data, or its empty.
      ## Let's compare the form-submitted password with the one in
      ## the database.
      
      $this->log( "Password given by user: " . $this->data['Authentication']['password'] );
      $this->log( "Password as md5_base64: " . $this->md5_base64( $this->data['Authentication']['password'] ) );
      $this->log( "Password from database: " . $someone['Authentication']['password'] );
      if ( ! empty( $someone['Authentication']['password'] ) 
            && $this->md5_base64( $this->data['Authentication']['password'] ) == $someone['Authentication']['password'] 
            && $someone['Authentication']['admin'] == '1' ) {
        ## should hash passwords
        
        ## session information to remember this user as 'logged-in'.
        if ( $this->Session->write( 'Authentication', $someone['Authentication'] ) ) {
          $this->log( 'Writing of Session Authentication Succeeded' );
          
          $this->log( 'Checking Session Authentication' );
          if ( $this->Session->check('Authentication') ) {
            $this->log( 'Session Authentication Check Succeeded.' );
          } else {
            $this->log( 'Session Authentication Check Failed.' );
          }
          
       } else {
          $this->log( 'Writing of Session Authentication Failed' );
        }

        ## Now that we have them stored in a session, forward them on
        ## to a landing page for the application
        $this->redirect('/logins');
      } else { ## Else they supplied incorrect data:
        $this->set('error', true );
      }
    }
  }
  
  function logout() {
    $this->Session->delete('Authentication');
    $this->redirect('/logins');
  }
  
  function md5_base64( $data ) {
    return preg_replace( '/=+$/', '', base64_encode( pack( 'H*', md5( $data ) ) ) );
  }

}

?>