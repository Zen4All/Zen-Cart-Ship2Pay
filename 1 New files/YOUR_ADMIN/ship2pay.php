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
      if (!empty($_POST['pay_ids'])) {
        $pay_ids = zen_db_prepare_input(implode(";", $_POST['pay_ids']));
      }
      $db->Execute("INSERT INTO " . TABLE_SHIP2PAY . " (shipment, payments_allowed,status)
                    VALUES ('" . zen_db_input($shp_id) . "', '" . zen_db_input($pay_ids) . "',0)");
      zen_redirect(zen_href_link(FILENAME_SHIP2PAY));
      break;
    case 'save':
      $s2p_id = (int)$_GET['s2p_id'];
      $shp_id = zen_db_prepare_input($_POST['shp_id']);
      if (!empty($_POST['pay_ids'])) {
        $pay_ids = zen_db_prepare_input(implode(";", $_POST['pay_ids']));
      }
      $db->Execute("UPDATE " . TABLE_SHIP2PAY . "
                    SET payments_allowed = '" . zen_db_input($pay_ids) . "',
                        shipment = '" . zen_db_input($shp_id) . "'
                    WHERE s2p_id = " . $s2p_id);
      zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . $s2p_id));
      break;
    case 'deleteconfirm':
      $s2p_id = (int)$_GET['s2p_id'];
      $db->Execute("DELETE FROM " . TABLE_SHIP2PAY . " WHERE s2p_id = " . $s2p_id);
      zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page']));
      break;
    case 'disable':
      $s2p_id = (int)$_GET['s2p_id'];
      $db->Execute("UPDATE " . TABLE_SHIP2PAY . "
                    SET status = 0
                    WHERE s2p_id = " . $s2p_id);
      zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . $s2p_id));
      break;
    case 'enable':
      $s2p_id = (int)$_GET['s2p_id'];
      $db->Execute("UPDATE " . TABLE_SHIP2PAY . "
                    SET status = 1
                    WHERE s2p_id = " . $s2p_id);
      zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . $s2p_id));
      break;
  }
}

$s2p_query_raw = "SELECT s2p_id, shipment, payments_allowed, status
                  FROM " . TABLE_SHIP2PAY . "
                  ORDER BY shipment";
