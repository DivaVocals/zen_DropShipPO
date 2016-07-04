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
// Number of rows on the page
define('DROPSHIP_MAX_ROWS', 200);
// Whether to show inactive products
define('DROPSHIP_SHOW_INACTIVE_PRODUCTS', false); 
// How to do the sorting - if true, use model sorting, false use DROPSHIP_SORT
define('DROPSHIP_USE_MODEL_SORT', false); 
define('DROPSHIP_SORT', ' manufacturers_name, products_model '); 
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
    <?php
    //delete


    if (DROPSHIP_USE_MODEL_SORT) { 
       if (isset($_GET['list_order'])) {
           if ($_GET['list_order'] == 'modelname') $disp_order = "products_model ASC";
           if ($_GET['list_order'] == 'modelnamedesc') $disp_order = "products_model DESC";
   
   
       } else {
   
           $disp_order = "products_id ASC";
       }
    } else {
           $disp_order = DROPSHIP_SORT;
    }


    if (isset($_POST[krotnosc])) {

        $krotnosc = $_POST[krotnosc];

        for ($i = 1; $i <= $krotnosc; $i++) {
            $id_product[$i] = $_POST[$i];

        }

//zapisywanie wszytskich ustawien dla odpowiednich wierszy w tabeli products

        for ($i = 1; $i < count($id_product); $i++) {

            $sub = "sub" . $id_product[$i];
            if ($_POST[$sub] != 'ownstock') {
                $numer = $_POST[$sub];
                $db->Execute("UPDATE " . TABLE_PRODUCTS . " SET default_subcontractor='$numer' WHERE  products_id ='$id_product[$i]' LIMIT 1");
            } else {
                $numer = "0";
                $db->Execute("UPDATE " . TABLE_PRODUCTS . " SET default_subcontractor='$numer' WHERE  products_id ='$id_product[$i]' LIMIT 1");

            }
        }


    }
    //projekt szablonu

    ?>
    <tr>
        <td class="pageHeading" colspan="2"><br><?php echo TABLE_SET_SUBC_HEADING; ?><br><br></td>
    </tr>
    <tr>
        <td valign="top" width='80%'>
            <table border="0" width='100%' cellspacing="2" cellpadding="0">
                <tr class="dataTableHeadingRow">
<?php if (DROPSHIP_USE_MODEL_SORT == true) { ?> 
                    <td width='5%' class="dataTableHeadingContent" align="left" valign="top">
                        <?php echo ID; ?>
                    </td>
                    <td width='20%' class="dataTableHeadingContent" align="left">
                        <?php echo TABLE_SET_SUBC_MODEL; ?><br>
                        <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=modelname'); ?>"><?php echo($_GET['list_order'] == 'modelname' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                        <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=modelnamedesc'); ?>"><?php echo($_GET['list_order'] == 'modelnamedesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                    </td>
                    <td width='20%' class="dataTableHeadingContent" align="left" valign="top">
                        <?php echo TABLE_SET_SUBC_PRODUCTS_NAME; ?><br>
                    </td>
                    <td width='20%' class="dataTableHeadingContent" align='left' valign="top">
                        <?php echo TABLE_SET_SUBC_MANUFACTURER; ?><br>
                    </td>

                    <td width='20%' class="dataTableHeadingContent" align="left" valign='top'>
                        <?php echo TABLE_SET_SUBC_DEFAULT; ?><br>
                    </td>
