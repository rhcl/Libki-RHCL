<?php

class Setting extends AppModel {
  var $name = 'Setting';

  function get_machine_name_filters() {
    if ( ! isset( $_SESSION['machine_name_filters'] ) ) {
      $filters = array();

      $setting = $this->Setting->findByName('machine_name_filters');
      $value = $setting['Setting']['value'];

      $pairs = explode( '::', $value );

      foreach ( $pairs as $pair ) {
        $arr = explode( '->', $pair );

        $filters[ $arr[1] ] = $arr[0];
      }

      $_SESSION['machine_name_filters'] = $filters;

      return $filters;
    } else {
      return $_SESSION['machine_name_filters'];
    }
  }


  function get_setting( $name ) {
    $setting = $this->findByName( $name );
    return $setting['Setting']['value'];
  }

  function write_setting( $name, $value ) {
    $setting = $this->findByName( $name );
    $setting['Setting']['value'] = $value;
    $this->save( $setting['Setting'], false );
  }                                                                                      
}