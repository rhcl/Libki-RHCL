<?php

class StatisticsController extends AppController {
  var $uses = array( 'Statistic', 'Setting' );
  var $helpers = array('Javascript');
  
  function beforeFilter() {
    $this->checkSession();
  }

  function index( $month = null, $year = null, $target = 'web' ) {
    $machine_name_filters = $this->Setting->get_machine_name_filters();

    if ( ! $month ) $month = ( isset($_REQUEST['month']) ) ? $_REQUEST['month'] : date("m");
    if ( ! $year ) $year = ( isset($_REQUEST['year']) ) ? $_REQUEST['year'] : date("Y");
    
    $machines = array();
    foreach ( $machine_name_filters as $filter => $title ) {
      $machines[] = $title;
    }
    $machines[] = "Total";
    
    $statistics = $this->Statistic->getCurrentUsage( $machine_name_filters, $month, $year );

    $footer = array();
    ## Create footer totals
    foreach( $statistics as $s ) {
      foreach ( $machines as $m ) {
        $footer[$m] += $s[$m];
      }
    }

    if ( $target == 'print' ) {
      $this->layout = 'popup';
    }

    $this->set( 'month', $month );
    $this->set( 'year', $year );
    $this->set( 'target', $target );    
    $this->set( 'machines', $machines );
    $this->set( 'statistics', $statistics );
    $this->set( 'footer', $footer );    
  }
}

?>