$s2p_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $s2p_query_raw, $s2p_query_numrows);
$s2p = $db->Execute($s2p_query_raw);
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=1">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" href="includes/stylesheet.css">
    <script src="includes/general.js"></script>
  </head>
  <body>
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
    <div class="container-fluid">
      <!-- body_text //-->
      <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 configurationColumnLeft">
          <table class="table table-striped table-hover">
            <thead>
              <tr class="dataTableHeadingRow">
                <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_SHIPMENT; ?></th>
                <th class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENTS; ?></th>
                <th class="dataTableHeadingContent text-center"><?php echo TABLE_HEADING_STATUS; ?></th>
                <th class="dataTableHeadingContent text-right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
                <?php
                foreach ($s2p as $item) {
                  if ((!isset($_GET['s2p_id']) || (isset($_GET['s2p_id']) && ($_GET['s2p_id'] == $item['s2p_id']))) && !isset($trInfo) && (substr($action, 0, 3) != 'new')) {
                    $trInfo = new objectInfo($item);
                  }

                  if (isset($trInfo) && is_object($trInfo) && ($item['s2p_id'] == $trInfo->s2p_id)) {
                    ?>
                  <tr id="defaultSelected" class="dataTableRowSelected" onclick="document.location.href = '<?php echo zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . $trInfo->s2p_id . '&action=edit'); ?>'">
                    <?php } else { ?>
                  <tr class="dataTableRow" onclick="document.location.href = '<?php echo zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . $item['s2p_id']); ?>'">
                    <?php } ?>
                  <td class="dataTableContent"><?php echo $item['shipment']; ?></td>
                  <td class="dataTableContent"><?php echo $cPay->GetModuleName($item['payments_allowed']); ?></td>
                  <td class="dataTableContent text-center">
                      <?php if ($item['status'] == '1') { ?>
                      <a href="<?php echo zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . $item['s2p_id'] . '&action=disable'); ?>"><?php echo zen_image(DIR_WS_IMAGES . 'icon_green_on.gif', IMAGE_ICON_STATUS_ON); ?></a>
                    <?php } else { ?>
                      <a href="<?php echo zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . $item['s2p_id'] . '&action=enable'); ?>"><?php echo zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_STATUS_OFF); ?></a>
                    <?php } ?>
                  </td>
                  <td class="dataTableContent text-right">
                      <?php if ((is_object($trInfo)) && ($item['s2p_id'] == $trInfo->s2p_id)) { ?>
                      <i class="fa fa-caret-right fa-lg"></i>
                    <?php } else { ?>
                      <a href="<?php echo zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . (int)$item['s2p_id']); ?>"><i class="fa fa-info-circle fa-lg"></i></a>
                    <?php } ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 configurationColumnRight">
            <?php
            $heading = array();
            $contents = array();
            switch ($action) {
              case 'new':
                $heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_NEW_SHP2PAY . '</h4>');
                $contents = array('form' => zen_draw_form('s2p', FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&action=insert', 'post', 'class="form-horizontal"'));
                $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
                $contents[] = array('text' => zen_draw_label(TEXT_INFO_SHIPMENT, 'shp_id', 'class="control-label"') . $cShip->shipping_select('name="shp_id" class="form-control"'));
                $contents[] = array('text' => zen_draw_label(TEXT_INFO_PAYMENTS, 'pay_ids[]', 'class="control-label"') . $cPay->payment_multiselect('name="pay_ids[]" class="form-control"'));
                $contents[] = array('align' => 'text-center', 'text' => '<button type="submit" class="btn btn-primary">' . IMAGE_INSERT . '</button> <a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page']) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
                break;
              case 'edit':
                $heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_EDIT_SHP2PAY . '</h4>');
                $contents = array('form' => zen_draw_form('s2p', FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . (int)$trInfo->s2p_id . '&action=save', 'post', 'class="form-horizontal"'));
                $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
                $contents[] = array('text' => zen_draw_label(TEXT_INFO_SHIPMENT, 'shp_id', 'class="control-label"') . $cShip->shipping_select('name="shp_id" class="form-control"', $trInfo->shipment));
                $contents[] = array('text' => zen_draw_label(TEXT_INFO_PAYMENTS, 'pay_ids[]', 'class="control-label"') . $cPay->payment_multiselect('name="pay_ids[]" class="form-control"', $trInfo->payments_allowed));
                $contents[] = array('align' => 'text-center', 'text' => '<button type="submit" class="btn btn-primary">' . IMAGE_UPDATE . '</button> <a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . (int)$trInfo->s2p_id) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
                break;
              case 'delete':
                $heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_DELETE_SHP2PAY . '</h4>');
                $contents = array('form' => zen_draw_form('s2p', FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . (int)$trInfo->s2p_id . '&action=deleteconfirm', 'post', 'class="form-horizontal"'));
                $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
                $contents[] = array('text' => '<br><b>' . $trInfo->shipment . ' >> ' . $cPay->GetModuleName($trInfo->payments_allowed) . '</b>');
                $contents[] = array('align' => 'text-center', 'text' => '<button type="submit" class="btn btn-danger">' . IMAGE_DELETE . '</button> <a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . (int)$trInfo->s2p_id) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
                break;
              default:
                if (is_object($trInfo)) {
                  $heading[] = array('text' => '<h4>' . $trInfo->shipment . '</h4>');
                  $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . (int)$trInfo->s2p_id . '&action=delete') . '" class="btn btn-warning" role="button">' . IMAGE_DELETE . '</a> <a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&s2p_id=' . (int)$trInfo->s2p_id . '&action=edit') . '" class="btn btn-primary" role="button">' . IMAGE_EDIT . '</a>');
                  $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENTS_ALLOWED . '<br><b>' . $cPay->GetModuleName($trInfo->payments_allowed) . '</b>');
                }
                break;
            }

            if ((zen_not_null($heading)) && (zen_not_null($contents))) {
              $box = new box;
              echo $box->infoBox($heading, $contents);
            }
            ?>
        </div>
      </div>
      <div class="row">
        <table class="table">
          <tr>
            <td><?php echo $s2p_split->display_count($s2p_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, (int)$_GET['page'], TEXT_DISPLAY_NUMBER_OF_PAYMENTS); ?></td>
            <td class="text-right"><?php echo $s2p_split->display_links($s2p_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, (int)$_GET['page']); ?></td>
          </tr>
          <?php if (empty($action)) { ?>
            <tr>
              <td class="text-right" colspan="2"><a href="<?php echo zen_href_link(FILENAME_SHIP2PAY, 'page=' . (int)$_GET['page'] . '&action=new'); ?>" class="btn btn-primary" role="button"><?php echo IMAGE_INSERT; ?></a></td>
            </tr>
          <?php } ?>
        </table>
      </div>
    </div>
    <!-- body_text_eof //-->
    <!-- body_eof //-->

    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
  </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
