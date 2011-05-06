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

$libki = parse_ini_file( "/etc/libki/libki.ini", $process_sections=true ) or die( "Unable to parse /etc/libki/libki.ini" );
$koha = parse_ini_file( "/etc/koha.conf" );

## Open the Koha database
$kdbh = mysql_connect( $koha["hostname"], $koha["user"], $koha["pass"] ) or die("Unable to connect to Koha MySQL Server" );
mysql_select_db( $koha["database"], $kdbh ) or die( "Unable to open Koha database." );

foreach ( $libki as $ini ) {
  ## Open the LibKi database
  $ldbh = mysql_connect( $ini["host"], $ini["username"], $ini["password"] ) or die( "Unable to connect to Libki MySQL Server" );
  mysql_select_db( $ini["database"], $ldbh ) or die( "Unable to open Libki database." );

  ## Get the daily minutes allotted for each user
  $query = "SELECT value FROM settings WHERE name = 'daily_minutes'";
  $result = mysql_query( $query, $ldbh );
  $row = mysql_fetch_assoc( $result );
  $daily_minutes = $row['value'];

  ## Delete all non-admin users from the libki db
  $query = "DELETE FROM logins WHERE admin = FALSE";
  mysql_query( $query, $ldbh ) or die( "Delete Failed\n");

  ## Grab all the cardnumbers and passwords from the Koha db
  ## Create libki users based on the data
  $query = "SELECT cardnumber, password FROM borrowers";
  $results = mysql_query( $query, $kdbh );

  while ( $result = mysql_fetch_assoc( $results ) ) {
    $cardnumber = mysql_real_escape_string( $result['cardnumber'], $ldbh );
    $password = mysql_real_escape_string( $result['password'], $ldbh );
  
    echo "Adding cardnumber $cardnumber\n";
  
    $query = "INSERT INTO logins ( username, units, status, password ) VALUES ( '$cardnumber', '$daily_minutes', 'Logged out', '$password' )";
    mysql_query( $query, $ldbh ) or die( "Update Failed\n" );
  }
}
?>