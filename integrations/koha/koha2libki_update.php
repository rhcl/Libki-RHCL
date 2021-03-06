#!/usr/bin/php
<?php
# This file is part of Libki.
#
# Libki is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#                
# Libki is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#                                
# You should have received a copy of the GNU General Public License
# along with Libki.  If not, see <http://www.gnu.org/licenses/>.
                                        
## Get borrowernumber from arguments
$borrowernumber = $argv[1];
if ( empty( $borrowernumber ) ) {
  die ( "No borrowernumber given\n" );
}

## To prevent Koha's call to this script from timing out,
## fork a child and let the parent return;
$pid = pcntl_fork();
if ( $pid == -1 ) {
  die('Could Not Fork');
} elseif ( $pid ) {
    // we are the parent
    pcntl_wait($status); //Protect against Zombie children
    exit();
} else {
    // We are the child

    $libki = parse_ini_file( "/etc/libki/libki.ini", $process_sections=true ) or die( "Unable to parse /etc/libki/libki.ini" );
    $koha = parse_ini_file( "/etc/libki/koha.ini" );

    ## Open the Koha database
    $kdbh = mysql_connect( $koha["host"], $koha["username"], $koha["pass"] ) or die("Unable to connect to Koha MySQL Server" );
    mysql_select_db( $koha["database"], $kdbh ) or die( "Unable to open Koha database." );

    ## Grab the userid and password of the user based on the borrowernumber
    $query = "SELECT userid, password FROM borrowers WHERE borrowernumber = '$borrowernumber'";
    $results = mysql_query( $query, $kdbh );
    $result = mysql_fetch_assoc( $results );
    $userid = $result['userid'];
    $password = $result['password'];
    
    foreach ( $libki as $ini ) {
      error_log( "Working on : " . print_r( $ini, 1 ) );
      ## Open the LibKi database
      $ldbh = mysql_connect( $ini["host"], $ini["username"], $ini["password"] ) or die( "Unable to connect to Libki MySQL Server" );
      mysql_select_db( $ini["database"], $ldbh ) or die( "Unable to open Libki database." );
    
      ## Get default starting minutes from database
      $query = "SELECT value FROM settings WHERE name = 'daily_minutes'";
      $results = mysql_query( $query, $ldbh );
      $result = mysql_fetch_assoc( $results );
      $daily_minutes = $result['value'];
  
      ## Check to see if this user is already in libki
      $query = "SELECT COUNT(*) AS userExists FROM logins WHERE username = '$userid'";
      $results = mysql_query( $query, $ldbh );
      $result = mysql_fetch_assoc( $results );
      $userExists = $result['userExists'];

      ## If the user doesn't already exist, create the user in libki
      if ( ! $userExists ) { 
        $query = "INSERT INTO logins ( username, units, status, password ) VALUES ( '$userid', '$daily_minutes', 'Logged out', '$password' )";
        mysql_query( $query, $ldbh ) or die( "Update Failed\n" );;
      } else { ## Copy over the koha password
        $query = "UPDATE logins SET password = '$password' WHERE username = '$userid'";
        mysql_query( $query, $ldbh ) or die( "Update Failed\n" );
      }

      mysql_close( $ldbh );
    }
    exit();
}


?>
