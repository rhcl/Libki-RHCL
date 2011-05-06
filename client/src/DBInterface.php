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

class DBInterface {
    private $ini;
    
    private $dbc;

    private $userData;
    
    public function __construct() {
      if ( DEBUG ) echo "DBInterface::__construct()\n";
      if ( ! $this->ini = parse_ini_file( "/etc/libki/libki.ini" ) ) { # Try stanard unix path
        if ( ! $this->ini = parse_ini_file( "C:\\etc\\libki\\libki.ini" ) ) { # Try absolute windows path
          if ( ! $this->ini = parse_ini_file( "libki.ini" ) ) { # Last resort, look in the working directory
	    die("Could not read libki.ini");
          }
        }
      }

      $this->host = $this->ini['host'];
      $this->database = $this->ini['database'];
      $this->username = $this->ini['username'];
      $this->password = $this->ini['password'];
      
      $success = $this->dbc = mysql_connect( $this->host, $this->username, $this->password );

      if ( DEBUG && $success ) {
        echo "Connected to database.\n";
      } elseif ( DEBUG ) {
        echo "Unable to connect to database.\n";
      }

    }
    
    public function registerClient() {
      $machine_name = $this->getMachine();
      if ( DEBUG ) echo "Registering client machine with name '$machine_NAME'";
      $machine_name = mysql_real_escape_string( $machine_name );
      $query = "INSERT INTO clients ( machine_name ) VALUES ( '$machine_name' )";
      $success = mysql_db_query( $this->database, $query, $this->dbc );    
      if ( ! $success ) { # Client isn't registered
        $query = "UPDATE clients SET last_updated = CURRENT_TIMESTAMP WHERE machine_name = '$machine_name' ";
        mysql_db_query( $this->database, $query, $this->dbc );
      }
    
    }
	
    public function getUnits() {
      if ( DEBUG ) echo "DBInterface::getUnits() returned " . $this->userData['units'] . "\n";
      return $this->userData['units'];
    }    

    public function verifyLogin( $username = null, $password = null ) {
      if ( DEBUG ) echo "DBInterface::verifyLogin( '$username', '$password' )\n";
      $success = ( $username && $password 
                && $this->fetchUserData( $username ) 
                && $this->userData['password'] == $this->md5_base64( $password  )
                && $this->setLoggedIn()
              );
      return $success;
    }

    public function verifyUnpause( $username = null, $password = null ) {
      if ( DEBUG ) echo "DBInterface::verifyUnpause( '$username', '$password' )\n";
      return ( $username && $password 
                && $username == $this->userData['username']
                && $this->md5_base64( $password ) == $this->userData['password']
              );
    }
    
    public function isLoggedIn( $username = null ) {
      if ( DEBUG ) echo "DBInterface::isLoggedIn( '$username' )\n";
      if ( $username ) {
        $this->fetchUserData( $username, $noUpdate = true );
      }
      $ret = $this->userData['status'] == 'Logged in';
      echo "Status of $username is " . $this->userData['status'] . "\n";
      if ( DEBUG ) echo "DBInterface::isLoggedIn( '$username' ) Returns '$ret'\n";
      return $ret;
    }
    
    public function fetchUserData( $username, $noUpdate = false ) {
      if ( DEBUG ) echo "DBInterface::fetchUserData( '$username' )\n";
      $query = "SELECT * FROM logins WHERE username = '$username'";
      $result = mysql_db_query( $this->database, $query, $this->dbc );
      if ( $result && !$noUpdate ) {
        mysql_db_query( $this->database, "UPDATE logins SET last_accessed = CURRENT_TIMESTAMP WHERE username = '$username' ", $this->dbc );
      }
      return $this->userData = mysql_fetch_assoc( $result );
    }

    public function fetchClientCommand() {
      $machine_name = $this->getMachine();
	  echo "Machine Name: $machine_name\n";
      $query = "SELECT command FROM clients WHERE machine_name = '$machine_name'";
      $result = mysql_db_query( $this->database, $query, $this->dbc );
      $client_data = mysql_fetch_assoc( $result );
	  $command = $client_data['command'];
	  echo "DBInterface::fetchClientCommand() = '$command' \n";
	  return $command;
    }

    public function clearClientCommand() {
      $machine_name = $this->getMachine();
      $query = "UPDATE clients SET command = NULL WHERE machine_name = '$machine_name'";
      $success = mysql_db_query( $this->database, $query, $this->dbc );
	  return $success;
    }    
	
    public function updateUserData() {
      if ( DEBUG ) echo "DBInterface::updateUserData()\n";
      return $this->fetchUserData( $this->userData['username'] );
    }
    
    public function setLoggedIn() {
      if ( DEBUG ) echo "DBInterface::setLoggedIn()\n";
      return $this->setMachine() && $this->setStatus( 'Logged in' );
    }

