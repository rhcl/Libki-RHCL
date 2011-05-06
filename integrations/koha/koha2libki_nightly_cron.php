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

define( 'DEBUG', 1 );
                                        
$libki = parse_ini_file( "/etc/libki/libki.ini", $process_sections=true ) or die( "Unable to parse /etc/libki/libki.ini" );
$koha = parse_ini_file( "/etc/koha.conf" );

## Open the Koha database
$kdbh = mysql_connect( $koha["hostname"], $koha["user"], $koha["pass"] ) or die("Unable to connect to Koha MySQL Server" );
mysql_select_db( $koha["database"], $kdbh ) or die( "Unable to open Koha database." );

##### Get Data From Koha #####

## Get list of expired accounts
$query = "SELECT cardnumber FROM borrowers WHERE DATE(expiry) < DATE( NOW() )";
if ( DEBUG ) echo "\n$query ";
$results = mysql_query( $query, $kdbh ) or die( mysql_error() );
$expired_cardnumbers = array();
while ( $result = mysql_fetch_assoc( $results ) ) {
  $expired_cardnumbers[] = $result['cardnumber'];
}

## get list of flagged accounts
$query = "SELECT cardnumber FROM borrowers WHERE ( gonenoaddress = 1 OR lost = 1 OR debarred = 1 )";
if ( DEBUG ) echo "\n$query ";
$results = mysql_query( $query, $kdbh ) or die( mysql_error() );
$flagged_cardnumbers = array();
while ( $result = mysql_fetch_assoc( $results ) ) {
  $flagged_cardnumbers[] = $result['cardnumber'];
}

## get list of accounts with long overdues
$query = "SELECT cardnumber
FROM borrowers, issues
WHERE issues.borrowernumber = borrowers.borrowernumber
AND returndate IS NULL
AND DATEDIFF( NOW() , DATE(issues.date_due) ) > 100
GROUP BY cardnumber";
if ( DEBUG ) echo "\n$query ";
$results = mysql_query( $query, $kdbh ) or die( mysql_error() );
$longoverdue_cardnumbers = array();
while ( $result = mysql_fetch_assoc( $results ) ) {
  $longoverdue_cardnumbers[] = $result['cardnumber'];
}

##### Update Libki Servers #####
foreach ( $libki as $ini ) {
  if ( DEBUG ) echo "\nUpdating Libki host " . $ini["host"];
  ## Open the LibKi database
  $ldbh = mysql_connect( $ini["host"], $ini["username"], $ini["password"] ) or die( "Unable to connect to Libki MySQL Server" );
  mysql_select_db( $ini["database"], $ldbh ) or die( "\nUnable to open Libki database: " . mysql_error() . "\n\n" );
  
  ## Disable expired accounts
  foreach( $expired_cardnumbers as $cardnumber ) {
  if ( DEBUG ) echo "\n$cardnumber has expired";
    $query = "UPDATE logins 
              SET 
                status = 'Kicked', 
                notes = 'Library card has expired.', 
                troublemaker = '1' 
              WHERE username = '$cardnumber'";
    mysql_query( $query, $ldbh );
  }
  
  ## Disable flagged accounts
  foreach( $flagged_cardnumbers as $cardnumber ) {
  if ( DEBUG ) echo "\n$cardnumber is flagged";
    $query = "UPDATE logins 
              SET 
                status = 'Kicked', 
                notes = 'Library card has no address, is lost, or is debarred.', 
                troublemaker = '1' 
              WHERE username = '$cardnumber'";
    mysql_query( $query, $ldbh );
  }
  
  ## Disable accounts with long overdues
  foreach( $longoverdue_cardnumbers as $cardnumber ) {
  if ( DEBUG ) echo "\n$cardnumber has long overdues";
    $query = "UPDATE logins 
              SET 
                status = 'Kicked', 
                notes = 'Library card has long overdues.', 
                troublemaker = '1' 
              WHERE username = '$cardnumber'";
    mysql_query( $query, $ldbh );
  }

  mysql_close( $ldbh );  
}

?>