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

class AlertPopup extends GtkDialog {
    
  public function __construct( $message ) {
    parent::__construct();

    $this->strings = KioskClient::getStrings();

    $this->set_position( GtK::WIN_POS_CENTER_ALWAYS );
    $this->set_default_size( 200, 100 );
    $this->set_resizable( false );
    $this->set_modal( false );

    $alertMessage = new GtkLabel();
    $alertMessage->set_use_markup( true );
    $alertMessage->set_markup( $message );

    $this->vbox->pack_start( $hbox = new GtkHBox() );
    $stock = GtkImage::new_from_stock( Gtk::STOCK_DIALOG_WARNING, Gtk::ICON_SIZE_DIALOG );
    $hbox->pack_start( $stock, 0, 0 );
    $hbox->pack_start( $alertMessage );

    $this->add_button( Gtk::STOCK_OK, Gtk::RESPONSE_OK );
    $this->action_area->set_layout( Gtk::BUTTONBOX_SPREAD );
    $this->set_has_separator( false );
    
    $this->show_all();
    $this->run();
    $this->destroy();
  }
}
?>