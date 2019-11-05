<?php

/*
 * $Id: Ship2Pay, v1.8 11:05 2016-05-11 Zen4All $
 */

class payment {

  var $modules;

  function __construct()
  {

    if (defined('MODULE_PAYMENT_INSTALLED') && zen_not_null(MODULE_PAYMENT_INSTALLED)) {
      $allmods = explode(';', MODULE_PAYMENT_INSTALLED);

      $this->modules = [];

      for ($i = 0, $n = sizeof($allmods); $i < $n; $i++) {
        $file = $allmods[$i];
        $class = substr($file, 0, strrpos($file, '.'));
        $this->modules[$i]['class'] = $class;
        $this->modules[$i]['file'] = $file;
      }
    }
  }

  function get_modules()
  {
    return $this->modules;
  }

  function payment_multiselect($parameters, $selected = '')
  {
    $arrSelected = explode(';', $selected);
    $select_string = '<select ' . $parameters . ' multiple size="10">';
    for ($i = 0, $n = sizeof($this->modules); $i < $n; $i++) {
      $sFile = $this->modules[$i]['file'];
      $sClass = $this->modules[$i]['class'];
      $select_string .= '<option value="' . $sFile . '"';
      if (in_array($sFile, $arrSelected)) {
        $select_string .= ' selected';
      }
      $select_string .= '>' . $sClass . '</option>';
    }
    $select_string .= '</select>';
    return $select_string;
  }

  function GetModuleName($sSelected)
  {
    $sNames1 = str_replace(".php", "", $sSelected);
    $sNames = str_replace(";", ",&nbsp;", $sNames1);
    return $sNames;
  }

}
