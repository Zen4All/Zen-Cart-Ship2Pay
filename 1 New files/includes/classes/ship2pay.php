<?php

/*
 * $Id: Ship2Pay, v1.8 11:05 2016-05-11 Zen4All $
 */

// Class to handle links between shipping and payment

class ship2pay {

  var $modules;

  function __construct()
  {
    global $db;
    $this->modules = array();
    $modules = $db->Execute("SELECT shipment, payments_allowed
                             FROM " . TABLE_SHIP2PAY . "
                             WHERE status = 1");
    foreach ($modules as $module) {
      $this->modules[$module['shipment']] = $module['payments_allowed'];
    }
  }

  function get_pay_modules($ship_module)
  {
    return $this->modules[$ship_module];
  }

}
