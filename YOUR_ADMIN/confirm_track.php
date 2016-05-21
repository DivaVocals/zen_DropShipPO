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
//  $Id: confirm_track.php 
//

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');

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
<!-- body //-->
<table border="0" width='100%' cellspacing="0" cellpadding="0">

    <!-- body_text //-->


    <tr>
        <td class="pageHeading" colspan="2"><?php echo HEADING_TITLE_TRACKING; ?><br><br></td>
    </tr>
    <td valign="top">
        <?php
        $sorder = $_GET[sorder];
        if ($sorder == 1)
            echo "<a href='" . HTTP_SERVER . DIR_WS_ADMIN . "confirm_track.php'>" . SHOW_OLDEST_PO_FIRST . "</a>";
        else
            echo "<a href='" . HTTP_SERVER . DIR_WS_ADMIN . "confirm_track.php?sorder=1'>" . SHOW_NEWEST_PO_FIRST . "</a>";
        ?></td>
    <tr>
        <td valign="top">
            <table border="0" width="100%" align="center" cellspacing="0" cellpadding="0">
                <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent" align="center" valign="top">
                        <?php echo NUMBER_POS_TRACKING; ?>
                    </td>
                    <td class="dataTableHeadingContent" align="center" valign="top">
                        <?php echo DATA_POS_TRACKING; ?>
                    </td>
                    <td class="dataTableHeadingContent" align="center" valign="top">
                        <?php echo PO_SENT_TO_NAME; ?>
                    </td>
                    <td class="dataTableHeadingContent" align="center" valign="top">
                        <?php echo DELIVER_NAME_TRACKING; ?>
                    </td>
                </tr>
                <?php

                $sort_orders = array();
                if ($sorder == 1)
                    $query210b = $db->Execute("SELECT p.po_number FROM " . TABLE_ORDERS_PRODUCTS . " as p, " . TABLE_ORDERS . " as o WHERE p.orders_id=o.orders_id AND p.po_sent=1 AND p.item_shipped=0 AND o.orders_status !=3 ORDER by p.orders_id DESC")
                    or die("Nie mozna sie polaczyc z baza danych");
                else
                    $query210b = $db->Execute("SELECT p.po_number FROM " . TABLE_ORDERS_PRODUCTS . " as p, " . TABLE_ORDERS . " as o WHERE p.orders_id=o.orders_id AND p.po_sent=1 AND p.item_shipped=0 AND o.orders_status !=3 ORDER by p.orders_id ASC")
                    or die("Nie mozna sie polaczyc z baza danych");
                $p = 0;
                while (!$query210b->EOF) {
                    $row210b = $query210b->fields;
                    $sort_orders[$p] = $row210b['po_number'];
                    $p++;
                    $query210b->MoveNext();
                }


                $temp = array_unique($sort_orders);
                $wyjscie = array_values($temp);

                for($h=0; $h<count($wyjscie); $h++)
                {
                         $row = $db->Execute("SELECT orders_id, po_number, po_date, po_sent_to_subcontractor FROM ".TABLE_ORDERS_PRODUCTS." WHERE po_sent=1 AND item_shipped=0 AND po_number='$wyjscie[$h]'");
                         if ($row->EOF) { 
                            continue; 
                         }
                         $i=0;
                         $row1=$db->Execute("SELECT delivery_name, delivery_company, delivery_street_address, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_suburb FROM ".TABLE_ORDERS." WHERE orders_id= " . (int) $row->fields['orders_id']);

                         $subcontractor = $db->Execute("SELECT name FROM ".TABLE_SUBCONTRACTORS_SHIPPING." WHERE subcontractors_id = " . (int)$row->fields['po_sent_to_subcontractor']);
                         if($h%2==1)
                            echo "<tr class='dataTableRowSelected'>";
                         else
                            echo "<tr class='dataTableRow'>";
                ?>
                                <td align="center" valign="center">
                                  <?php  echo "<a href='".HTTP_SERVER.DIR_WS_ADMIN."confirm_track_sub.php?aID=".$row->fields['po_sent_to_subcontractor']."&oID=".$row->fields['po_number']."' target='_blank'>".$row->fields['orders_id']."-".$row->fields['po_number']."</a>"; ?>
                                </td>
                             <td align="center" valign="center">
                                  <?php  echo $row->fields['po_date']; ?>
                                </td>
                  <td align="center" valign="center">
                                  <?php  echo $subcontractor->fields['name']; ?>
                                </td>
                            <td align="center" valign="top">
                                  <?php
                if ($row1->fields['delivery_country'] == zen_get_country_name(STORE_COUNTRY))
                   $orderaddresscountry="";
                else
                   $orderaddresscountry="<br />".$row1->fields['delivery_country'];
                if ($row1->fields['delivery_suburb'] == "" ||
                    $row1->fields['delivery_suburb'] == NULL)
                   $orderaddresssuburb="";
                else
                   $orderaddresssuburb="<br />".$row1->fields['delivery_suburb'];
                if ($row1->fields['delivery_company'] == "" ||
                    $row1->fields['delivery_company'] == NULL)
                   $orderaddresscompany="";
                else
                   $orderaddresscompany=$row1->fields['delivery_company']."<br />";
                echo $row1->fields['delivery_name']."<br />".$orderaddresscompany.$row1->fields['delivery_street_address'].$orderaddresssuburb."<br />".$row1->fields['delivery_city'].", ".$row1->fields['delivery_state']." ".$row1->fields['delivery_postcode']."<br />".$orderaddresscountry;
                                ?>
                                </td>
                               </tr>
                            <?php
                            }
                ?>
            </table>
        </td>

    </tr>
</table>

<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
