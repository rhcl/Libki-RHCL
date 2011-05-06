<?php

echo "Creating Libki Database\n";

if ( ! $ini = parse_ini_file( "/etc/libki/libki.ini" ) ) {
    die("Could not read /etc/libki/libki.ini");     
}

$host = $ini['host'];
$database = $ini['database'];
$username = $ini['username'];
$password = $ini['password'];

$dbh = mysql_connect( $host, $username, $password );

if ( ! $dbh ) {
  die("Unable to connect to database.");
}

## Create the database
mysql_query("CREATE DATABASE $database") or die(mysql_error()."\n");

## Build the structure
$sql = file_get_contents('libki_structure.sql');
mysql_db_query( $database, $sql ) or die(mysql_error()."\n");

## Add the data
$sql = file_get_contents('libki_data.sql');
mysql_db_query( $database, $sql ) or die(mysql_error()."\n");

?>