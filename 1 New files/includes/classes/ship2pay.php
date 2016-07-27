<?php

/*
 * $Id: Ship2Pay, v1.8 11:05 2016-05-11 Zen4All $
 */

// Class to handle links between shipping and payment

class ship2pay {

  var $modules;

  function __construct() {
    global $db;
    $this->modules = array();
    $mods = $db->Execute("SELECT shipment, payments_allowed FROM " . TABLE_SHIP2PAY . " WHERE status = 1");
    while (!$mods->EOF) {
      $this->modules[$mods->fields['shipment']] = $mods->fields['payments_allowed'];

      $mods->MoveNext();
    }
  }

  function get_pay_modules($ship_module) {
    return $this->modules[$ship_module];
  }

}
