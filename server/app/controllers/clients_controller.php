<?php

class ClientsController extends AppController {
  var $helpers = array('Javascript');
  var $uses = array('Client', 'Login', 'Setting');

  function beforeFilter() {
    $this->checkSession();
  }

  function index() {
    $this->pageTitle = 'View Registered Client Machines';
    $this->set( 'clients', $this->Client->getClientStates() );    

    $this->set( 'use_koha_integration', $this->Setting->get_setting('use_koha_integration') );
    $this->set( 'koha_intranet_url', $this->Setting->get_setting('koha_intranet_url') );
  }

  function kick_user ( $username  ) {
    $this->Login->kick_user( $username );
    $this->flash_to_index();
  }
  
  function unpause ( $username ) {
    $this->Login->unpause( $username );
    $this->flash_to_index();
  }

  function log_out ( $username ) {
    $this->Login->log_out( $username );
    $this->flash_to_index();
  }

  function reboot ( $machine_name ) {
    $this->Client->reboot( $machine_name );
    $this->flash_to_index();
  }

  function toggle_troublemaker ( $username ) {
    $this->log( "toggle_troublemaker ( $username )" );
    $this->Login->toggle_troublemaker( $username );
    $this->flash_to_index();
  }

  function flash_to_index() {
    $this->flash('Your Command Has Been Processed.', '/clients/index', $pause = 0);  
  }

}

?>