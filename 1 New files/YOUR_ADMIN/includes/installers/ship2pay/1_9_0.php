<?php
/*
 * change to text imstead of varchar
 */

zen_register_admin_page('configShip2pay', 'BOX_CONFIGURATION_SHIP2PAY', 'FILENAME_SHIP2PAY', 'gID=' . $configuration_group_id, 'configuration', 'N', $configuration_group_id);
$db->Execute("ALTER TABLE " . TABLE_SHIP2PAY . " CHANGE payments_allowed payments_allowed TEXT CHARACTER SET " . DB_CHARSET . " NOT NULL;");