<?php
/**
 * $Id: Ship2Pay, v1.6 2014-05-02 Zen4All/Design75 $
 */
require('includes/application_top.php');
$action = (isset($_GET['action']) ? $_GET['action'] : '');
require(DIR_WS_CLASSES . 'ship2pay_shipping.php');
$cShip = new shipping;
require(DIR_WS_CLASSES . 'ship2pay_payment.php');
$cPay = new payment;
if (zen_not_null($action)) {
  switch ($action) {
    case 'insert':
      $shp_id = zen_db_prepare_input($_POST['shp_id']);
      if (isset($_POST['pay_ids'])) {
        $pay_ids = zen_db_prepare_input(implode(";", $_POST['pay_ids']));
      }
      $db->Execute("INSERT INTO " . TABLE_SHIP2PAY . " (shipment, payments_allowed,status)
                    VALUES (" . (int)$shp_id . ", '" . zen_db_input($pay_ids) . "',0)");
      zen_redirect(zen_href_link(FILENAME_SHIP2PAY));
      break;
    case 'save':
      $s2p_id = (int)$_GET['s2p_id'];
      $shp_id = zen_db_prepare_input($_POST['shp_id']);
      if (isset($_POST['pay_ids'])) {
        $pay_ids = zen_db_prepare_input(implode(";", $_POST['pay_ids']));
      }
      $db->Execute("UPDATE " . TABLE_SHIP2PAY . "
                    SET payments_allowed = '" . zen_db_input($pay_ids) . "',
                        shipment = '" . zen_db_input($shp_id) . "'
                    WHERE s2p_id = " . zen_db_input($s2p_id));
      zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p_id));
      break;
    case 'deleteconfirm':
      $s2p_id = zen_db_prepare_input($_GET['s2p_id']);
      $db->Execute("delete from " . TABLE_SHIP2PAY . " where s2p_id = " . zen_db_input($s2p_id));
      zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page']));
      break;
    case 'disable':
      $s2p_id = zen_db_prepare_input($_GET['s2p_id']);
      $db->Execute("update " . TABLE_SHIP2PAY . " set status = 0 where s2p_id = " . zen_db_input($s2p_id));
      zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p_id));
      break;
    case 'enable':
      $s2p_id = zen_db_prepare_input($_GET['s2p_id']);
      $db->Execute("update " . TABLE_SHIP2PAY . " set status = 1 where s2p_id = " . zen_db_input($s2p_id));
      zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p_id));
      break;
  }
}
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <script type="text/javascript" src="includes/menu.js"></script>
    <script type="text/javascript" src="includes/general.js"></script>
    <script type="text/javascript">
      <!--
      function init()
      {
          cssjsmenu('navbar');
          if (document.getElementById)
          {
              var kill = document.getElementById('hoverJS');
              kill.disabled = true;
          }
      }
      // -->
    </script>
  </head>
  <body onLoad="init()">
    <!-- header //-->
      <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
      <!-- header_eof //-->
      <!-- body //-->
    <table style="width:100%;padding: 2px;margin: 2px">
      <tr>
        <!-- body_text //-->
        <td style="width:100%;vertical-align: top">
          <table style="width:100%;padding: 2px;margin: 0">
            <tr>
              <td style="width: 100%">
                <table style="width:100%;padding: 0;margin: 0">
                  <tr>
                    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                    <td class="pageHeading" style="text-align: right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td>
                <table style="width:100%;padding: 0;margin: 0">
                  <tr>
                    <td style="vertical-align: top">
                      <table style="width:100%;padding: 2px;margin: 0">
                        <tr class="dataTableHeadingRow">
                          <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SHIPMENT; ?></td>
                          <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENTS; ?></td>
                          <td class="dataTableHeadingContent" style="text-align: center"><?php echo TABLE_HEADING_STATUS; ?></td>
                          <td class="dataTableHeadingContent" style="text-align: right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                        </tr>
                        <?php
                        $s2p_query_raw = "SELECT s2p_id, shipment, payments_allowed, status FROM " . TABLE_SHIP2PAY . " ORDER BY shipment";
                        $s2p_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $s2p_query_raw, $s2p_query_numrows);
                        $s2p = $db->Execute($s2p_query_raw);
                        while (!$s2p->EOF) {
                          if ((!isset($_GET['s2p_id']) || (isset($_GET['s2p_id']) && ($_GET['s2p_id'] == $s2p->fields['s2p_id']))) && !isset($trInfo) && (substr($action, 0, 3) != 'new')) {
                            $trInfo = new objectInfo($s2p->fields);
                          }

                          if (isset($trInfo) && is_object($trInfo) && ($s2p->fields['s2p_id'] == $trInfo->s2p_id)) {
                            echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id . '&action=edit') . '\'">' . "\n";
                          } else {
                            echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p->fields['s2p_id']) . '\'">' . "\n";
                          }
                          ?>
                          <td class="dataTableContent">&nbsp;<?php echo $s2p->fields['shipment']; ?></td>
                          <td class="dataTableContent"><?php echo $cPay->GetModuleName($s2p->fields['payments_allowed']); ?></td>
                          <td class="dataTableContent" style="text-align: center">
                              <?php
                              if ($s2p->fields['status'] == '1') {
                                echo '<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p->fields['s2p_id'] . '&action=disable') . '">' . zen_image(DIR_WS_IMAGES . 'icon_green_on.gif', IMAGE_ICON_STATUS_ON) . '</a>';
                              } else {
                                echo '<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p->fields['s2p_id'] . '&action=enable') . '">' . zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_STATUS_OFF) . '</a>';
                              }
                              ?>
                          </td>
                          <td class="dataTableContent" style="text-align: right"><?php
                              if ((is_object($trInfo)) && ($s2p->fields['s2p_id'] == $trInfo->s2p_id)) {
                                echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
                              } else {
                                echo '<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p->fields['s2p_id']) . '">' . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
                              }
                              ?>
                            &nbsp;
                          </td>
                    </tr>
                    <?php
                    $s2p->MoveNext();
                  }
                  ?>
                  <tr>
                    <td colspan="3">
                      <table style="width:100%;padding: 2px;margin: 0">
                        <tr>
                          <td class="smallText" style="vertical-align: top"><?php echo $s2p_split->display_count($s2p_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PAYMENTS); ?></td>
                          <td class="smallText" style="text-align: right"><?php echo $s2p_split->display_links($s2p_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                        </tr>
                        <?php
                        if (empty($action)) {
                          ?>
                          <tr>
                            <td style="text-align: right" colspan="2"><?php echo '<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&action=new') . '">' . zen_image_button('button_insert.gif', IMAGE_INSERT) . '</a>'; ?></td>
                          </tr>
                          <?php
                        }
                        ?>
                      </table>
                    </td>
                  </tr>
                </table></td>
              <?php
              $heading = array();
              $contents = array();
              switch ($action) {
                case 'new':
                  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_SHP2PAY . '</b>');
                  $contents = array('form' => zen_draw_form('s2p', FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&action=insert'));
                  $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
                  $contents[] = array('text' => '<br>' . TEXT_INFO_SHIPMENT . '<br>' . $cShip->shipping_select('name="shp_id"'));
                  $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENTS . '<br>' . $cPay->payment_multiselect('name="pay_ids[]"'));
                  $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_insert.gif', IMAGE_INSERT) . '&nbsp;<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page']) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                  break;
                case 'edit':
                  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_SHP2PAY . '</b>');
                  $contents = array('form' => zen_draw_form('s2p', FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id . '&action=save'));
                  $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
                  $contents[] = array('text' => '<br>' . TEXT_INFO_SHIPMENT . '<br>' . $cShip->shipping_select('name="shp_id"', $trInfo->shipment));
                  $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENTS . '<br>' . $cPay->payment_multiselect('name="pay_ids[]"', $trInfo->payments_allowed));
                  $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_update.gif', IMAGE_UPDATE) . '&nbsp;<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                  break;
                case 'delete':
                  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SHP2PAY . '</b>');
                  $contents = array('form' => zen_draw_form('s2p', FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id . '&action=deleteconfirm'));
                  $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
                  $contents[] = array('text' => '<br><b>' . $trInfo->shipment . ' >> ' . $cPay->GetModuleName($trInfo->payments_allowed) . '</b>');
                  $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_module_remove.gif', IMAGE_DELETE) . '&nbsp;<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
                  break;
                default:
                  if (is_object($trInfo)) {
                    $heading[] = array('text' => '<b>' . $trInfo->shipment . '</b>');
                    $contents[] = array('align' => 'center', 'text' => '</a> <a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id . '&action=delete') . '">' . zen_image_button('button_module_remove.gif', IMAGE_DELETE) . '</a> <a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->s2p_id . '&action=edit') . '">' . zen_image_button('button_edit.gif', IMAGE_EDIT));
                    $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENTS_ALLOWED . '<br><b>' . $cPay->GetModuleName($trInfo->payments_allowed) . '</b>');
                  }
                  break;
              }

              if ((zen_not_null($heading)) && (zen_not_null($contents))) {
                echo '            <td width="25%" valign="top">' . "\n";
                $box = new box;
                echo $box->infoBox($heading, $contents);
                echo '            </td>' . "\n";
              }
              ?>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </td>
  <!-- body_text_eof //-->
</tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
