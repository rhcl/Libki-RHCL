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

class LoginWindow extends GtkWindow {
  public $messageLabel;
  public $usernameEntry;
  public $passwordEntry;
  public $loginButton;
  public $quitButton;

  protected $strings;
  protected $paused;
    
  public function __construct( $status = null ) {
    if ( DEBUG ) echo "LoginWindow::__construct( \$status = $status )\n";

    parent::__construct();

    $this->strings = KioskClient::getStrings();
    
    $this->status = $status;

    $screen = GdkScreen::get_default();
    $screenWidth = $screen->get_width();
    $screenHeight = $screen->get_height();
  
    $this->set_decorated( 1 );
    $this->set_default_size( 800, 600 ); ##Temporary Changed for testing  ($screenWidth, $screenHeight)
    #$this->fullscreen();
    $this->set_keep_above( 0 );
    $this->set_modal( 1 );
      
    $messageBox = new GtkHBox();
    $this->loginBox = new GtkVBox();
    $this->errorBox = new GtkVBox();
    $usernameBox = new GtkHBox();
    $passwordBox = new GtkHBox();
    
    ## Build the login box
    $this->messageLabel = new GtkLabel(); 
    $this->messageLabel->set_use_markup( true ); 
    $this->messageLabel->set_label( $this->strings['promptLabel'] );

    $usernameLabel = new GtkLabel();
    $usernameLabel->set_use_markup( true );
    $usernameLabel->set_label( $this->strings['usernameLabel'] );

    $this->usernameEntry = new GtkEntry();
    $passwordLabel = new GtkLabel();
    $passwordLabel->set_use_markup( true );
    $passwordLabel->set_label( $this->strings['passwordLabel'] );
    $this->passwordEntry = new GtkEntry();
    $this->passwordEntry->set_visibility( 0 );
    $this->loginButton = new GtkButton( $this->strings['loginButton'] );
  
    $messageBox->pack_start( $this->messageLabel );
    $usernameBox->pack_start( $usernameLabel );
    $usernameBox->pack_start( $this->usernameEntry );
    $passwordBox->pack_start( $passwordLabel );
    $passwordBox->pack_start( $this->passwordEntry );

    $this->loginBox->pack_start( $messageBox );
    $this->loginBox->pack_start( $usernameBox );
    $this->loginBox->pack_start( $passwordBox );
    $this->loginBox->pack_start( $this->loginButton );
    
    ## Build the error message box
    $this->errorLabel = new GtkLabel();
    $this->errorLabel->set_use_markup( true );
    $this->errorLabel->set_label( 'Error : Username and Password Do Not Match' );
    $errorLabelBox = new GtkHBox();
    $errorLabelBox->pack_start( $this->errorLabel );
    $this->errorButton = new GtkButton( 'OK' );
    $errorButtonBox = new GtkHBox();
    $errorButtonBox->pack_start( $this->errorButton );
    $this->errorBox->pack_start( $errorLabelBox );
    $this->errorBox->pack_start( $errorButtonBox );

    if ( DEBUG ) {
      $this->quitButton = new GtkButton("Quit");
      $this->loginBox->pack_start( $this->quitButton );
    }  
    $this->connect_simple( 'destroy', array('Gtk', 'main_quit') );
  
    $centerHBox = new GtkHBox();
    $centerVBox = new GtkVBox();
    $leftBox = new GtkHBox();
    $rightBox = new GtkHBox();
    $topBox = new GtkHBox();
    $bottomBox = new GtkHBox();

    $insideTopBox = new GtkVBox();
    $this->topLabel = new GtkLabel(); 
    $this->topLabel->set_use_markup( true ); 
    $this->topLabel->set_label( $this->strings['topLabel'] );
    $insideTopBox->pack_start( $this->topLabel );

    if ( file_exists( '/etc/libki/logo.png' ) ) {
		$logo = GtkImage::new_from_file('/etc/libki/logo.png');
		$insideTopBox->pack_start( $logo );
    } elseif ( file_exists( 'logo.png' ) ) {
		$logo = GtkImage::new_from_file('logo.png');
		$insideTopBox->pack_start( $logo );
    }
    $topBox->pack_start( $insideTopBox );
  
    $centerHBox->pack_start( $leftBox, true );
    $centerHBox->pack_start( $this->loginBox, false );
    $centerHBox->pack_start( $this->errorBox, false );
    $centerHBox->pack_start( $rightBox, true );
    $centerVBox->pack_start( $topBox, true );
    $centerVBox->pack_start( $centerHBox, false );
    $centerVBox->pack_start( $bottomBox, true );
  
    $this->add( $centerVBox );
    
  }
  
  public function hideLogin() {
    $this->hide_all();
  }
  
  public function showLoginBox() {
    $this->loginBox->show_all();  
  }

  public function hideLoginBox() {
    $this->loginBox->hide_all();    
  }
  
  public function hideErrorBox() {
    $this->errorBox->hide_all();  
  }
  
  public function showErrorBox( $message ) {
    $this->errorLabel->set_label( $message );
    $this->errorBox->show_all();  
  }
  
  public function getUsername() {
    return $this->usernameEntry->get_text();
  }
  
  public function getPassword() {
    return $this->passwordEntry->get_text();
  }
  
  public function isPaused() {
    return $this->status == "Paused";
  }

  public function unPause() {
    return $this->status = null;
  }  
}
?>
