<?php

echo "Updating Libki Database\n";
$dbh = GetDBH();

$version = GetVersion();
echo "Libki Database is currently at version $version\n";

$DBversion = '0.00.00.01';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query('RENAME TABLE libki.login TO libki.logins') or error_log(mysql_error()."\n");
  echo "Renamed table login to logins.\n";
  mysql_query('CREATE TABLE clients (
               machine_name varchar(255) NOT NULL,
               last_updated timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
               PRIMARY KEY  (machine_name)
               )'
  ) or error_log(mysql_error());
  echo "Created clients table.\n";
  SetVersion( $DBversion );
}

$DBversion = '0.00.00.02';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query("INSERT INTO libki.settings ( id, name, value, description )
               VALUES ( NULL , 'daily_minutes', '30', 'The number of minutes each user will start with each day.' )
  ") or error_log(mysql_error());
  echo "Created clients table.\n";
  SetVersion( $DBversion );
}

$DBversion = '0.10.00.03';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query("ALTER TABLE `logins` ADD `troublemaker` TINYINT( 1 ) NOT NULL DEFAULT '0';") or error_log(mysql_error());
  echo "Added field 'troublemaker' to logins table.\n";
  SetVersion( $DBversion );
}

$DBversion = '0.10.00.04';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query("ALTER TABLE `logins` ADD `id` INT( 11 ) NOT NULL PRIMARY KEY AUTO_INCREMENT FIRST ") or error_log(mysql_error());
  echo "Added logins.id as primary key.\n";
  mysql_query("ALTER TABLE `logins` ADD UNIQUE (`username`)") or error_log(mysql_error());
  echo "Changed logins.username to require uniqueness\n";
  SetVersion( $DBversion );
}

$DBversion = '0.20.00.01';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query("ALTER TABLE `logins` CHANGE `machine` `machine` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL") or error_log(mysql_error());
  echo "Updated logins.machine to default to empty string instead of NULL.\n";
  SetVersion( $DBversion );
}

$DBversion = '0.20.00.02';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query(" ALTER TABLE `settings` ADD UNIQUE (`name`) ") or error_log(mysql_error());
  echo "Require settings.name to be unique.\n";
  mysql_query("INSERT INTO `libki`.`settings` (`id`, `name`, `value`, `description`) VALUES (NULL, 'use_koha_integration', '0', 'Turn on Koha integration with Libki');") or error_log(mysql_error());
  echo "Added setting use_koha_integration.\n";
  mysql_query("INSERT INTO `libki`.`settings` (`id`, `name`, `value`, `description`) VALUES (NULL, 'koha_intranet_url', '', 'The address of your Koha intranet server.');") or error_log(mysql_error());
  echo "Added setting koha_intranet_url.\n";
  SetVersion( $DBversion );
}

$DBversion = '0.30.00.00';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query("
    CREATE TABLE `libki`.`reservations` (
    `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
    `machine_name` VARCHAR( 255 ) NOT NULL ,
    `starting_time` DATETIME NOT NULL ,
    `ending_time` DATETIME NOT NULL ,
    PRIMARY KEY ( `id` )
    ) ENGINE = MyISAM DEFAULT CHARSET=utf8;
  ");
  SetVersion( $DBversion );
  echo "Added Reservations Table\n";
}

$DBversion = '0.30.00.01';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query("ALTER TABLE `reservations` ADD `username` VARCHAR( 100 ) NOT NULL AFTER `machine_name` ;");
  SetVersion( $DBversion );
  echo "Updated Reservations Table\n";
}

$DBversion = '0.30.00.02';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query("ALTER TABLE `clients` ADD `require_reservation` BOOL NOT NULL DEFAULT '0';");
  SetVersion( $DBversion );
  echo "Updated Clients Table\n";
}

$DBversion = '0.30.00.03';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query("ALTER TABLE `clients` ADD `category` VARCHAR( 100 ) NULL AFTER `machine_name`");
  SetVersion( $DBversion );
  echo "Updated Clients Table\n";
}

$DBversion = '0.30.00.04';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query("ALTER TABLE `logins` CHANGE `id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT");
  SetVersion( $DBversion );
  echo "Updated Logins Table ( Set id to auto increment )\n";
}

$DBversion = '0.30.00.05';
if ( GetVersion() < TransformToNum($DBversion)) {
  mysql_query(" ALTER TABLE `clients` ADD `command` VARCHAR( 10 ) NULL");
  SetVersion( $DBversion );
  echo "Updated Logins Table ( Set id to auto increment )\n";
}

echo "Finished updating Libki Database\n";
$version = GetVersion();
echo "Libki Database is now at version $version\n";

function TransformToNum( $version ) {
  # remove the 3 last . to have a Perl number
  $parts = explode( '.', $version );
  $primary = $parts[0];
  $secondary = implode( null, array( $parts[1], $parts[2], $parts[3] ) );
  $version = "$primary.$secondary";
  return $version;                   
}               

function SetVersion( $libki_version ) {
  $libki_version = TransformToNum( $libki_version ); 

  $dbh = GetDBH();

  $sql = "REPLACE INTO settings ( name, value, description ) VALUES ( 'version', '$libki_version', 'Current Version of the LibKi Database' )";
  mysql_query( $sql );
}                                                             

function GetVersion() {
  $dbh = GetDBH();
  $result = mysql_query("SELECT * FROM settings WHERE name = 'version'");
  $version = mysql_fetch_assoc( $result );

  if ( ! $version ) {
    $version = '0';
  }
  
  return $version['value'];
}

function GetDBH() {
  if ( ! $ini = parse_ini_file( "/etc/libki/libki.ini" ) ) {
    error_log("Could not read /etc/libki/libki.ini");     
  }

  $host = $ini['host'];
  $database = $ini['database'];
  $username = $ini['username'];
  $password = $ini['password'];

  $DBH = mysql_pconnect( $host, $username, $password );

  if ( ! $DBH ) {
    die("Unable to connect to database.");
  }
  
  mysql_query("use $database");
  
  return $DBH;
}

?>