<?php

class OfferController extends AppController {

  function beforeFilter() {
    $this->checkSession();
  }
  
  function index() {
  
  }

}

?>