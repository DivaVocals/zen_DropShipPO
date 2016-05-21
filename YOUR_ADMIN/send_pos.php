<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
//
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
define('FPDF_FONTPATH', 'includes/classes/fpdf/font/');
require('pdfpack.php');

//load email templates
@ $wp1 = fopen("../email/email_dropship_po_header.txt", 'r');
@ $wp2 = fopen("../email/email_dropship_po_products.txt", 'r');
@ $wp3 = fopen("../email/email_dropship_po_footer.txt", 'r');

/*
function zen_get_products_manufacturers_name($product_id) {
    global $db;

    $product_query = "select m.manufacturers_name
                      from " . TABLE_PRODUCTS . " p, " .
                            TABLE_MANUFACTURERS . " m
                      where p.products_id = '" . (int)$product_id . "'
                      and p.manufacturers_id = m.manufacturers_id";

    $product =$db->Execute($product_query);

    return ($product->RecordCount() > 0) ? $product->fields['manufacturers_name'] : "";
  }
 * 
 */
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <script language="javascript" src="includes/menu.js"></script>
    <script language="javascript" src="includes/general.js"></script>

    <script type="text/javascript">
        <!--
        function init() {
            cssjsmenu('navbar');
            if (document.getElementById) {
                var kill = document.getElementById('hoverJS');
                kill.disabled = true;
            }
        }
        // -->
    </script>

</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->


