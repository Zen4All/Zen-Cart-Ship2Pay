<?php

/*
  $Id: Ship2Pay, v1.5 17:18 2008-06-27 gjw Exp $
  modified by wladimirec

 */

////
// Class to handle links between shipping and payment

class ship2pay {

  var $modules;

// class constructor
  function ship2pay() {
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
