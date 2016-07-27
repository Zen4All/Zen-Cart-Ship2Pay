<?php
/*
  $Id: Ship2Pay, v1.5 17:18 2008-06-27 gjw Exp $
  modified by wladimirec
  2012-11-01 fdeboer
  - Replaced all $HTTP_GET/POST_VARS with _GET/_POST
  - Fixed button image links
*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'shipping.php');
  $cShip = new shipping;
  require(DIR_WS_CLASSES . 'payment.php');
  $cPay = new payment;
  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'insert':
        $shp_id = zen_db_prepare_input($_POST['shp_id']);
        if (isset($_POST['pay_ids'])){
          $pay_ids = zen_db_prepare_input(implode(";", $_POST['pay_ids']));
        }
        $db->Execute("insert into " . TABLE_SHIP2PAY . " (shipment, payments_allowed,status) values ('" . zen_db_input($shp_id) . "', '" . zen_db_input($pay_ids)."',0)");
        zen_redirect(zen_href_link(FILENAME_SHIP2PAY));
        break;
      case 'save':
        $s2p_id = zen_db_prepare_input($_GET['s2p_id']);
        $shp_id = zen_db_prepare_input($_POST['shp_id']);
        if (isset($_POST['pay_ids'])){
          $pay_ids = zen_db_prepare_input(implode(";", $_POST['pay_ids']));
        }
        $db->Execute("update " . TABLE_SHIP2PAY . " set payments_allowed = '" . zen_db_input($pay_ids) . "', shipment = '" . zen_db_input($shp_id) . "' where s2p_id = ". zen_db_input($s2p_id));
        zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p_id));
        break;
      case 'deleteconfirm':
        $s2p_id = zen_db_prepare_input($_GET['s2p_id']);
        $db->Execute("delete from " . TABLE_SHIP2PAY . " where s2p_id = " . zen_db_input($s2p_id));
        zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page']));
        break;
      case 'disable':
        $shp_id = zen_db_prepare_input($_GET['s2p_id']);
        $db->Execute("update " . TABLE_SHIP2PAY . " set status = 0 where s2p_id = " . zen_db_input($shp_id));
        zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p_id));
        break;
      case 'enable':
        $shp_id = zen_db_prepare_input($_GET['s2p_id']);
        $db->Execute("update " . TABLE_SHIP2PAY . " set status = 1 where s2p_id = " . zen_db_input($shp_id));
        zen_redirect(zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p_id));
        break;
    }
  }
  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td style="width: 70%; vertical-align: top;">
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table>
	</td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0" >
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SHIPMENT; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENTS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $s2p_query_raw = "select s2p_id, shipment, payments_allowed, status from " . TABLE_SHIP2PAY;
  $s2p_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $s2p_query_raw, $s2p_query_numrows);
  $s2p = $db->Execute($s2p_query_raw);
  while (!$s2p->EOF) {
    if (((!$_GET['s2p_id']) || (@$_GET['s2p_id'] == $s2p->fields['s2p_id'])) && (!$trInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $trInfo = new objectInfo($s2p);
    }

    if ( (is_object($trInfo)) && ($s2p->fields['s2p_id'] == $trInfo->fields['s2p_id']) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->fields['s2p_id'] . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p->fields['s2p_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent">&nbsp;<?php echo $s2p->fields['shipment']; ?></td>
                <td class="dataTableContent"><?php echo $cPay->GetModuleName($s2p->fields['payments_allowed']); ?></td>
                <td class="dataTableContent" align="center">
                <?php
                      if ($s2p->fields['status'] == '1') {
                        echo zen_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p->fields['s2p_id'] . '&action=disable') . '">' . zen_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                      } else {
                        echo '<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p->fields['s2p_id'] . '&action=enable') . '">' . zen_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . zen_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                      }
                ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($trInfo)) && ($s2p->fields['s2p_id'] == $trInfo->fields['s2p_id']) ) { echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $s2p->fields['s2p_id']) . '">' . zen_image(DIR_WS_ADMIN . DIR_WS_ICONS . IMAGE_ICON_INFO, IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
	$s2p->MoveNext();
  }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $s2p_split->display_count($s2p_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PAYMENTS); ?></td>
                    <td class="smallText" align="right"><?php echo $s2p_split->display_links($s2p_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td colspan="5" align="right"><?php echo '<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&action=new') . '">' . zen_image_button('button_insert.gif', IMAGE_INSERT) . '</a>'; ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
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
      $contents = array('form' => zen_draw_form('s2p', FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->fields['s2p_id']  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_SHIPMENT . '<br>' . $cShip->shipping_select('name="shp_id"',$trInfo->fields['shipment']));
      $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENTS . '<br>' . $cPay->payment_multiselect('name="pay_ids[]"', $trInfo->fields['payments_allowed']));
      $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_update.gif', IMAGE_UPDATE) . '&nbsp;<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->fields['s2p_id']) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SHP2PAY . '</b>');
      $contents = array('form' => zen_draw_form('s2p', FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->fields['s2p_id']  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $trInfo->fields['shipment'] . ' >> ' . $cPay->GetModuleName($trInfo->fields['payments_allowed']) . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_module_remove.gif', IMAGE_DELETE) . '&nbsp;<a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->fields['s2p_id']) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($trInfo)) {
        $heading[] = array('text' => '<b>' . $trInfo->fields['shipment'] . '</b>');
        $contents[] = array('align' => 'center', 'text' => '</a> <a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->fields['s2p_id'] . '&action=delete') . '">' . zen_image_button('button_module_remove.gif', IMAGE_DELETE) . '</a> <a href="' . zen_href_link(FILENAME_SHIP2PAY, 'page=' . $_GET['page'] . '&s2p_id=' . $trInfo->fields['s2p_id'] . '&action=edit') . '">' . zen_image_button('button_edit.gif', IMAGE_EDIT));
        $contents[] = array('text' => '<br>' . TEXT_INFO_PAYMENTS_ALLOWED . '<br><b>' . $cPay->GetModuleName($trInfo->fields['payments_allowed']) .'</b>');
      }
      break;
  }

  if ( (zen_not_null($heading)) && (zen_not_null($contents)) ) {
    echo '            <td class="tdConf" style="width: 30%;">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
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
