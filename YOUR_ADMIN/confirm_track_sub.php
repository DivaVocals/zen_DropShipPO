<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
//  $Id: confirm_track_sub.php 
//

// Include application configuration parameters
require('includes/application_top.php');
require(DIR_FS_CATALOG . 'includes/database_tables.php');
require('includes/extra_datafiles/purchaseorders.php');
require('includes/languages/english/extra_definitions/confirm_tracking.php');
require('includes/languages/english/orders.php');
require(DIR_FS_CATALOG . 'includes/filenames.php');
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <link rel="stylesheet" type="text/css" href="includes/style_tracking.css">
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<?php
if ($_POST['save'] == 'save')
{
    $ile = $_POST['ile'];
    //wprowadzanie zmian w tebeli orders_status_historys
    $tracka_id1 = $_POST['track_id1'];
    $tracka_id2 = $_POST['track_id2'];
    $tracka_id3 = $_POST['track_id3'];
    $tracka_id4 = $_POST['track_id4'];
    $tracka_id5 = $_POST['track_id5'];
    $orders_id = $_POST['orders_id_0'];

//ladowanie zmiennej orders_products_id i sprawdzenie ile zostalo zaznaczone pol typu checkbox
    for ($k = 0; $k < $ile; $k++) {

        if ($_POST['orders_products_id_' . $k] != '') {

            $orders_products_id[$k] = $_POST['orders_products_id_' . $k];


        }
    }

    if (count($orders_products_id) == 0) {
        echo "<font class='tekst'>" . TRACK_SAVE_ERROR . "</font>";
    } else {
        if (($tracka_id1 != '') or ($tracka_id2 != '') or ($tracka_id3 != '') or ($tracka_id4 != '') or ($tracka_id5 != '')) {
//funkcja sprawdzajaca shipping complet
            function sprawdz($orders_id, $ilosc_checkboxow)
            {
                global $db;
                $query8 = $db->Execute("SELECT orders_id, item_shipped FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id='$orders_id' AND item_shipped='0'");

                if (!$query8->EOF) {
                    return 1;
                } else {
                    return 0;
                }

            }

//generowanie komentarza oraz statusu zamowienia gdy tracking jest kompletny
            if (sprawdz($orders_id, count($orders_products_id)) == 1) {
                $order_shipping_complete = 1;
                $status = 3;
                $comments = PO_FULLSHIP_COMMENTS . "

The following items have shipped:
";

            }


            if (sprawdz($orders_id, count($orders_products_id)) == 0) {
                $order_shipping_complete = 0;
                $status = 2;
                $comments = PO_PARTIALSHIP_COMMENTS . "

The following items have shipped:
";
            }

            for ($n = 0; $n <= count($orders_products_id); $n++) {
                $tmp = $orders_products_id[$n];

                $row6c = $db->Execute("SELECT products_name, products_quantity FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id='$orders_id' AND orders_products_id='$tmp'");

                if (count($orders_products_id) == 1 OR count($orders_products_id) == 0 OR count($orders_products_id) == $n + 1 OR count($orders_products_id) == $n) {

                    $znacznik = '';
                } else {
                    $znacznik = ', ';

                }

                if (defined('POSM_MODULE_VERSION')) {
                    $row6->fields['products_name'] = preg_replace ('/\[.*\]/', '', $row6->fields['products_name']);
                    $row6c->fields['products_name'] = preg_replace ('/\[.*\]/', '', $row6c->fields['products_name']);
                }
                $comments = $comments . $row6c->fields['products_quantity'] . " " . $row6c->fields['products_name'] . $znacznik;

            }
            $comments = str_replace("'", '\'\'', $comments);


            for ($k = 0; $k <= count($orders_products_id); $k++) {

                $tmp = $orders_products_id[$k];
                $db->Execute("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET item_shipped=1 WHERE orders_products_id='$tmp' LIMIT 1");

            }

            $row44 = $db->Execute("SELECT date_purchased, customers_name, customers_email_address, orders_status FROM " . TABLE_ORDERS . " WHERE orders_id='$orders_id'");


            $row55 = $db->Execute("SELECT orders_status_name, orders_status_id FROM " . TABLE_ORDERS_STATUS . " WHERE orders_status_id='$status'");

            $row_notify = $db->Execute("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key='PO_NOTIFY'");

            if ($row_notify->fields['configuration_value'] == 1) {

                if ($order_shipping_complete == 1) {
                    $customer_notified = 1;
                } else {
                    $customer_notified = 0;
                }
                define('SEND_EMAILS', 'true');

                $notify_comments = '';

                if ($comments != '') {
                    $notify_comments = EMAIL_TEXT_COMMENTS_UPDATE . $comments . "\n\n";
                    $order_comment = $comments;
                }
                if ($tracka_id1 != '') {
                    $notify_comments .= 'Your ' . CARRIER_NAME_1 . ' Tracking ID is ' . $tracka_id1 . "\n" . 'You can track your package at <a href="' . CARRIER_LINK_1 . $tracka_id1 . '">' . CARRIER_LINK_1 . $tracka_id1 . "</a>\n\n";
                }
                if ($tracka_id2 != '') {
                    $notify_comments .= 'Your ' . CARRIER_NAME_2 . ' Tracking ID is ' . $tracka_id2 . "\n" . 'You can track your package at <a href="' . CARRIER_LINK_2 . $tracka_id2 . '">' . CARRIER_LINK_2 . $tracka_id2 . "</a>\n\n";
                }
                if ($tracka_id3 != '') {
                    $notify_comments .= 'Your ' . CARRIER_NAME_3 . ' Tracking ID is ' . $tracka_id3 . "\n" . 'You can track your package at <a href="' . CARRIER_LINK_3 . $tracka_id3 . '">' . CARRIER_LINK_3 . $tracka_id3 . "</a>\n\n";
                }
                if ($tracka_id4 != '') {
                    $notify_comments .= 'Your ' . CARRIER_NAME_4 . ' Tracking ID is ' . $tracka_id4 . "\n" . 'You can track your package at <a href="' . CARRIER_LINK_4 . $tracka_id4 . '">' . CARRIER_LINK_4 . $tracka_id4 . "</a>\n\n";
                }
                if ($tracka_id5 != '') {
                    $notify_comments .= 'Your ' . CARRIER_NAME_5 . ' Tracking ID is ' . $tracka_id5 . "\n" . 'You can track your package at <a href="' . CARRIER_LINK_5 . $tracka_id5 . '">' . CARRIER_LINK_5 . $tracka_id5 . "</a>\n\n";
                }
                $message = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" .
                    EMAIL_TEXT_ORDER_NUMBER . ' ' . $orders_id . "\n\n" .
                    EMAIL_TEXT_INVOICE_URL . ' <a href="' . HTTP_SERVER . DIR_WS_CATALOG . "/index.php?main_page=tracker&order_id=$orders_id" . '">' . HTTP_SERVER . DIR_WS_CATALOG . "/index.php?main_page=tracker&order_id=$orders_id" . "</a>\n\n" .
                    EMAIL_TEXT_DATE_ORDERED . ' ' . $row44->fields['date_purchased'] . "\n\n" .
                    $notify_comments .
                    EMAIL_TEXT_STATUS_UPDATED . sprintf(EMAIL_TEXT_STATUS_LABEL, $row55->fields['orders_status_name']) .
                    EMAIL_TEXT_STATUS_PLEASE_REPLY;
                $html_msg['EMAIL_MESSAGE_HTML'] = str_replace('
   ', '<br />', $message);
                // REMOVE THE TAGS FOR TEXT EMAIL
                $message = strip_tags($message);

                // SET THE TO EMAIL ADDRESS
                $email_to = $row44->fields['customers_email_address'];

                // SET THE SUBJECT
                $subject = EMAIL_TEXT_SUBJECT . ' #' . $orders_id;


                zen_mail($email_to, $email_to, $subject, $message, STORE_NAME, EMAIL_FROM, $html_msg, NULL);
            } else {
                $customer_notified = 0;
            }

//wpisywanie odpowiednich danych do statusu
            $db->Execute("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY . "
            (orders_status_id, orders_id, date_added,
                customer_notified, track_id1, track_id2, track_id3, track_id4, track_id5,comments)
               values ('$status','$orders_id',now(),'1','$tracka_id1',
              '$tracka_id2','$tracka_id3','$tracka_id4','$tracka_id5','$comments')");

            if ($order_shipping_complete == 1) {
                $db->Execute("update " . TABLE_ORDERS . "
                        set orders_status = '" . $status . "', last_modified = now() where orders_id ='$orders_id'");
            }
            echo "<font class='tekst'>" . SUBCONTRACTOR_TRACKING_THANKYOU . "</font>";;


        } else {
            echo "<font class='tekst'>" . TRACK_SAVE_ERROR . "</font>";
        }
    }
}
else{
$aID = (int)$_GET['aID'];
$oID = (int)$_GET['oID'];

// If you're not a superuser, you should be logged in as this id.
if (!zen_is_superuser()) { 
   $recs = $db->Execute("SELECT subcontractors_id FROM " . TABLE_SUBCONTRACTORS_TO_ADMINS . " WHERE admin_id = " . $_SESSION['admin_id']); 
   $found = false; 
   while (!$recs->EOF) { 
        if ($recs->fields['subcontractors_id'] == $aID) { 
           $found = true; break;
        }
        $recs->MoveNext(); 
   }
   if (!$found) {
      echo "<font class='tekst'>" . TRACKING_INVALID. "</font>";;
      return; 
   }
}

//funkcja sprawdzajaca czy istnieje taki numer po i subkontraktor przyporzadkowany temu numerowi
function ilosc($oID, $aID)
{
    global $db;
    $result = $db->Execute("SELECT po_number FROM " . TABLE_ORDERS_PRODUCTS . " WHERE po_sent_to_subcontractor='$aID' AND po_number='$oID' AND item_shipped=0");

    if (!$result->EOF) {
        return 1;
    } else {
        return 0;
    }
}

//funkcja oblsugujaca blad jezeli nic nie znajdzie
function error($oID, $aID)
{
    global $db;
    $result = $db->Execute("SELECT po_number
         FROM " . TABLE_ORDERS_PRODUCTS . " WHERE po_sent_to_subcontractor='$aID' AND po_number='$oID'");

    if (!$result->EOF) {
        return 1;
    } else {
        return 0;
    }
}

//funkcja sprawdzajaca czy wszystkie dane zostaly juz zapisane jesli nie to pozwala na zapisanie trackingu
function save($oID, $aID)
{
    global $db;
    $query110a = $db->Execute("SELECT po_number
         FROM " . TABLE_ORDERS_PRODUCTS . " WHERE po_sent_to_subcontractor='$aID' AND po_number='$oID'");

    $query110b = $db->Execute("SELECT po_number
         FROM " . TABLE_ORDERS_PRODUCTS . " WHERE po_sent_to_subcontractor='$aID' AND po_number='$oID' AND item_shipped=1");

    if ($query110a->RecordCount() == $query110b->RecordCount()) {
        return 0;
    } else {
        return 1;
    }
}


//jezeli funckja ilosc() zwroci jedynke to bedzie wykonywany ponizszy kod
if (error($oID, $aID) == 0) {
    echo "<font class='tekst'>" . TRACKING_ERROR . "</font>";;
} else {
    if (save($oID, $aID) == 0) {
        echo "<font class='tekst'>" . TRACKING_SAVING . "</font>";;
    }
}

if (error($oID, $aID) == 1 AND save($oID, $aID) == 1)
{
if (ilosc($oID, $aID))
{
echo "<form name='save1' method='POST' action='confirm_track_sub.php'>"; 

echo "<font class='tekst'>" . SUBCONTRACTOR_FORM_DESCRIPTION . "</font>";
$i = 0;
?>
<table width='950px' border="0" cellspacing="0" cellpadding="3">
    <tr>
        <td width=80% valign='top'>
            <table border="0" cellspacing="0" cellpadding="3">
                <tr>
                    <td width='5%' align='left' class='td_naglowek'><font
                            class='naglowki'><?php echo TRACKING_PO_NUMBER; ?></font></td>
                    <td width='15%' align='left' class='td_naglowek'><font
                            class='naglowki'><?php echo TRACKING_PO_DATE; ?></font></td>
                    <td width='20%' align='left' class='td_naglowek'><font
                            class='naglowki'><?php echo TRACKING_CUSTOMER_DATA; ?></font></td>
                    <td width='20%' align='left' class='td_naglowek_zak'><font class='naglowki'>Add Tracking ID</font>
                    </td>

                </tr>
                <?php

                $row2 = $db->Execute("SELECT po_number, po_sent_to_subcontractor, item_shipped, orders_id, po_date
            FROM " . TABLE_ORDERS_PRODUCTS . " WHERE po_sent_to_subcontractor='$aID' AND po_number='$oID' AND item_shipped=0");

                $row3 = $db->Execute("SELECT delivery_name, delivery_company, delivery_street_address, delivery_city, delivery_postcode,
            delivery_state, delivery_country, delivery_suburb
            FROM " . TABLE_ORDERS . " WHERE orders_id='" . $row2->fields['orders_id'] . "'");
                if ($row3->fields['delivery_suburb'] == "" ||
                    $row3->fields['delivery_suburb'] == NULL
                )
                    $orderaddresssuburb = "";
                else
                    $orderaddresssuburb = "<br />" . $row3->fields['delivery_suburb'];
                if ($row3->fields['delivery_company'] == "" ||
                    $row3->fields['delivery_company'] == NULL
                )
                    $orderaddresscompany = "";
                else
                    $orderaddresscompany = $row3->fields['delivery_company'] . "<br />";
                $ordersaddress = $row3->fields['delivery_name'] . "<br />" . $orderaddresscompany . $row3->fields['delivery_street_address'] . $orderaddresssuburb . "<br />" . $row3->fields['delivery_city'] . ", " . $row3->fields['delivery_state'] . " " . $row3->fields['delivery_postcode'] . "<br />" . $row3->fields['delivery_country'];
                ?>
                <tr>
                    <td width='5%' class='td' valign="top"><font
                            class='tekst'><?php echo $row2->fields['orders_id'] . "-" . $row2->fields['po_number']; ?></font></td>
                    <td width='10%' class='td' valign="top"><font class='tekst'><?php echo $row2->fields['po_date']; ?></font></td>
                    <td width='20%' class='td' valign="top"><font class='tekst'><?php echo $ordersaddress; ?></font>
                    </td>
                    <td class='td_zakonczenie'>
                        <table border="0" cellspacing="0" cellpadding="3">

                                <tr>
                                    <td><font class='tekst'>
                                            <?php echo CARRIER_NAME_1; ?></font></td>
                                    <td valign="top">
                                        <?php echo "<input type='text' name='track_id1' class='sub'>"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><font class='tekst'><?php echo CARRIER_NAME_2; ?></font></td>
                                    <td valign="top">

                                        <?php echo "<input type='text' name='track_id2' class='sub'>"; ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td><font class='tekst'><?php echo CARRIER_NAME_3; ?></font></td>
                                    <td valign="top">
                                        <?php echo "<input type='text' name='track_id3' class='sub'>"; ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td><font class='tekst'><?php echo CARRIER_NAME_4; ?></font></td>
                                    <td valign="top">
                                        <?php echo "<input type='text' name='track_id4' class='sub'>"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><font class='tekst'><?php echo CARRIER_NAME_5; ?></font></td>
                                    <td valign="top">
                                        <?php echo "<input type='text' name='track_id5' class='sub'>"; ?>
                                    </td>
                                </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <br>
            <table border="0" cellspacing="0" cellpadding="3">
                <tr>
                    <td class='td_naglowek'><font class='naglowki'><?php echo SEND_TRACKING_YES_NO; ?></font></td>
                    <td align="left" valign="top" class='td_naglowek_zak'><font
                            class='naglowki'><?php echo TRACKING_PRODUCT_NAME; ?></font></td>
                </tr>

                <?php
                $row5 = $db->Execute("SELECT orders_id, products_name, products_model, orders_products_id
            FROM " . TABLE_ORDERS_PRODUCTS . " WHERE po_sent_to_subcontractor='$aID' AND po_number='$oID' AND item_shipped=0");
                $i = 0;
                while (!$row5->EOF) {
                     if (defined('POSM_MODULE_VERSION')) {
                        $row5->fields['products_name'] = preg_replace ('/\[.*\]/', '', $row5->fields['products_name']);
                     }
                    $row6 = $db->Execute("SELECT orders_id, orders_products_id, products_options, products_options_values  
                                         FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
                                         WHERE orders_products_id='" . $row5->fields['orders_products_id'] ."' AND orders_id='" . $row5->fields['orders_id']. "'");

                    $attributes = '';
                    while (!$row6->EOF) {
                        $attributes = $attributes . $row6->fields['products_options'] . ": " . $row6->fields['products_options_values'] . "<br>";
                        $row6->MoveNext();
                    }

                    if (!empty($row5->fields['products_model'])) { 
                       $model = "(" . $row5->fields['products_model'] . ")";
                    }
                    ?>
                    <tr>
                        <td align="center" class='td' valign='top'><input type='checkbox'
                                                                          name='<?php echo "orders_products_id_" . $i ?>'
                                                                          value='<?php echo $row5->fields['orders_products_id']; ?>' CHECKED></td>
                        <td class='td_zakonczenie'>
                        <font class='tekst'>
<?php 
   echo $model . "<br />"; 
   echo $row5->fields['products_name'] . "<br />" . $attributes; 
?>
                    </font></td>
                    </tr>
                    <?php
                    echo "<input type='hidden' name='orders_id_$i' value='" . $row5->fields['orders_id']. "'>";
                    $i++;
                    $row5->MoveNext();
                }
                echo "<input type='hidden' name='ile' value='$i'>";
                echo "<input type='hidden' name='x' value='$aID'>";
                echo "<input type='hidden' name='y' value='$oID'>";
                echo "<input type='hidden' name='save' value='save'>";
                ?>
                <tr>
                    <td COLSPAN="2" align='center'><input class="normal_button button" type="button"
                                                          value="<?php echo IMAGE_SAVE; ?>" name='insert'
                                                          ONCLICK="javascript:document.save1.submit();"></td>
                </tr>
            </table>
            </form>

            <?php
            }

            }
            }
            ?>
</body>
</html>
