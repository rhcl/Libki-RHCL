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

require_once('KioskClient.phpw');
require_once('MessageWindow.php');
require_once('AlertPopup.php');
require_once('DBInterface.php');

class SetPasswordWindow extends GtkWindow {
    
  public function __construct( $username ) {
    parent::__construct();
    
    $this->username = $username;
    $this->strings = KioskClient::getStrings();
    
#    $this->set_default_size( 300, 200 );
    $this->set_resizable( false );
    $this->set_modal( true );
    
    $this->set_keep_above( true );

    $this->set_title( $this->strings['setPassword'] );
  
    $this->messageLabel = new GtkLabel();
    $this->messageLabel->set_use_markup( true );
    $this->messageLabel->set_markup( $this->strings['setPassword'] );

    $passwordLabel1 = new GtkLabel();
    $passwordLabel1->set_use_markup( true );
    $passwordLabel1->set_label( $this->strings['passwordLabel'] );
    $this->passwordEntry1 = new GtkEntry();
    $this->passwordEntry1->set_visibility( 0 );
    $passwordBox1 = new GtkHBox();
    $passwordBox1->pack_start( $passwordLabel1 );
    $passwordBox1->pack_start( $this->passwordEntry1 );    

    $passwordLabel2 = new GtkLabel();
    $passwordLabel2->set_use_markup( true );
    $passwordLabel2->set_label( $this->strings['passwordLabel'] );
    $this->passwordEntry2 = new GtkEntry();
    $this->passwordEntry2->set_visibility( 0 );
    $passwordBox2 = new GtkHBox();
    $passwordBox2->pack_start( $passwordLabel2 );
    $passwordBox2->pack_start( $this->passwordEntry2 );
     
    $this->setPasswordButton = new GtkButton( $this->strings['setPasswordButton'] );

    $mainBox = new GtkVBox(); 
    $mainBox->pack_start( $this->messageLabel );
    $mainBox->pack_start( $passwordBox1 );
    $mainBox->pack_start( $passwordBox2 );
    $mainBox->pack_start( $this->setPasswordButton );                      

    $this->add( $mainBox );

    $this->connect( 'key-press-event', array( &$this, 'onKey' ), $input );
    $this->setPasswordButton->connect_simple( 'clicked', array( &$this, 'onClick' ) );
        
    $this->show_all();
    
  }

  ## This function is called whenever the enter key his pressed on the login screen.
  ## If either field is empty, it will jump to the empty field.
  ## If neither field is empty, it will act the same as clicking the 'Log In' button.
  public function onKey( $widget, $event, $input ) {
    if ( DEBUG ) echo "SetPasswordWindow::onKey()\n";
    if ( $event->keyval == Gdk::KEY_Return ) {
      $this->checkPasswords();
      return true;
    } else {
      return false;
    }
  }
  
  public function onClick() {
    if ( DEBUG ) echo "SetPasswordWindow::onClick()\n";
    $this->checkPasswords();
  }

  public function checkPasswords() {
      if ( DEBUG ) echo "SetPasswordWindow::checkPasswords()\n";
      $password1 = $this->passwordEntry1->get_text();
      $password2 = $this->passwordEntry2->get_text();

      if ( empty( $password1 ) ) {
        if ( DEBUG ) echo "SetPasswordWidnwo::checkPasswords() :: Password Field 1 is Empty!\n";
        $this->passwordEntry1->grab_focus();
      } elseif ( empty( $password2 ) ) {
        if ( DEBUG ) echo "SetPasswordWidnwo::checkPasswords() :: Password Field 2 is Empty!\n";
        $this->passwordEntry2->grab_focus();
      } elseif ( $password1 != $password2 ) {
        if ( DEBUG ) echo "SetPasswordWidnwo::checkPasswords() :: Passwords Do Not Match!\n";
        $this->messageLabel->set_markup( $this->strings['passwordsDoNotMatch'] );
        $this->passwordEntry1->set_text( '' );
        $this->passwordEntry2->set_text( '' );
        $this->passwordEntry1->grab_focus();
      } else { ## Passwords match, Huzzah!!!
        if ( DEBUG ) echo "SetPasswordWidnwo::checkPasswords() :: Passwords Match! Updating Database.\n";
        $db = new DBInterface();
        $db->setPassword( $this->username, $password1 );
        $this->destroy();
      }
  
  }
}
?>