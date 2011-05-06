<?php

class Statistic extends AppModel {
  var $name = 'Statistic';

  function getCurrentUsage( $machine_name_filters, $month = null, $year = null ) {
    $stats = array();
    
    $machines = array();
    foreach ( $machine_name_filters as $filter => $title ) {
      $machines[ $title ] = $filter;
    }

    if ( ! count( $machines ) ) {
      $machines["All Machines"] = ''; ## If no filters are set, select all.
    }
    
    if ( ! $month ) $month = date('m'); ## Month as 01 - 12
    if ( ! $year  ) $year = date('Y'); ## Year as YYYY
    $days = date('t', mktime( 0, 0, 0, 1, $month, $year ) ); ## Number of days gone through this month.

    for ( $i = 1; $i <= $days; $i++ ) {
      if ( $i < 10 ) {
        $day = '0' . $i;
      } else {
        $day = $i;
      }
      
      $total = 0;
      
      foreach ( $machines as $title => $filter ) {
        $results = $this->query("
          SELECT COUNT( DISTINCT( username ) ) as myCount FROM statistics 
          WHERE time LIKE '$year-$month-$day%'
          AND status = 'Logged out'
          AND machine LIKE '$filter%'
        ");

        $count = $results[0][0]['myCount'];
        $stats["$month-$day-$year"][$title] = $count;
        
        $total += $count;
      }
      
      $stats["$month-$day-$year"]['Total'] = $total;
              
    }
    
    return $stats;    
  }
}