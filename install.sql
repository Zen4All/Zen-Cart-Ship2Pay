CREATE IF NOT EXISTS TABLE `ship2pay` (
`s2p_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`shipment` VARCHAR( 100 ) NOT NULL ,
`payments_allowed` VARCHAR( 250 ) NOT NULL ,
`status` TINYINT NOT NULL
);
INSERT INTO `admin_pages` (`page_key`, `language_key`, `main_page`, `page_params`, `menu_key`, `display_on_menu`, `sort_order`) VALUES
('ship2pay', 'BOX_MODULES_SHIP2PAY', 'FILENAME_SHIP2PAY', '', 'modules', 'Y', 4);
