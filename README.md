# Zen-Cart-Ship2Pay

Ship2Pay,v 1.8 2016-07-27 Zen4All/Design75
Support thread: https://www.zen-cart.com/showthread.php?213177-Ship2Pay-Support-thread

* based on :
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 Edwin Bekaert (edwin@ednique.com)

  Released under the GNU General Public License

* modified to zen cart distribution by wladimirec

  tested on: Zen Cart Version v1.3.8

  fixed: undefined function calling in:<br>
    /admin/includes/classes/shipping.php<br>
    /admin/includes/classes/payment.php

* V 1.5.2 - 2012-11-01 fdeboer
  tested on: Zen Cart Version 1.3.9h

  added: dutch language definitions<br>
  fixed: obsolete $HTTP_GET/POST_VARS with $_GET/_POST<br>
  fixed: admin button image links<br>
  modified: moved language file to extra_definitions folder and merged manual language definitions from install into the language file.

* V 1.6- 2014-05-02 Zen4All/Design75
  tested on: Zen Cart 1.5.x

  added: datafiles for database<br>
  added: datafiles for filenames<br>
  removed: unused variables $language, $PHP_SELF,$shipment,$GLOBALS from /includes/classes/ship2pay.php<br>
  removed:<br>
    admin/image/icons/icon_status_green_light.gif It was not used in this module. instead the default present<br>
    admin/image/icon_status_green_light.gif is used)<br>
  renamed:<br>
    /admin/includes/classes/shipping.php to /admin/includes/classes/ship2pay_shipping.php<br>
    /admin/includes/classes/payment.php to /admin/includes/classes/ship2pay_payment.php<br>
    this way it is easier to spot module specific files<br>
  updated: admin/ship2pay.php code cleaning so it is up to Zen Cart 1.5.1 standard<br>
  updated: install.sql A statement was added so a link is added to the Modules menu<br>
  updated: installation instructions

* V 1.7- 2014-06-24 Zen4All/Design75
  tested on: Zen Cart 1.5.x
  
  Admin language files have been updated, to prevent the module title form showing up in other locations then this module<br>
  changed: YOUR_ADMIN/includes/languages/YOUR_LANGUAGE/extra_definitions/ship2pay.php<br>
  added: YOUR_ADMIN/includes/languages/YOUR_LANGUAGE/extra_definitions/ship2pay.php

* V 1.8- 2016-07-27 Zen4All/Design75
  tested on: Zen Cart 1.5.x

  Updated files to be compatible with php 7.0<br>
  Removed Polish language files, because I am not able to provide support for them.<br>
  Updated control panel file<br>
  Added Zen Cart 1.5.5a overwrite file for "includes/classes/payment.php"

------------------------------------------

## SUMMARY:

Limit the number of payment options depending on the chosen shipping method.
For example When a customer chooses pickup at store he should be able to select cash on pickup or money-order and when he chooses flat rate shipping he should be able to select cash on delivery or moneyorder.

After installation, go to the admin > modules and click on ship 2 pay
insert your links between shipping and payment.
If you don't specify any link, the code will work as before.



      
## INSTALLATION:

1. Unzip to a folder of your choice.
2. Rename the YOUR_ADMIN folder to the name of your admin folder
3. Copy the files to your server.
  * file-list:
    - /YOUR_ADMIN/ship2pay.php
    - /YOUR_ADMIN/includes/classes/ship2pay_payment.php
    - /YOUR_ADMIN/includes/classes/ship2pay_shipping.php
    - /YOUR_ADMIN/includes/extra_datafiles/ship2pay_database_names.php
    - /YOUR_ADMIN/includes/extra_datafiles/ship2pay_filenames.php
    - /YOUR_ADMIN/includes/languages/english/extra_definitions/ship2pay.php
    - /YOUR_ADMIN/includes/languages/dutch/extra_definitions/ship2pay.php
    - /includes/classes/ship2pay.php
    - /includes/extra_datafiles/ship2pay_database_names.php

  * Modify file:<br>
Open the file /includes/classes/payment.php<br>
When using Zen Cart 1.5.5a You can skip this step and use the file provided in the folder "Core file overrides"<br>
Find the following line: (about line 28)<br>
  ```php
  $this->modules = explode(';', MODULE_PAYMENT_INSTALLED);
  ```
  <br>
    replace that single line with:<br>
  ```php
  //BOF Ship2pay
  //  $this->modules = explode(';', MODULE_PAYMENT_INSTALLED);
  require (DIR_WS_CLASSES . 'ship2pay.php');
  $my_ship2pay = new ship2pay;
  $arrship = explode('_',$_SESSION['shipping']['id']);
  $ship2pay_mods = $my_ship2pay->get_pay_modules($arrship[0]);
  if (zen_not_null($ship2pay_mods)) {
    $this->modules = explode(';', $ship2pay_mods);
  } else {
    $this->modules = explode(';', MODULE_PAYMENT_INSTALLED);
  }
  //EOF ship2pay
  ```
      
4. Use phpmyadmin, or the built-in sql patch page to update you Database with the contents of install.sql
5. Go to the admin -> modules -> ship2pay and setup your ship2pay settings

## UN-INSTALLATION:

Reverse the installation steps, and use the uninstall.sql
