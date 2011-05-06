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

class MessageWindow extends GtkWindow {
    
  public function __construct( $message ) {
    parent::__construct();

    $this->strings = KioskClient::getStrings();
    
    $this->set_default_size( 300, 200 );
    $this->set_resizable( false );

    $this->set_keep_above( true );

    $this->set_title( "Message: $message" );
  
    $this->messageLabel = new GtkLabel();
    $this->messageLabel->set_use_markup( true );
    $this->messageLabel->set_markup( $this->strings["message_begin"] . $message . $this->strings["message_end"] );
    $this->set_title( $message );
    
    $hbox = new GtkHBox();
    $stock = GtkImage::new_from_stock( Gtk::STOCK_DIALOG_WARNING, Gtk::ICON_SIZE_DIALOG );
    $hbox->pack_start( $stock, 0, 0 );
    $hbox->pack_start( $this->messageLabel );
               
    $this->add( $hbox );
    $this->show_all();
    
  }

  public function updateMessage( $message ) {
    $this->messageLabel->set_markup( "<span color='red'>$message</span>" );    
  }
}
?>