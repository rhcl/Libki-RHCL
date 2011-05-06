<?php

class SettingsController extends AppController {
  var $helpers = array('Javascript');
  
  function beforeFilter() {
    $this->checkSession();
  }

  function index() {
    $settings = $this->Setting->findAll();
    
    $arr = array();
    foreach( $settings as $setting ) {
      $key = $setting['Setting']['name'];
      $value = $setting['Setting']['value'];
      
      $arr[ $key ] = $value;

      $this->set( 'settings', $arr );
    }
    
    
  }
  
  function updateTimeSettings() {
    ## Save max_pause_time
    $max_pause_time = $_REQUEST['max_pause_time'];    
    $setting = $this->Setting->findByName( 'max_pause_time' );
    $setting['Setting']['value'] = $max_pause_time;
    $this->Setting->save( $setting );

    ## Save time_before_auto_logout
#    $time_before_auto_logout = $_REQUEST['time_before_auto_logout'];
#    $setting = $this->Setting->findByName( 'time_before_auto_logout' );
#    $setting['Setting']['value'] = $time_before_auto_logout;
#    $this->Setting->save( $setting );

    ## Save post_crash_timeout
    $post_crash_timeout = $_REQUEST['post_crash_timeout'];
    $setting = $this->Setting->findByName( 'post_crash_timeout' );
    $setting['Setting']['value'] = $post_crash_timeout;
    $this->Setting->save( $setting );

    ## Save seconds_between_client_updates
    $seconds_between_client_updates = $_REQUEST['seconds_between_client_updates'];
    $setting = $this->Setting->findByName( 'seconds_between_client_updates' );
    $setting['Setting']['value'] = $seconds_between_client_updates;
    $this->Setting->save( $setting );

    ## Save daily_minutes
    $daily_minutes = $_REQUEST['daily_minutes'];
    $setting = $this->Setting->findByName( 'daily_minutes' );
    $setting['Setting']['value'] = $daily_minutes;
    $this->Setting->save( $setting );

    $this->flash( 'Your Settings Have Been Saved', '/settings/', '0' );
  }

  function updateMachineNameFilters() {
    $machine_name_filters = $_REQUEST['machine_name_filters'];
    $setting = $this->Setting->findByName( 'machine_name_filters' );
    $setting['Setting']['value'] = $machine_name_filters;
    $this->Setting->save( $setting );
    
    $this->flash( 'Your Settings Have Been Saved', '/settings/', '0' );
  }

  function updateKohaIntegration() {
    $use_koha_integration = $_REQUEST['use_koha_integration'];
    $setting = $this->Setting->findByName( 'use_koha_integration' );
    $setting['Setting']['value'] = $use_koha_integration;
    $this->Setting->save( $setting );
    
    $koha_intranet_url = $_REQUEST['koha_intranet_url'];
    $setting = $this->Setting->findByName( 'koha_intranet_url' );
    $setting['Setting']['value'] = $koha_intranet_url;
    $this->Setting->save( $setting );
    
    $this->flash( 'Your Settings Have Been Saved', '/settings/', '0' );
  }

  ## Loads the about page
  function about() {
    ## Probably want to put version number and stuff here.
  }

  ## Loads the help page
  function help() {
  }
  
}

?>