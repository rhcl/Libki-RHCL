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

##### Get Data From Koha #####

##### Update Libki Servers #####
foreach ( $libki as $ini ) {
  $ldbh = mysql_connect( $ini["host"], $ini["username"], $ini["password"] ) or die( "Unable to connect to Libki MySQL Server" );
  mysql_select_db( $ini["database"], $ldbh ) or die( "\nUnable to open Libki database: " . mysql_error() . "\n\n" );

  ## Get list of accounts on the libki server
  $lquery = "SELECT username FROM logins";
  $lresults = mysql_query( $lquery, $ldbh ) or die( mysql_error() );
  $old_accounts = array();
  while ( $lresult = mysql_fetch_assoc( $lresults ) ) {
    $cardnumber = $lresult['cardnumber'];
    
    $kquery = "SELECT COUNT(*) AS in_db FROM borrowers WHERE cardnumber = '$cardnumber'";
    $kresults = mysql_query( $kquery, $kdbh ) or die( mysql_error() );
    $kresult = mysql_fetch_assoc( $kresults );
    $in_db = $kresult['in_db'];
    if ( ! $in_db ) {
      $old_accounts[] = $cardnumber;
    }
  }

  ## Delete accounts no found in koha
  foreach ( $old_accounts as $old_account ) {
    $lquery = "DELETE FROM logins WHERE username LIKE '$old_account' ";
    mysql_query( $lquery, $ldbh );
  }
  
  mysql_close( $ldbh );  
}

mysql_close( $kdbh );
?>