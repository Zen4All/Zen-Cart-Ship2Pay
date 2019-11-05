DROP TABLE ship2pay;

DELETE FROM admin_pages WHERE admin_pages.page_key = 'ship2pay';
DELETE FROM admin_pages WHERE admin_pages.page_key = 'configShip2pay';
DELETE FROM configuration_group WHERE configuration_group.configuration_group_title = 'Ship2Pay';