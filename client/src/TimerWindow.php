<?php
## Copyright 2007 Kyle Hall

## This file is part of libKi.

## libKi is free software; you can redistribute it and/or modify
## it under the terms of the GNU General Public License as published by
## the Free Software Foundation; either version 2 of the License, or
## (at your option) any later version.

## libKi is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.

## You should have received a copy of the GNU General Public License
## along with libKi; if not, write to the Free Software
## Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

require_once( 'DBInterface.php' );

class TimerWindow extends GtkWindow {
    public $logoutButton;
    public $quitButton;
    public $pauseButton;
    public $timeLabel;
    
    protected $timeLeft;

  public function __construct() {
    parent::__construct();
    
    $dbi = new DBInterface();

    $this->strings = KioskClient::getStrings();
    
    $this->set_position( Gtk::WIN_POS_CENTER );

    $this->set_default_size( 300, 200 );
    $this->set_resizable( false );
  
    $this->timeLabel = new GtkLabel( $this->strings["timeLeftLabel_begin"] . '0' . $this->strings["timeLeftLabel_end"] );
    $this->set_title( $this->strings["windowTitle_begin"] . '0' . $this->strings["windowTitle_end"] );
        
    $this->logoutButton = new GtkButton( $this->strings['logoutButton'] );
    $this->pauseButton = new GtkButton( $this->strings['pauseButton'] );
    
    $box = new GtkVBox();
    $box->pack_start( $this->timeLabel );

    if ( $dbi->getSetting( 'max_pause_time' ) ) {
      $box->pack_start( $this->pauseButton );
    }

    $box->pack_start( $this->logoutButton );

#    if ( DEBUG ) {
#      $this->quitButton = new GtkButton("Quit");
#      $box->pack_start( $this->quitButton );
#    }
  
#    $this->connect_simple( 'destroy', array('Gtk', 'main_quit') );
    
    $this->add( $box );
    
  }
  
  public function updateTime( $time ) {
    $this->timeLeft = $time;
    $this->timeLabel->set_label( $this->strings["timeLeftLabel_begin"] . $time . $this->strings["timeLeftLabel_end"] );
    $this->set_title( $this->strings["windowTitle_begin"] . $time . $this->strings["windowTitle_end"] );
  }
  
}
?>