<br>
<!-- body //--><br>
<table border="0" width='100%' cellspacing="0" cellpadding="0">

    <!-- body_text //-->
    <?php

    if (isset($_GET['list_order'])) {
        if ($_GET['list_order'] == 'costumers_name') $disp_order = "customers_name ASC";
        if ($_GET['list_order'] == 'costumers_namedesc') $disp_order = " customers_name DESC";


    } else {

        $disp_order = "subcontractors_id ASC";
    }


    ?>
    <?php
    // send reviewed e-mail
    if ($_POST[ereview] == 'yes') {
        $html_msg['EMAIL_MESSAGE_HTML'] = str_replace('
', '<br />', $_POST[ebody]);
        if ($_POST[includepackinglistoption] == 'yes') {
            zen_mail($_POST[eaddress], $_POST[eaddress], $_POST[etitle], $_POST[ebody], PO_FROM_EMAIL_NAME, PO_FROM_EMAIL_ADDRESS, $html_msg, NULL, PO_PACKINGLIST_FILENAME, 'application/pdf');
        } else { 
            zen_mail($_POST[eaddress], $_POST[eaddress], $_POST[etitle], $_POST[ebody], PO_FROM_EMAIL_NAME, PO_FROM_EMAIL_ADDRESS, $html_msg, NULL);
       }
    }


    // send e-mail or review email before sending
    if ((isset($_POST[what]) and $_POST[what] == 'send' and $_GET['resend'] != 'yes') || ($_GET['resend'] == 'yes')) {
        $k = $_POST[krotnosc];
        $n = 0;

        for ($p = 1; $p < $k; $p++) {
            $pos = "pos" . $p;
            $sub = "sub" . $p;
            $id = "id" . $p;
            $opi = "opi" . $p;
            $posk[$n] = $_POST[$pos];

            if ($posk[$n] == 'on') {
                $subk[$n] = $_POST[$sub];
                $idk[$n] = $_POST[$id];
                $opik[$n] = $_POST[$opi];
                $n++;
            }
        }

        if (!$wp1) {
            echo "Nie mo&#191;na otworzyc pliku";
            exit;
        } else {
            $i = 0;

            while (!feof($wp1)) {
                $zamowienie[$i] = fgets($wp1, 999);
                $i++;
            }

            fclose($wp1);
            for ($i = 0; $i < count($zamowienie); $i++) {
                $zawartosc = $zawartosc . $zamowienie[$i];
            }
        }

        if (!$wp2) {
            echo "Nie mo&#191;na otworzyc pliku";
            exit;
        } else {
            $i = 0;
            while (!feof($wp2)) {
                $tresc_robij[$i] = fgets($wp2, 999);
                $i++;
            }
        }

        fclose($wp2);
        $t = 0;

        if (!$wp3) {
            echo "Nie mo&#191;na otworzyc pliku";
            exit;
        } else {
            while (!feof($wp3)) {
                $tracking_link[$t] = fgets($wp3, 999);
                $t++;
            }
        }

        fclose($wp3);

        for ($i = 0; $i < count($tresc_robij); $i++) {
            $tresc_robij1 = $tresc_robij1 . $tresc_robij[$i];
        }

        //zbieranie danych na temat produktow i zamawiajacego
        for ($i = 0; $i < count($idk); $i++) {
            $row4 = $db->Execute("SELECT p.orders_products_id, p.products_model, p.products_name, p.final_price, p.products_quantity,
                o.customers_name, o.customers_street_address, o.customers_city, o.customers_postcode, o.customers_state, o.customers_country, o.customers_telephone, o.customers_email_address,
                o.delivery_name, o.delivery_company, o.delivery_street_address, o.delivery_city, o.delivery_state, o.delivery_postcode, o.delivery_country,
                o.billing_name, o.billing_company, o.billing_street_address, o.billing_city, o.billing_state, o.billing_postcode, o.billing_country,
                o.payment_method,  o.date_purchased, o.currency, o.customers_id, o.orders_id, o.shipping_method, o.orders_status, o.customers_suburb, o.delivery_suburb, o.billing_suburb, o.customers_company
                FROM " . TABLE_ORDERS_PRODUCTS . " as p, " . TABLE_ORDERS . " as o
                WHERE
                p.orders_id=o.orders_id
                AND
                p.orders_products_id=" . (int)$idk[$i]);
            // $shipway = '';
            while (!$row4->EOF) {
                if ($row4->fields['customers_company'] != '' &&
                    $row4->fields['customers_company'] != NULL
                )
                    $address = $row4->fields['customers_company'] . "\n";
                else
                    $address = "";
                if ($row4->fields['customers_company'] != '' && $row4->fields['customers_company'] != NULL)
                    $address .= $row4->fields['customers_street_address'] . "\n" . $row4->fields['customers_company'] . "\n" . $row4->fields['customers_city'] . ", " . $row4->fields['customers_state'] . " " . $row4->fields['customers_postcode'] . "\n" . $row4->fields['customers_country'];
                else
                    $address .= $row4->fields['customers_street_address'] . "\n" . $row4->fields['customers_city'] . ", " . $row4->fields['customers_state'] . " " . $row4->fields['customers_postcode'] . "\n" . $row4->fields['customers_country'];
                if ($row4->fields['delivery_company'] != '' && $row4->fields['delivery_company'] != NULL)
                    $address_deliver = $row4->fields['delivery_company'] . "\n";
                else
                    $address_deliver = "";
                if ($row4->fields['delivery_suburb'] != '' && $row4->fields['delivery_suburb'] != NULL)
                    $address_deliver .= $row4->fields['delivery_street_address'] . "\n" . $row4->fields['delivery_suburb'] . "\n" . $row4->fields['delivery_city'] . ", " . $row4->fields['delivery_state'] . " " . $row4->fields['delivery_postcode'] . "\n" . $row4->fields['delivery_country'];
                else
                    $address_deliver .= $row4->fields['delivery_street_address'] . "\n" . $row4->fields['delivery_city'] . ", " . $row4->fields['delivery_state'] . " " . $row4->fields['delivery_postcode'] . "\n" . $row4->fields['delivery_country'];
                if ($row4->fields['billing_company'] != '' && $row4->fields['billing_company'] != NULL)
                    $address_billing = $row4->fields['billing_company'] . "\n";
                else
                    $address_billing = "";
                if ($row4->fields['billing_suburb'] != '' && $row4->fields['billing_suburb'] != NULL)
                    $address_billing .= $row4->fields['billing_street_address'] . "\n" . $row4->fields['billing_suburb'] . "\n" . $row4->fields['billing_city'] . ", " . $row4->fields['billing_state'] . " " . $row4->fields['billing_postcode'] . "\n" . $row4->fields['billing_country'];
                else
                    $address_billing .= $row4->fields['billing_street_address'] . "\n" . $row4->fields['billing_city'] . ", " . $row4->fields['billing_state'] . " " . $row4->fields['billing_postcode'] . "\n" . $row4->fields['billing_country'];
                $price = $row4->fields['final_price'] . ' ' . $row4->fields['date_purchased'];
                $zawartosc2 = array();
                //podmiana tagow dla pliku header
                $zawartosc2[$i] = str_replace("{customers_name}", $row4->fields['customers_name'], $zawartosc);
                $zawartosc2[$i] = str_replace("{order_number}", $row4->fields['orders_id'], "$zawartosc2[$i]");
                $zawartosc2[$i] = str_replace("{customers_address}", $address, "$zawartosc2[$i]");
                $zawartosc2[$i] = str_replace("{customers_phone}", $row4->fields['customers_telephone'], "$zawartosc2[$i]");
                $zawartosc2[$i] = str_replace("{customers_email}", $row4->fields['customers_email_address'], "$zawartosc2[$i]");
                $zawartosc2[$i] = str_replace("{delivery_name}", $row4->fields['delivery_name'], "$zawartosc2[$i]");
                $zawartosc2[$i] = str_replace("{po_comments}", $_POST[posubcomments], "$zawartosc2[$i]");
                $oatmeal = $db->Execute("select comments from " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = '" . zen_db_input($row4->fields[orders_id]) . "' order by date_added");
                $catmeow = nl2br(zen_db_output($oatmeal->fields['comments']));
                $catmeow = strip_tags($catmeow);
                $zawartosc2[$i] = str_replace("{customers_comments}", $catmeow, "$zawartosc2[$i]");
                if ($row4->fields['delivery_company'] != '') {
                    $zawartosc2[$i] = str_replace("{delivery_company}", $row4->fields['delivery_company'], "$zawartosc2[$i]");
                } else {
                    $zawartosc2[$i] = str_replace("{delivery_company}", '-', "$zawartosc2[$i]");
                }

                $zawartosc2[$i] = str_replace("{delivery_address}", $address_deliver, "$zawartosc2[$i]");

                if ($row4->fields['billing_company'] != '') {
                    $zawartosc2[$i] = str_replace("{billing_company}", $row4->fields['billing_company'], "$zawartosc2[$i]");
                } else {
                    $zawartosc2[$i] = str_replace("{billing_company}", '-', "$zawartosc2[$i]");
                }

                $zawartosc2[$i] = str_replace("{billing_name}", $row4->fields['billing_name'], "$zawartosc2[$i]");
                $zawartosc2[$i] = str_replace("{billing_address}", $address_billing, "$zawartosc2[$i]");
                $zawartosc2[$i] = str_replace("{payment_method}", $row4->fields['payment_method'], "$zawartosc2[$i]");
                $zawartosc2[$i] = str_replace("{date_purchased}", $row4->fields['date_purchased'], "$zawartosc2[$i]");
                if ($row4->fields['shipping_method'] != PO_CHANGE_SHIPPING_FROM) {
                    $zawartosc2[$i] = str_replace("{shipping_method}", $row4->fields['shipping_method'], "$zawartosc2[$i]");
                } else {
                    $zawartosc2[$i] = str_replace("{shipping_method}", PO_CHANGE_SHIPPING_TO, "$zawartosc2[$i]");
                }

                //ustawianie odpowiednich zmiennych do posortowania w celu uzyskania odpowiedneij ilosci numerow po
                //oraz wygenerowanai odpowiednije ilosci e-maili

                $wielowymiar[$i][0] = $subk[$i]; //id_subcontractors
                $wielowymiar[$i][1] = $row4->fields['customers_id']; //id_customers
                $wielowymiar[$i][2] = $idk[$i]; //id_produktu zamowionego
                $wielowymiar[$i][3] = $zawartosc2[$i]; //zawartosc
                $wielowymiar[$i][4] = $row4->fields['orders_id']; //id_orders
                $wielowymiar[$i][5] = $row4->fields['delivery_name'] . "\n" . $address_deliver;
                $wielowymiar[$i][6] = $row4->fields['billing_name'] . "\n" . $address_billing;
                $wielowymiar[$i][7] = $row4->fields['shipping_method']; //shipping
                $row4->MoveNext(); 
            }
        }

        $p = 0;
        $byly = array();

        for ($i = 0; $i < count($wielowymiar); $i++) {
            if ($byly[$i] == false) {
                $tmpt = array();
                $rowcounttmpt = 0;
                $tmpt[$rowcounttmpt][0] = $wielowymiar[$i][0];
                $tmpt[$rowcounttmpt][1] = $wielowymiar[$i][1];
                $tmpt[$rowcounttmpt][2] = $wielowymiar[$i][2];
                $tmpt[$rowcounttmpt][3] = $wielowymiar[$i][3];
                $tmpt[$rowcounttmpt][4] = $wielowymiar[$i][4];
                $tmpt[$rowcounttmpt][5] = $wielowymiar[$i][5];
                $tmpt[$rowcounttmpt][6] = $wielowymiar[$i][6];
                $tmpt[$rowcounttmpt][7] = $wielowymiar[$i][7];
                $rowcounttmpt++;
                $byly[$i] = true;

                for ($j = $i + 1; $j < count($wielowymiar); $j++) {
                    if (($wielowymiar[$j][0] == $wielowymiar[$i][0]) && ($wielowymiar[$j][4] == $wielowymiar[$i][4])) {
                        $tmpt[$rowcounttmpt][0] = $wielowymiar[$j][0];
                        $tmpt[$rowcounttmpt][1] = $wielowymiar[$j][1];
                        $tmpt[$rowcounttmpt][2] = $wielowymiar[$j][2];
                        $tmpt[$rowcounttmpt][3] = $wielowymiar[$j][3];
                        $tmpt[$rowcounttmpt][4] = $wielowymiar[$i][4];
                        $tmpt[$rowcounttmpt][5] = $wielowymiar[$i][5];
                        $tmpt[$rowcounttmpt][6] = $wielowymiar[$i][6];
                        $tmpt[$rowcounttmpt][7] = $wielowymiar[$i][7];
                        $rowcounttmpt++;
                        $byly[$j] = true;
                    }
                }

                $tresc_ostateczna = '';
                $trescik = '';
                $newzawartosc = '';

                //wybieranie dpowiedniego produktu i dodanie go do szablonu e-mail
                //odpowiednie tagi zpliku email_products sa zastepowane zmiennymi
                $pdf = new INVOICE('P', 'mm', 'Letter');
                $pdf->Open();
                $pdf->AddPage();
                $storeaddressnocr = str_replace(STORE_NAME . chr(13) . chr(10), "", STORE_NAME_ADDRESS);
                $storeaddressnocr = str_replace(STORE_NAME . chr(13), "", $storeaddressnocr);
                $storeaddressnocr = str_replace(STORE_NAME . " ", "", $storeaddressnocr);
                $storeaddressnocr = str_replace(STORE_NAME, "", $storeaddressnocr);
                $pdf->addSociete(STORE_NAME,

                    $storeaddressnocr);
                $pdf->fact_dev(PACKING_LIST_FIRST_WORD . " ", PACKING_LIST_SECOND_WORD);
                $invdate = date("m-d-Y");
                $pdf->addDate($invdate);
                $pdf->addClient($tmpt[0][4]);
                $pdf->addClientShipAdresse($tmpt[0][5]);
                $pdf->addClientBillAdresse($tmpt[0][6]);
                $oatmeal = $db->Execute("select comments from " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = '" . zen_db_input($tmpt[0][4]) . "' order by date_added");
                $catmeow = nl2br(zen_db_output($oatmeal->fields['comments']));
                if ($catmeow != '' && $catmeow != NULL && $_POST[addcommentstoplist] == 1) {
                    $catmeow = strip_tags($catmeow);
                    $pdf->addReference($catmeow);
                }
                $cols = array(PACKING_LIST_MODEL_NUMBER => 40,
                    PACKING_LIST_PRODUCT_DESCRIPTION => 120.9,
                    PACKING_LIST_QUANTITY => 25);
                $pdf->addCols($cols);
                $cols = array(PACKING_LIST_MODEL_NUMBER => "L",
                    PACKING_LIST_PRODUCT_DESCRIPTION => "L",
                    PACKING_LIST_QUANTITY => "C");
                $pdf->addLineFormat($cols);
                $pdf->addLineFormat($cols);
                $y = 89;
                $countproductsonpo = 0;
                for ($h = 0; $h < count($tmpt); $h++) {
                    $tm = $tmpt[$h][2];
                    $tm1 = $tmpt[$h][4];
                    $row9 = $db->Execute("SELECT products_model, products_name, final_price, products_quantity, products_id FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_products_id='$tm'");
                    $trescik = $tresc_robij1;
                    $manufacturernamed = zen_get_products_manufacturers_name($row9->fields['products_id']);
                    $trescik = str_replace("{manufacturers_name}", $manufacturernamed, $trescik);
                    if (defined('POSM_MODULE_VERSION')) {
                       $row9->fields['products_name'] = preg_replace ('/\[.*\]/', '', $row9->fields['products_name']);
                    }
                    $trescik = str_replace("{products_name}", $row9->fields['products_name'], $trescik);
                    $trescik = str_replace("{products_model}", $row9->fields['products_model'], $trescik);
                    $trescik = str_replace("{final_price}", $row9->fields['final_price'], $trescik);
                    $trescik = str_replace("{products_quantity}", $row9->fields['products_quantity'], $trescik);
                    $row9a = $db->Execute("SELECT orders_id, orders_products_id, products_options, products_options_values
                        FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
                        WHERE orders_products_id='$tm' AND orders_id='$tm1'");
                    $attributes = '';
                    while (!$row9a->EOF) {
                        $attributes = $attributes . $row9a->fields['products_options'] . ": " . $row9a->fields['products_options_values'] . "\n";
                        $row9a->MoveNext();
                    }

                    $trescik = str_replace("{products_attributes}", $attributes, $trescik);

                    $tresc_ostateczna = $tresc_ostateczna . $trescik;
                    $newzawartosc = $tmpt[0][3] . $tresc_ostateczna;
                    $line = array(PACKING_LIST_MODEL_NUMBER => $row9->fields['products_model'],
                        PACKING_LIST_PRODUCT_DESCRIPTION => $row9->fields['products_name'] . " " .
                            $attributes,
                        PACKING_LIST_QUANTITY => $row9->fields['products_quantity']);
                    $size = $pdf->addLine($y, $line);
                    $y += $size + 2;
                    $countproductsonpo++;
                }
                $rowcp = $db->Execute("SELECT orders_products_id FROM " . TABLE_ORDERS_PRODUCTS . "  WHERE  orders_id='$tm1'  ")
                or die('Failed to connect database: 8');

                $countproducts = 0;
                while (!$rowcp->EOF) {
                    $countproducts++;
                    $rowcp->MoveNext();
                }
                //wybieranie addressu pczty email poddostawcy
                $dlaemaila = ($tmpt[0][0] != '0') ? $tmpt[0][0] : 0;
                $subcontractor = $db->Execute("SELECT * FROM " . TABLE_SUBCONTRACTORS_SHIPPING . " WHERE subcontractors_id='$dlaemaila'");
                $addressdo = $subcontractor->fields['email_address'];
                /* if ($dlaemaila==0) $addressdo=PO_OWN_STOCK_EMAIL; */

                $row110 = $db->Execute("SELECT max(po_number) AS num FROM " . TABLE_ORDERS_PRODUCTS);
                $kod = $row110->fields['num'] + 1;
                if ($row110->EOF) {
                    $kod = $kod . "1";
                }

                $newzawartosc = str_replace("{po_number}", $wielowymiar[$i][4] . "-" . $kod, $newzawartosc);
                $tematk = PO_SUBJECT;
                $tematk = str_replace("{po_number}", $wielowymiar[$i][4] . "-" . $kod, $tematk);
                $tematk = str_replace("{contact_person}", $subcontractor->fields['contact_person'], $tematk);
                $tematk = str_replace("{full_name}", $subcontractor->fields['name'], $tematk);
                $tematk = str_replace("{alias}", $subcontractor->fields['alias'], $tematk);
                $tematk = str_replace("{order_number}", $wielowymiar[$i][4], $tematk);
                $tematk = str_replace("{purchase_date}", $wielowymiar[$i][4], $tematk);
                // $tracking_link_1 = '<a href="' . HTTP_SERVER . DIR_WS_ADMIN . 'confirm_track_sub.php?aID=' . $dlaemaila . '&oID=' . $kod . '">' . HTTP_SERVER . DIR_WS_ADMIN . 'confirm_track_sub.php?aID=' . $dlaemaila . '&oID=' . $kod . '</a>';
                $tracking_link_1 = HTTP_SERVER . DIR_WS_ADMIN . 'confirm_track_sub.php?aID=' . $dlaemaila . '&oID=' . $kod; 

                for ($t = 0; $t <= count($tracking_link); $t++) {
                    $tracking_link_good = $tracking_link_good . str_replace("{tracking_link}", $tracking_link_1, $tracking_link[$t]);
                }

                $newzawartosc = $newzawartosc . $tracking_link_good;
                $newzawartosc = str_replace("{contact_person}", $subcontractor->fields['contact_person'], $newzawartosc);
                $newzawartosc = str_replace("{full_name}", $subcontractor->fields['name'], $newzawartosc);
                $newzawartosc = str_replace("{alias}", $subcontractor->fields['alias'], $newzawartosc);
                $newzawartosc = str_replace("{subcontractors_id}", $subcontractor->fields['subcontractors_id'], $newzawartosc);
                $newzawartosc = str_replace("{street}", $subcontractor->fields['street1'], $newzawartosc);
                $newzawartosc = str_replace("{city}", $subcontractor->fields['city'], $newzawartosc);
                $newzawartosc = str_replace("{state}", $subcontractor->fields['state'], $newzawartosc);
                $newzawartosc = str_replace("{zip}", $subcontractor->fields['zip'], $newzawartosc);
                $newzawartosc = str_replace("{telephone}", $subcontractor->fields['telephone'], $newzawartosc);
                $newzawartosc = str_replace("{email_address}", $subcontractor->fields['email_address'], $newzawartosc);
                if ($tmpt[0][7] != PO_CHANGE_SHIPPING_FROM) {

                    $newzawartosc = str_replace("{shipping_method}", $tmpt[0][7], $newzawartosc);
                } else {
                    $newzawartosc = str_replace("{shipping_method}", PO_CHANGE_SHIPPING_TO, $newzawartosc);
                }
                $passitw = $wielowymiar[$i][4];
                $row978 = $db->Execute("SELECT orders_status FROM " . TABLE_ORDERS . " WHERE orders_id='$passitw'");
                if ($row978->fields['orders_status'] == 1) {
                    $db->Execute("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY . "
                                (orders_status_id, orders_id, date_added,
                                         customer_notified, comments)
                                   values ('2','$tm1',now(),'0','" . PO_SENT_COMMENTS . "')");
                    $db->Execute("UPDATE " . TABLE_ORDERS . " SET orders_status = '2', last_modified = now() WHERE orders_id = " . $tm1);
                }
                //wysylanie e-maila
                if (PURCHASEORDERS_DEBUG == 'Yes') {
                    echo "<br>DEBUG--><br>From   :" . PO_FROM_EMAIL_NAME . " &lt;" . PO_FROM_EMAIL_ADDRESS . "&gt;<br>To     :" . $addressdo . "<br>Subject:" . $tematk . "<br>Content:<br>" . str_replace("\n", "<br>", $newzawartosc);
                }
                if ($countproductsonpo != $countproducts)
                    $pdf->addNotes(SHIPPING_OPTION . ": " . $tmpt[0][7] . "\n\n" . PO_PARTIALSHIP_PACKINGLIST . "\n" . $_POST[plistcomments] . "\n" . "PO NUMBER:" . $wielowymiar[$i][4] . "-" . $kod);
                else
                    $pdf->addNotes(SHIPPING_OPTION . ": " . $tmpt[0][7] . "\n\n" . PO_FULLSHIP_PACKINGLIST . "\n" . $_POST[plistcomments] . "\n" . "PO NUMBER:" . $wielowymiar[$i][4] . "-" . $kod);
                $pdf->Output(PO_PACKINGLIST_FILENAME, "F");
                if ($_POST[reviewthensend] == 'yes') {
                    ?>
                    <form name="editpo" action="send_pos.php" method="POST">
<?php echo zen_hide_session_id(); ?>
                    <center><?php echo REVIEW_EMAIL_EMAIL_TITLE; ?>&nbsp;<input type="text" name="etitle" size="125"
                                                                                value="<?php echo $tematk; ?>"/><br/><br/>
                        <?php echo REVIEW_EMAIL_SEND_EMAIL_TO; ?>&nbsp;<input type="text" name="eaddress" size="125"
                                                                              value="<?php echo $addressdo; ?>"/><br/><br/>
                        <textarea rows="30" name="ebody"><?php echo $newzawartosc; ?></textarea>
                        <input type="hidden" name="includepackinglistoption"
                               value="<?php echo $_POST[includepackinglistoption]; ?>"/><input type="hidden"
                                                                                               name="ereview"
                                                                                               value="yes"/><br/><br/>
                        <input class="normal_button button" type="button" value="<?php echo IMAGE_SEND; ?>"
                               name='insert' ONCLICK="javascript:document.editpo.submit();"><br/><br/>
                        <?php echo REVIEW_AND_SUBMIT_WARNING; ?></center>
                    </form>
<?php           } else {
                    $html_msg['EMAIL_MESSAGE_HTML'] = str_replace('
', '<br />', $newzawartosc);
                    if ($_POST[includepackinglistoption] == 'yes')
                        zen_mail($addressdo, $addressdo, $tematk, $newzawartosc, PO_FROM_EMAIL_NAME, PO_FROM_EMAIL_ADDRESS, $html_msg, NULL, PO_PACKINGLIST_FILENAME, 'application/pdf');
                    else
                        zen_mail($addressdo, $addressdo, $tematk, $newzawartosc, PO_FROM_EMAIL_NAME, PO_FROM_EMAIL_ADDRESS, $html_msg, NULL);
                }
                $tracking_link_good = '';
                $date = date('Y-m-d');
// unlink($pdffilename); 

// FIXME - it would be ideal if the db were not updated prior to the 
// email actually being sent, but this requires a few more changes - 
// will do this later.
                // if (!($_POST[reviewthensend] == 'yes')) {
                   for ($m = 0; $m < count($tmpt); $m++) {
                       $tm = $tmpt[$m][2];
                       $tm2 = $tmpt[$m][0];
   
                       $db->Execute("UPDATE " . TABLE_ORDERS_PRODUCTS . " SET po_sent='1', item_shipped=0, po_number='$kod', po_sent_to_subcontractor='$tm2', po_date='$date' WHERE  orders_products_id='$tm' LIMIT 1");
                   }
                // }
            }
        }
    }
    if ($_POST[reviewthensend] != 'yes') { ?>
    <tr>
        <td class="pageHeading" colspan="2"><?php echo BOX_CUSTOMERS_SEND_POS; ?><br><br></td>
    </tr>
    <tr>
        <td valign="top"><?php echo REFRESH_WARNING; ?></td>
    </tr>
    <tr>
        <td valign="top"><br>

            <?php
            if ($_POST[co] == 'old') {
                function sub2()
                {
                    global $db;
                    $row22 = $db->Execute("SELECT subcontractors_id,alias FROM " . TABLE_SUBCONTRACTORS_SHIPPING . " ORDER BY alias")
                    or die('Failed to connect database: ');

                    echo "<select name='sub11'>" .
                        "<option value='%'>" . TABLE_ALL_SUBCONTRACTORS . "</option>";
                    while (!$row22->EOF) {
                        echo "<option value='" . $row22->fields['subcontractors_id']. "'>" . $row22->fields['alias'] . "</option>";
                        $row22->MoveNext();
                    }
                    echo '</select>';
                }

//przejscie do szbalonu ktory wyswietla wyslane juz e-maile z starymi numerami po
                echo "<form name='drugi' action='send_pos.php' method='post'>
                   <input class='normal_button button' type='submit' name='old' value='" . BUTTON_NEW . "'>
                   </form><br>&nbsp;";
//wyszukiwarka
                echo '<form name="wyszukiwarka" action="send_pos.php" method="POST">
                   <input type="hidden" name="co" value="old">
                   <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                   <tr><td align="center" colspan="2">' . TABLE_DATA_FROM_DATES . ': <input type="text" size="20" name="data_od"> ' . TABLE_TO . ' <input type="text" size="10" name="data_do">
                                &nbsp;&nbsp;' . TABLE_PO_PREOVIOUS_NUMBER . ': <input type="text" size="10" name="po_number">&nbsp;&nbsp;
                                ' . TABLE_ORDER_NUMBER . ': <input type="text" size="10" name="orders_num">&nbsp;&nbsp;
                                ' . TABLE_SUBCONTRACTOR . ': ';
                sub2();
                echo '&nbsp;&nbsp;' . TABLE_SHOW_DELIVERED_ORDERS . ' <input type="checkbox" name="showdeliv" value="1">&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="co2" value="wyswietl"></form><input type="image" src="includes/languages/english/images/buttons/button_search.gif" name="insert1" ONCLICK="javascript:document.wyszukiwarka.submit();"><br><br></td></tr></table></form>';


            } else {

                echo "<form name='pierwszy' action='send_pos.php' method='post'>
                   <input class='normal_button button' type='submit' name='old' value='" . BUTTON_OLD . "'>
                   <input type='hidden' name='co' value='old'>
                   </form><br>&nbsp;";
            }

            //wyglad szablonu dla staryc po
            if ($_POST[co] == 'old') {
                ?>
                <table border="0" width="100%" align="center" cellspacing="0" cellpadding="0">
                <tr class="dataTableHeadingRow">
                    <td width='12%' class="dataTableHeadingContent" align="center">
                        <?php echo TABLE_ORDER_NUMBER; ?>
                    </td>
                    <td width='12%' class="dataTableHeadingContent" align="center">
                        <?php echo TABLE_CUSTOMER_NAME; ?><br>

                    </td>
                    <td width='12%' class="dataTableHeadingContent" align="center">
                        <?php echo TABLE_PRODUCTS_NAME; ?><br>


                    </td>
                    <td width='12%' class="dataTableHeadingContent" align="center">
                        <?php echo TABLE_SEND_PO; ?><br>
                    </td>
                    <td width='12%' class="dataTableHeadingContent" align="center">
                        <?php echo TABLE_PO_SUBCONTRACTOR; ?><br>

                    </td>
                    <td width='12%' class="dataTableHeadingContent" align="center">
                        <?php echo TABLE_PO_PREOVIOUSLY_SENT_TO; ?><br>

                    </td>
                    <td width='12%' class="dataTableHeadingContent" align="center">
                        <?php echo TABLE_PO_PREOVIOUS_NUMBER; ?><br>

                    </td>
                    <td width='12%' class="dataTableHeadingContent" align="center">
                        <?php echo TABLE_PO_WHEN_SEND; ?><br>

                    </td>
                </tr>
                <form name='pos' method='post' action='send_pos.php?resend=yes'>


                <?php
//pobieranie danych ktore sa wprowadzone w wyszukiwarce
                if (isset($_POST[co2]) AND $_POST[co2] == 'wyswietl') {

                    if ($_POST[data_od] != '') {
                        $data_od = zen_db_prepare_input($_POST[data_od]);
                        $zmienna2 = "AND (p.po_date>='$data_od')";
                    } else {
                        $zmienna2 = '';
                    }

                    if ($_POST[data_do] != '') {
                        $data_do = zen_db_prepare_input($_POST[data_do]);
                        $zmienna1 = "AND (p.po_date<='$data_do')";
                    } else {
                        $zmienna1 = '';
                    }

                    if ($_POST[showdeliv] != '1') {
                        $zmienna3 = "AND (o.orders_status != 3)";
                    } else {
                        $zmienna3 = '';
                    }

                    if ($_POST[po_number] != '') {
                        $po_number = (int)$_POST[po_number];
                    } else {
                        $po_number = '%';
                    }

                    if ($_POST[orders_num] != '') {
                        $orders_num = (int)$_POST[orders_num];
                    } else {
                        $orders_num = '%';
                    }

                    if ($_POST[sub11] != '') {
                        $sub1 = zen_db_prepare_input($_POST[sub11]);
                    } else {
                        $sub1 = '%';
                    }


//generowanie pola typu select ktory ma za zadanie wyswietlanie odpowiedniego subcontracotra dla odpowiedniego produktu
                    function sub($name, $i)
                    {
                        global $db;
                        $row22 = $db->Execute("SELECT  subcontractors_id,alias FROM " . TABLE_SUBCONTRACTORS_SHIPPING . " ORDER BY alias");
                        $row232 = $db->Execute("SELECT products_id, default_subcontractor FROM " . TABLE_PRODUCTS . " WHERE products_id='$name'");

                        echo "<select name='sub$i'>";
                        while (!$row22->EOF) {

                            echo "<option value='" . $row22->fields['subcontractors_id'] . "'";

                            if ($row232->fields['default_subcontractor'] == $row22->fields['subcontractors_id']) {
                                echo "selected";
                            }
                            echo ">" . $row22->fields['alias'] . "</option>";
                            $row22->MoveNext();
                        }
                        echo "</select>";
                    }


                    $row2query = "SELECT p.orders_products_id, p.orders_id, p.orders_products_id, p.products_name, p.po_number,  p.po_sent_to_subcontractor, p.products_id, p.po_date  FROM " . TABLE_ORDERS_PRODUCTS . " as p, " . TABLE_ORDERS . " as o WHERE  p.orders_id=o.orders_id AND p.po_sent='1'"; 
                    if ($orders_num != '%') {
                        $row2query .= " AND  (p.orders_id LIKE '$orders_num') ";
                    }
                    if ($po_number != '%') {
                        $row2query .= " AND (p.po_number = " . (int)$po_number . ") "; 
                    }
                    if ($sub1 != '%') {
                        $row2query .= " AND  (p.po_sent_to_subcontractor LIKE '$sub1') ";  
                    }
                    $row2query .= " " . $zmienna2 . " " . $zmienna1 . " " . $zmienna3 . " " . "ORDER BY orders_id DESC";
                    $row2 = $db->Execute($row2query); 

//wyswietlanie danych
                    $i = 1;
                    while (!$row2->EOF) {
                        $row3 = $db->Execute("SELECT * FROM " . TABLE_ORDERS . " as o, " . TABLE_ORDERS_PRODUCTS . " as p WHERE o.orders_id = o.orders_id AND o.orders_id=" . (int)$row2->fields['orders_id']);
                        $row100 = $db->Execute("SELECT alias FROM " . TABLE_SUBCONTRACTORS_SHIPPING . " WHERE subcontractors_id=" . (int)$row2->fields['po_sent_to_subcontractor']);

                        if (defined('POSM_MODULE_VERSION')) {
                            $row2->fields['products_name'] = preg_replace ('/\[.*\]/', '', $row2->fields['products_name']);
                        }
                        if ($i % 2 == 1) {

                            echo "<tr class='dataTableRowSelected'>" .
                                "<td  align='center'>" . $row2->fields['orders_id'] . "</td><td  align='center'>" . $row3->fields['customers_name'] . "</td><td  align='center'>". $row2->fields['products_name'] . "</td><td align='center'><input type='checkbox' name='pos" . $i. "'>" .

                                "</td><td  align='center'>";
                            sub($row2->fields['products_id'], $i);
                            echo "</td>" .
                                "<td  align='center'>" . $row100->fields['alias'] . "</td>" .
                                "<td  align='center'>" . $row2->fields['po_number'] . "</td>" .
                                "<td  align='center'>" . $row2->fields['po_date'] . "</td>" .
                                "</tr><input type='hidden' name='opi". $i. "' value='" .$row2->fields['orders_products_id']. "'><input type='hidden' name='id" . $i . "' value=" . $row2->fields['orders_products_id'] . ">";

                        }

                        if ($i % 2 == 0) {

                            echo "<tr class='dataTableRow'>" .
                                "<td  align='center'>" . $row2->fields['orders_id'] . "</td><td  align='center'>" . $row3->fields['customers_name'] . "</td><td  align='center'>" . $row2->fields['products_name']. "</td><td align='center'><input type='checkbox' name='pos" . $i . "'>" .

                                "</td><td  align='center'>";
                            sub($row2->fields['products_id'], $i);
                            echo "</td>" .
                                "<td  align='center'>" . $row100->fields['alias'] . "</td>" .
                                "<td  align='center'>" . $row2->fields['po_number'] .  "</td>" .
                                "<td  align='center'>" . $row2->fields['po_date'] . "</td>" .
                                "</tr><input type='hidden' name='opi" . $i . "' value='" . $row2->fields['orders_products_id'] . "'><input type='hidden' name='id" . $i ."' value=" . $row2->fields['orders_products_id'] . ">";

                        }

                        $i++;
                        $row2->MoveNext();
                    }
                    echo "<input type='hidden' name='krotnosc' value='$i'>";


                    ?><input type='hidden' name='what' value='send'>
                    <tr>
                        <td colspan='8' align='center'><br><br></td>
                    </tr>
                    <tr>
                        <td colspan='8' align='center'><?php echo TABLE_COMMENTS_FOR_POS; ?>:&nbsp;<input type="text"
                                                                                                          name="posubcomments"
                                                                                                          size="90">
                        </td>
                    </tr>
                    <tr>
                        <td colspan='8' align='center'><br><br></td>
                    </tr>
                    <?php if (PO_SEND_PACKING_LISTS != 0) { ?>
                        <tr>
                        <td colspan='8' align='center'><?php echo TABLE_COMMENTS_FOR_PACKING_LISTS; ?>:&nbsp;<input
                                type="text" name="plistcomments" size="90" maxlength="90"></td></tr><?php } ?>
                    <tr>
                        <td colspan='8' align='center'><br></td>
                    </tr>
                    <tr>
                        <td colspan='8' align='center'><?php echo COMMENTS_WARNING; ?></td>
                    </tr>
                    <tr>
                        <td colspan='8' align='center'><br><br></td>
                    </tr>
                    <tr>
                        <td colspan='8' align='center'>
                            <?php if (PO_SEND_PACKING_LISTS == 0) { ?>
                                <input type="hidden" name="includepackinglistoption" value="no"> <?php } ?>
                            <?php if (PO_SEND_PACKING_LISTS == 1) { ?>
                                <input type="hidden" name="includepackinglistoption" value="yes"> <?php } ?>
                            <?php if (PO_SEND_PACKING_LISTS == 2) { ?>
                                <?php echo TABLE_INCLUDE_PACKINGLIST_OPTION; ?><input type="checkbox"
                                                                                      name="includepackinglistoption"
                                                                                      value="yes"
                                                                                      CHECKED>&nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
                            <?php if (PO_SEND_PACKING_LISTS == 3) { ?>
                                <?php echo TABLE_INCLUDE_PACKINGLIST_OPTION; ?><input type="checkbox"
                                                                                      name="includepackinglistoption"
                                                                                      value="yes">&nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
                            <?php if (PO_SEND_PACKING_LISTS != 0) { ?>
                                <?php echo TABLE_ADD_CUSTOMERS_COMMENTS_TO_PACKING_LIST; ?><input type="checkbox"
                                                                                                  name="addcommentstoplist"
                                                                                                  value="1"
                                                                                                  CHECKED>&nbsp;&nbsp;&nbsp;&nbsp;<?php }
                            echo TABLE_REVIEW_EMAIL_OPTION; ?><input type="checkbox" name="reviewthensend" value="yes"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
                                class="normal_button button" type="button" value="<?php echo IMAGE_SEND; ?>"
                                name='insert' ONCLICK="javascript:document.pos.submit();"></td>
                    </tr></form>

                    </table>
                <?php }


            } else {
//generowanie szablonu dla nie wysllanych numerow po
                ?>
                <table border="0" width="100%" align="center" cellspacing="0" cellpadding="0">
                    <tr class="dataTableHeadingRow">
                        <td width='10%' class="dataTableHeadingContent" align="center">
                            <?php echo TABLE_ORDER_NUMBER; ?>
                        </td>

                        <td width='20%' class="dataTableHeadingContent" align="center">
                            <?php echo TABLE_ORDER_COMMENTS; ?><br>
                        </td>
                        <td width='15%' class="dataTableHeadingContent" align="center">
                            <?php echo TABLE_ORDER_SHIPPING; ?><br>

                        </td>
                        <td width='15%' class="dataTableHeadingContent" align="left">
                            <?php echo TABLE_ORDER_ADDRESS; ?><br>

                        </td>
                        <td width='10%' class="dataTableHeadingContent" align="left">
                            <?php echo TABLE_ORDER_PRODUCT_MANUFACTURER; ?><br>


                        </td>
                        <td width='15%' class="dataTableHeadingContent" align="left">
                            <?php echo TABLE_PRODUCTS_NAME; ?><br>


                        </td>
                        <td width='5%' class="dataTableHeadingContent" align="center">
                            <?php echo TABLE_SEND_PO; ?><br>
                        </td>
                        <td width='10%' class="dataTableHeadingContent" align="center">
                            <?php echo TABLE_PO_SUBCONTRACTOR; ?><br>

                        </td>

                    </tr>
                    <form name='pos' method='post' action='send_pos.php'>
                        <?php


                        function sub($name, $i)
                        {
                            global $db;
                            $row22 = $db->Execute("SELECT  subcontractors_id,alias FROM " . TABLE_SUBCONTRACTORS_SHIPPING . " ORDER BY alias");
                            $row232 = $db->Execute("SELECT products_id, default_subcontractor FROM " . TABLE_PRODUCTS . " WHERE products_id='$name'");

                            echo "<select name='sub" . $i. "'>";
                            while (!$row22->EOF) {
                                echo "<option value='" . $row22->fields['subcontractors_id'] . "'";

                                if ($row232->fields['default_subcontractor'] == $row22->fields['subcontractors_id']) {
                                    echo "selected";
                                }
                                echo ">" . $row22->fields['alias'] ."</option>";
                                $row22->MoveNext(); 
                            }
                            echo "</select>";
                        }


                        $a = $_GET["a"];
                        $l_odp_napasku = '10';
                        $l_odp_nastronie = '10';
                        $start = $a * $l_odp_nastronie;

                        $skrypt = "send_pos.php?";


                        $count_query = "SELECT p.orders_products_id, p.orders_id, p.orders_products_id, p.products_name, p.products_id, o.shipping_method, o.delivery_state, p.products_quantity, o.delivery_street_address, o.delivery_city, o.delivery_suburb, o.delivery_postcode, o.delivery_country, o.delivery_company, o.delivery_name, p.products_model FROM " . TABLE_ORDERS_PRODUCTS . " as p, " . TABLE_ORDERS . " as o WHERE  p.orders_id=o.orders_id AND po_sent='0' AND o.orders_status != 3 AND po_number  IS NULL";
                        $queryxx = $db->Execute($count_query); 

                        $l_odp = $queryxx->RecordCount();

                        $show_query = $count_query .  " ORDER BY p.orders_id DESC LIMIT $start, $l_odp_nastronie";
                        $row2 = $db->Execute($show_query); 

                        $i = 1;
                        while (!$row2->EOF) {
                            $rowpa = $db->Execute("SELECT orders_id, orders_products_id, products_options, products_options_values
                                FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . "
                                WHERE orders_products_id=" . $row2->fields['orders_products_id'] . " AND orders_id= "  .$row2->fields['orders_id']);
                            if (defined('POSM_MODULE_VERSION')) {
                               $row2->fields['products_name'] = preg_replace ('/\[.*\]/', '', $row2->fields['products_name']);
                            }
                            $attributes = '';
                            while (!$rowpa->EOF) {
                                $attributes = $attributes . "<br />" . $rowpa->fields['products_options'] . ": " . $rowpa->fields['products_options_values'];
                                $rowpa->MoveNext();
                            }


                            $oatmeal = $db->Execute("select comments from " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = '" . zen_db_input($row2->fields['orders_id']) . "' order by date_added");
                            $catmeow = nl2br(zen_db_output($oatmeal->fields['comments']));
                            $manufacturernamed = zen_get_products_manufacturers_name($row2->fields['products_id']);

                            if ($row2->fields['delivery_country'] == zen_get_country_name(STORE_COUNTRY))
                                $orderaddresscountry = "";
                            else
                                $orderaddresscountry = "<br />" . $row2->fields['delivery_country'];
                            if ($row2->fields['delivery_suburb'] == "" || $row2->fields['delivery_suburb'] == NULL)
                                $orderaddresssuburb = "";
                            else
                                $orderaddresssuburb = "<br />" . $row2->fields['delivery_suburb'];
                            if ($row2->fields['delivery_company'] == "" || $row2->fields['delivery_company'] == NULL)
                                $orderaddresscompany = "";
                            else
                                $orderaddresscompany = $row2->fields['delivery_company'] . "<br />";
                            $ordersaddress = $orderaddresscompany . $row2->fields['delivery_street_address'] . $orderaddresssuburb . "<br />" . $row2->fields['delivery_city'] . ", " . $row2->fields['delivery_state'] . " " . $row2->fields['delivery_postcode'] . $orderaddresscountry;
                            if ($i % 2 == 1)
                                echo "<tr class='dataTableRowSelected'>";
                            else
                                echo "<tr class='dataTableRow'>";
                            echo "<td  align='center'>" . $row2->fields['orders_id'] . "</td><td  align='center'>" . $catmeow . "</td><td  align='center'>" . $row2->fields['shipping_method'] . "</td><td  align='left'>" . $row2->fields['delivery_name'] . "<br />" . $ordersaddress . "</td><td  align='left'>" . $manufacturernamed . "<br />" . $row2->fields['products_model'] . "</td><td  align='left'>" . $row2->fields['products_quantity'] . "x" . $row2->fields['products_name'] . $attributes . "</td><td align='center'><input type='checkbox' name='pos" . $i. "'>" .

                                "</td><td  align='center'>";
                            sub($row2->fields['products_id'], $i);
                            echo "</td>" .
                                "</tr><input type='hidden' name='opi$i' value=" . $row2->fields['orders_products_id']. "><input type='hidden' name='id" . $i. "' value=" . $row2->fields['orders_products_id']. ">";
                            $i++;
                            $row2->MoveNext();
                        }
                        echo "<input type='hidden' name='krotnosc' value='" . $i ."'>";


                        ?><input type='hidden' name='what' value='send'>
                        <?php pasek($l_odp, $l_odp_nastronie, $l_odp_napasku, $skrypt, $a); ?>
                        <tr>
                            <td colspan='9' align='center'><br><br></td>
                        </tr>
                        <tr>
                            <td colspan='9' align='center'><?php echo TABLE_COMMENTS_FOR_POS; ?>:&nbsp;<input
                                    type="text" name="posubcomments" size="90"></td>
                        </tr>
                        <tr>
                            <td colspan='9' align='center'><br><br></td>
                        </tr>
                        <?php if (PO_SEND_PACKING_LISTS != 0) { ?>
                            <tr>
                            <td colspan='9' align='center'><?php echo TABLE_COMMENTS_FOR_PACKING_LISTS; ?>:&nbsp;<input
                                    type="text" name="plistcomments" size="90" maxlength="90"></td></tr><?php } ?>
                        <tr>
                            <td colspan='9' align='center'><br></td>
                        </tr>
                        <tr>
                            <td colspan='9' align='center'><?php echo COMMENTS_WARNING; ?></td>
                        </tr>
                        <tr>
                            <td colspan='9' align='center'><br><br></td>
                        </tr>
                        <tr>
                            <td colspan='8' align='center'>
                                <?php if (PO_SEND_PACKING_LISTS == 0) { ?>
                                    <input type="hidden" name="includepackinglistoption" value="no"> <?php } ?>
                                <?php if (PO_SEND_PACKING_LISTS == 1) { ?>
                                    <input type="hidden" name="includepackinglistoption" value="yes"> <?php } ?>
                                <?php if (PO_SEND_PACKING_LISTS == 2) { ?>
                                    <?php echo TABLE_INCLUDE_PACKINGLIST_OPTION; ?><input type="checkbox"
                                                                                          name="includepackinglistoption"
                                                                                          value="yes"
                                                                                          CHECKED>&nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
                                <?php if (PO_SEND_PACKING_LISTS == 3) { ?>
                                    <?php echo TABLE_INCLUDE_PACKINGLIST_OPTION; ?><input type="checkbox"
                                                                                          name="includepackinglistoption"
                                                                                          value="yes">&nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
                                <?php if (PO_SEND_PACKING_LISTS != 0) { ?>
                                    <?php echo TABLE_ADD_CUSTOMERS_COMMENTS_TO_PACKING_LIST; ?><input type="checkbox"
                                                                                                      name="addcommentstoplist"
                                                                                                      value="1"
                                                                                                      CHECKED/>&nbsp;&nbsp;&nbsp;&nbsp;<?php }
                                echo TABLE_REVIEW_EMAIL_OPTION; ?><input type="checkbox" name="reviewthensend"
                                                                         value="yes"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
                                    class="normal_button button" type="button" value="<?php echo IMAGE_SEND; ?>"
                                    name='insert' ONCLICK="javascript:document.pos.submit();"></td>
                        </tr>
                    </form>
                    <tr>
                        <td colspan='9' align='center'><br><br></td>
                    </tr>
                </table>
            <?php } ?>
        </td>

    </tr>
</table>

<?php }
require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