<?php  } else { ?>
                    <td width='20%' class="dataTableHeadingContent" align='left' valign="top">
                        <?php echo TABLE_SET_SUBC_MANUFACTURER; ?><br>
                    </td>
                    <td width='20%' class="dataTableHeadingContent" align="left">
                        <?php echo TABLE_SET_SUBC_MODEL; ?><br>
                    </td>
                    <td width='5%' class="dataTableHeadingContent" align="left" valign="top">
                        <?php echo ID; ?>
                    </td>
                    <td width='20%' class="dataTableHeadingContent" align="left" valign="top">
                        <?php echo TABLE_SET_SUBC_PRODUCTS_NAME; ?><br>
                    </td>

                    <td width='20%' class="dataTableHeadingContent" align="left" valign='top'>
                        <?php echo TABLE_SET_SUBC_DEFAULT; ?><br>
                    </td>

<?php  } ?>

                </tr>
                <form name='set' action='set_subcontrac.php?' method='post'>

                    <?php
                    // Display type of field
                    function sub($name)
                    {
                        global $db;
                        $row33 = $db->Execute("SELECT  default_subcontractor, products_id FROM " . TABLE_PRODUCTS . " WHERE products_id='$name'");

                        echo "<select name='sub$name'>";

                        $row22 = $db->Execute("SELECT subcontractors_id,alias FROM " . TABLE_SUBCONTRACTORS_SHIPPING . " ORDER BY alias");
                        while (!$row22->EOF) {
                            if ($row22->fields['subcontractors_id'] == $row33->fields['default_subcontractor']) {
                                echo "<option value='" . $row22->fields['subcontractors_id'] ."' selected>" . $row22->fields['alias'] . "</option>";
                            } else {
                                echo "<option value='" . $row22->fields['subcontractors_id']. "'>" . $row22->fields['alias'] . "</option>";
                            }
                            $row22->MoveNext();
                        }
                        echo "</select>";
                    }

                    // generate and assign variables for pagination
                    $a = $_GET["a"];
                    $l_odp_napasku = '10';
                    $start = $a * DROPSHIP_MAX_ROWS;
                    $i = 0;
                    if (DROPSHIP_SHOW_INACTIVE_PRODUCTS) {
                       $where = " 1=1 "; 
                    } else { 
                       $where = " products_status = 1 "; 
                    } 

                    // query the records 
                    $row2 = $db->Execute("SELECT products_id, master_categories_id, products_type, products_model, manufacturers_name FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id WHERE " . $where . " order by $disp_order LIMIT $start, " . DROPSHIP_MAX_ROWS);
                    $query33 = $db->Execute("SELECT products_id, products_model, manufacturers_id FROM " . TABLE_PRODUCTS . " WHERE " . $where);

                    $l_odp = $query33->RecordCount();

                    $row4 = $db->Execute("SELECT MAX(products_id) as max FROM " . TABLE_PRODUCTS . " LIMIT $start, " . DROPSHIP_MAX_ROWS);

                    echo "<input type='hidden' name='ilosc' value='$row4->fields['max']'>";

                    // display the records
                    $i = 1;
                    while (!$row2->EOF) {
                        $row5 = $db->Execute("SELECT products_name  FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_id=" . $row2->fields['products_id']);

                        $manuf_id = $row2->fields['manufacturers_id']; 

                        $cPath = $row2->fields['master_categories_id'];
                        $type_handler = $zc_products->get_admin_handler($row2->fields['products_type']);
                        $prid_link = '<a href="' . zen_href_link($type_handler , 'product_type=' . $row2->fields['products_type'] . '&cPath=' . $cPath . '&pID=' . $row2->fields['products_id'] . '&action=new_product') . '">' .$row2->fields['products_id'] . '</a>'; 
                        if ($i % 2 == 1) {
                          echo "<tr class='dataTableRow'>"; 
                          if (DROPSHIP_USE_MODEL_SORT == true) { 
                            echo "<td align='left'>" . $prid_link . "</td><td align='left'>" . $row2->fields['products_model'] . "</td><td align='left'>" . $row5->fields['products_name'] . "</td><td  align='left'>" . $row2->fields['manufacturers_name'] . "</td><td align='left'>";
                          } else {
                            echo "<td align='left'>" . $row2->fields['manufacturers_name'] . "</td><td align='left'>" . $row2->fields['products_model'] . "</td><td align='left'>" . $prid_link . "</td><td  align='left'>" . $row5->fields['products_name'] . "</td><td align='left'>";
                          }
                          sub($row2->fields['products_id']);
                          echo "</td>";
                          echo "</tr><input type='hidden' name='$i' value='" . $row2->fields['products_id']. "'>";
                        }

                        if ($i % 2 == 0) {
                          echo "<tr class='dataTableRowSelected'>"; 
                          if (DROPSHIP_USE_MODEL_SORT == true) { 
                            echo "<td align='left'>" . $prid_link . "</td><td align='left'>" . $row2->fields['products_model'] . "</td><td align='left'>" . $row5->fields['products_name'] . "</td><td  align='left'>". $row2->fields['manufacturers_name'] . "</td><td align='left'>";
                          } else { 
                            echo "<td align='left'>" . $row2->fields['manufacturers_name'] . "</td><td align='left'>" . $row2->fields['products_model'] . "</td><td align='left'>" . $prid_link . "</td><td  align='left'>" . $row5->fields['products_name'] . "</td><td align='left'>";
                          }
                            sub($row2->fields['products_id']);
                            echo "</td>" . 
                                "</tr><input type='hidden' name='$i' value='" . $row2->fields['products_id'] . "'>";
                            echo "</td>" .
                                "</tr><input type='hidden' name='" . $i ."' value='" . $row2->fields['products_id']. "'>";
                        }
                        $i++;
                        $row2->MoveNext(); 
                    }
                    echo "<input type='hidden' name='krotnosc' value='" . $i. "'>";

                    //ustawienie adresu
                    $skrypt = "set_subcontrac.php?";
                    //uruchomienie funkcji porcjujacej dane
                    pasek($l_odp, DROPSHIP_MAX_ROWS, $l_odp_napasku, $skrypt, $a);
                    ?>
                </form>
                <tr>
                    <td colspan='5' align='left'><br></td>
                </tr>
                <tr>
                    <td colspan='6' align='center'><input class="normal_button button" type="button"
                                                          value="<?php echo IMAGE_SAVE; ?>" name='insert'
                                                          ONCLICK="javascript:document.set.submit();"></td>
                </tr>
                </td>
                </tr>

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