    public function setLoggedOut() {
      if ( DEBUG ) echo "DBInterface::setLoggedOut()\n";
      return $this->setStatus( 'Logged out' ) && $this->clearMachine() && $this->userData = null;
    }
    
    public function setKicked() {
      if ( DEBUG ) echo "DBInterface::setKicked()\n";
      return $this->setStatus( 'Kicked' ) && $this->clearMachine() && $this->userData = null;
    }

    public function setPaused() {
      if ( DEBUG ) echo "DBInterface::setPaused()\n";
      return $this->setStatus( 'Paused' );
    }
    
    public function isKicked( $username = null ) {
      if ( DEBUG ) echo "DBInterface::isKicked( '$username' )\n";
      if ( $username ) {
        $this->fetchUserData( $username );
      }
      return $this->userData['status'] == 'Kicked';
    }

    public function needsPassword( $username = null ) {
      if ( DEBUG ) echo "DBInterface::needsPassword( '$username' )\n";
      if ( $username ) {
        $this->fetchUserData( $username );
      }
      return empty( $this->userData['password'] ) && $this->userData['username'];
    }

    public function getMessage() {
      if ( DEBUG ) echo "DBInterface::getMessage()\n";
      if ( $message = $this->userData['message'] ) {
        $query = "UPDATE logins SET message = '' WHERE username = '" . $this->userData['username'] . "'";
        mysql_db_query( $this->database, $query, $this->dbc );    
      }
      return $message;
    }
    
    public function createStat( $status, $username = null, $machine = null ) {
      if ( DEBUG ) echo "DBInterface::createStat( '$status', '$username', '$machine' )\n";
      if ( ! $username ) {
        $username = $this->userData['username'];
      }
      
      if ( ! $machine ) {
        $machine = $this->getMachine();
      }
      
      $query = "INSERT INTO statistics ( username, machine, status, time ) VALUES ( '$username', '$machine', '$status', NOW() )";
      $result = mysql_db_query( $this->database, $query, $this->dbc );
      
      if ( DEBUG ) echo "DBInterface::createStat( '$status', '$username', '$machine' ) returned $result\n";
      
      return $result;
    }
    
    public function getSetting( $name ) {
      $query = "SELECT value FROM settings WHERE name = '$name'";
      $result = mysql_db_query( $this->database, $query, $this->dbc );
      $row = mysql_fetch_assoc( $result );
      $value = $row['value'];
      return $value;
    }

    public function setPassword( $username, $password ) {
      if ( DEBUG ) echo "DBInterface::setPassword( '$username', '$password' )\n";
      
      $password = $this->md5_base64( $password );
      
      $query = "UPDATE logins SET password = '$password' WHERE username = '$username'";
      $result = mysql_db_query( $this->database, $query, $this->dbc );

      return $result;
    }


    private function setStatus( $status ) {
      if ( DEBUG ) echo "DBInterface::setStatus( '$status' )\n";
      $this->createStat( $status );
      $query = "UPDATE logins SET status = '$status' WHERE username = '" . $this->userData['username'] . "'";
      $result = mysql_db_query( $this->database, $query, $this->dbc );

      if ( DEBUG ) echo "DBInterface::setStatus( '$status' ) returned $result\n";
      
      return $result;
    }
    
    private function getMachine() {
      if ( $this->ini['machine'] ) {
        $this->userData['machine'] = $this->ini['machine'];
        return $this->ini['machine'];
      } elseif ( $_SERVER['USER'] ) {
        $this->userData['machine'] = $_SERVER['USER'];
        return $_SERVER['USER'];
      } elseif ( $this->userData['machine'] ) {
        return $this->userData['machine'];
      } else {
        die('Could not get Machine Name. Please define machine in libki.ini or enable $_SERVER for PHP');
      }
    }
    
    private function setMachine( $machine = null ) {
      if ( DEBUG ) echo "DBInterface::setMachine( '$machine' )\n";
      if ( ! $machine ) {
        $machine = $this->getMachine();
      }
      $query = "UPDATE logins SET machine = '$machine' WHERE username = '" . $this->userData['username'] . "'";
      if ( DEBUG ) echo "$query\n";
      $result = mysql_db_query( $this->database, $query, $this->dbc );
      
      if ( DEBUG ) echo "DBInterface::setMachine returned $result\n";
      return $result;
    }

    private function clearMachine() {
      if ( DEBUG ) echo "DBInterface::clearMachine( '$machine' )\n";
      $query = "UPDATE logins SET machine = NULL WHERE username = '" . $this->userData['username'] . "'";
      if ( DEBUG ) echo "$query\n";
      $result = mysql_db_query( $this->database, $query, $this->dbc );
      if ( DEBUG ) echo "DBInterface::setMachine returned $result\n";
      return $result;
    }
    
    ## Replicates the Perl function CPAN Digest::MD5    
    private function md5_base64 ( $data ) {
        return preg_replace( '/=+$/', '', base64_encode( pack( 'H*', md5( $data ) ) ) );
    }
}
?>