<?php

class Client extends AppModel {
  var $name = 'Client';

  function getClientStates () {
      return $this->query("SELECT * FROM clients LEFT JOIN logins ON clients.machine_name = logins.machine ORDER BY machine_name");
  }
  
  function reboot( $machine_name ) {
    return $this->query("UPDATE clients SET command = 'reboot' WHERE machine_name = '$machine_name'");
  }
}
?>