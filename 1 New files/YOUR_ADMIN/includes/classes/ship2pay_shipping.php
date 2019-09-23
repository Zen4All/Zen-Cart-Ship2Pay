<?php

/*
 * $Id: Ship2Pay, v1.8 11:05 2016-05-11 Zen4All $
 */

class shipping {

  var $modules;

  function __construct()
  {

    if (defined('MODULE_SHIPPING_INSTALLED') && zen_not_null(MODULE_SHIPPING_INSTALLED)) {
      $allmods = explode(';', MODULE_SHIPPING_INSTALLED);

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

  function shipping_select($parameters, $selected = '')
  {
    echo $selected;
    $select_string = '<select ' . $parameters . '>';
    for ($i = 0, $n = sizeof($this->modules); $i < $n; $i++) {
      $select_string .= '<option value="' . $this->modules[$i]['class'] . '"';
      if ($selected == $this->modules[$i]['class']) {
        $select_string .= ' SELECTED';
      }
      $select_string .= '>' . $this->modules[$i]['class'] . '</option>';
    }
    $select_string .= '</select>';
    return $select_string;
  }
}
