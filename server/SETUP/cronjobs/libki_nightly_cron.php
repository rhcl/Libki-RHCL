#!/usr/bin/php
<?php
$ini = parse_ini_file("/etc/libki/libki.ini");
$dbh = mysql_connect( $ini["host"], $ini["username"], $ini["password"] ) or die( "Unable to connection to MySQL Server" );
mysql_select_db( $ini["database"] ) or die( "Unable to open Libki database." );

## Get the daily minutes allotted for each user
$query = "SELECT value FROM settings WHERE name = 'daily_minutes'";
$result = mysql_query( $query );
$row = mysql_fetch_assoc( $result );
$daily_minutes = $row['value'];

## Clear out all guest accounts
$query = "DELETE FROM logins WHERE username LIKE 'guest%'";
mysql_query( $query ) or die(mysql_error());

## Reset starting guest id
$query = "UPDATE settings SET value = '1' WHERE name = 'next_guest_id'";
mysql_query( $query ) or die(mysql_error());

## Reset units for all accounts
$query = "UPDATE logins SET units = '$daily_minutes'";
mysql_query( $query ) or die(mysql_error());

## Make accounts marked as troublemaker impossible to log into
$query = "UPDATE logins SET password = '*' WHERE troublemaker = 1";
mysql_query( $query ) or die(mysql_error());

## Write to log
$myFile = "/var/log/libki/libki.log";
$fh = fopen($myFile, 'a') or die("can't open file");
$stringData = date('l jS \of F Y h:i:s A') . ": Executed nightly cronjob\n";
fwrite($fh, $stringData);
fclose($fh);
?>