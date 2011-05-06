<?php
class LibkiHelper extends Helper {

  ## RoundTimeUp rounds a timestamp to the next specified increment 
  ## It only works for increments less than or equal to 1 hour
  ## Example: RoundTimeUp("15 Minutes") rounds the time right now to the next 15 minutes 11:12 rounds to 11:15
  public function RoundTimeUp($increment, $timestamp=0) {
    if(!$timestamp) $timestamp = time();
     
    $increment = strtotime($increment, 1) - 1;
    $this_hour = strtotime(date("Y-m-d H:", strtotime("-1 Hour", $timestamp))."00:00");
    $next_hour = strtotime(date("Y-m-d H:", strtotime("+1 Hour", $timestamp))."00:00");

    $increments = array();
    $differences = array();

    for($i = $this_hour; $i <= $next_hour; $i += $increment) {
        if($i > $timestamp) return $i;
    }
  }
}
?>