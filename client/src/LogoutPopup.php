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

class LogoutPopup extends GtkDialog {
    
  public function __construct() {
    parent::__construct();

    $this->strings = KioskClient::getStrings();

    $this->set_default_size( 200, 100 );
    $this->set_resizable( false );
    $this->set_modal( true );

    $label = new GtkLabel( $this->strings["logoutWarningLabel"] );
    $this->vbox->pack_start( $label );
    $this->vbox->show_all();

    $this->add_button( Gtk::STOCK_NO, Gtk::RESPONSE_NO );
    $this->add_button( GtK::STOCK_YES, Gtk::RESPONSE_YES );    
  }
}
?>