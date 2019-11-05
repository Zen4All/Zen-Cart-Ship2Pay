<?php

/*
 * 
 */

$db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_SHIP2PAY . " (
  s2p_id int(11) NOT NULL AUTO_INCREMENT,
  shipment varchar(100) NOT NULL,
  payments_allowed text NOT NULL,
  status tinyint(4) NOT NULL,
  PRIMARY KEY (s2p_id)
) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET . ";");

$check = $db->Execute("SELECT page_key
                       FROM " . TABLE_ADMIN_PAGES . "
                       WHERE pagekey = 'ship2pay'");
if ($check->RecordCount() == 0) {
  zen_register_admin_page('ship2pay', 'BOX_MODULES_SHIP2PAY', 'FILENAME_SHIP2PAY', '', 'modules', 'Y', 4);
}