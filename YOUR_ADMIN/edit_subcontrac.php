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
// pobieranie rozkazu i wykannie go
if (isset($_GET['what']) AND $_GET['what'] == 'delete') {
    $did = (int)$_GET['did'];
    $result = $db->Execute("DELETE FROM " . TABLE_SUBCONTRACTORS_SHIPPING . " WHERE subcontractors_id='$did' LIMIT 1");
    $result = $db->Execute("DELETE FROM " . TABLE_SUBCONTRACTORS_TO_ADMINS. " WHERE subcontractors_id='$did' LIMIT 1");
}

//pobieranie danych dla polecenia inser oraz wykannaie go
if (isset($_GET['pole']) AND $_GET['pole'] == 1) {
    $alias = $_GET['alias'];
    $name = $_GET['name'];
    $street = $_GET['street'];
    $zip = $_GET['zip'];
    $city = $_GET['city'];
    $state = $_GET['state'];
    $telephone = $_GET['telephone'];
    $contact_person = $_GET['contact_person'];
    $admin_id = (int)$_GET['admin_id'];
    $e_mail = $_GET['e_mail'];

    $db->Execute("INSERT INTO " . TABLE_SUBCONTRACTORS_SHIPPING . "(alias, name, street1, zip, email_address, telephone, contact_person, city, state)
VALUES('$alias','$name','$street','$zip','$e_mail','$telephone','$contact_person', '$city', '$state')")
    or die("Nie mozna ustawic nowych rekordow1");
    echo "<meta http-equiv=\"refresh\" content=\"0 url=edit_subcontrac.php\">";

    $insert_id = $db->Insert_ID();
    $db->Execute("INSERT INTO " . TABLE_SUBCONTRACTORS_TO_ADMINS . "(subcontractors_id, admin_id) VALUES ('$insert_id', '$admin_id')");
 
}

//save

if (isset($_GET['pole']) AND $_GET['pole'] == 0) {
    $alias = $_GET['alias'];
    $name = $_GET['name'];
    $street = $_GET['street'];
    $zip = $_GET['zip'];
    $city = $_GET['city'];
    $state = $_GET['state'];
    $telephone = $_GET['telephone'];
    $contact_person = $_GET['contact_person'];
    $e_mail = $_GET['e_mail'];
    $key = $_GET['key'];
    $admin_id = (int)$_GET['admin_id'];

    $result = $db->Execute("UPDATE " . TABLE_SUBCONTRACTORS_SHIPPING . " SET alias='$alias', name='$name',
 street1='$street', zip='$zip', city='$city', state='$state', email_address='$e_mail', telephone='$telephone', contact_person='$contact_person'
 WHERE subcontractors_id='$key' LIMIT 1");
    // Insert or create new record 

    $rec = $db->Execute("SELECT admin_id FROM " . TABLE_SUBCONTRACTORS_TO_ADMINS . " WHERE subcontractors_id='$key'"); 
    if ($rec->EOF) { 
    $db->Execute("INSERT INTO " . TABLE_SUBCONTRACTORS_TO_ADMINS . "(subcontractors_id, admin_id) VALUES ('$key', '$admin_id')");
    } else { 
       $db->Execute("UPDATE " . TABLE_SUBCONTRACTORS_TO_ADMINS . " SET admin_id = " . $admin_id . " WHERE subcontractors_id='$key' LIMIT 1"); 
    }
}


//ustawianie zmiennej w celu sortowania danych w odpowiednie sposob
if (isset($_GET['list_order'])) {
    if ($_GET['list_order'] == 'firstname') $disp_order = "alias ASC";
    if ($_GET['list_order'] == 'firstnamedesc') $disp_order = "alias DESC";
    if ($_GET['list_order'] == 'lastname') $disp_order = "name ASC";
    if ($_GET['list_order'] == 'lastnamedesc') $disp_order = "name DESC";
    if ($_GET['list_order'] == 'company') $disp_order = "street1 ASC";
    if ($_GET['list_order'] == 'companydesc') $disp_order = "street1 DESC";
    if ($_GET['list_order'] == 'email') $disp_order = "email_address ASC";
    if ($_GET['list_order'] == 'emaildesc') $disp_order = "email_address DESC";
    if ($_GET['list_order'] == 'zip') $disp_order = " city_state_zip ASC";
    if ($_GET['list_order'] == 'zipdesc') $disp_order = "city_state_zip DESC";
    if ($_GET['list_order'] == 'telephone') $disp_order = "telephone ASC";
    if ($_GET['list_order'] == 'telephonedesc') $disp_order = "telephone DESC";
    if ($_GET['list_order'] == 'person') $disp_order = "contact_person ASC";
    if ($_GET['list_order'] == 'persondesc') $disp_order = "contact_person DESC";

} else {

    $disp_order = "subcontractors_id ASC";
}



// ustawianie linkow dla naglowkow szablonu ktore pozwalaja na sortowanie kolumn


?>
<tr>
    <td class="pageHeading" colspan="2"><br><?php echo HEADING_TITLE_EDIT_SUBCONTRACTORS; ?><br><br></td>
</tr>
<tr>
    <td valign="top" width='80%'>
        <table border="0" width='100%' cellspacing="0" cellpadding="0">
            <tr class="dataTableHeadingRow">
                <td width='3%' class="dataTableHeadingContent" align="center" valign="top">
                    <?php echo ID; ?>
                </td>
                <td width='14%' class="dataTableHeadingContent" align="center">
                    <?php echo TABLE_HEADING_SHORTNAME; ?><br>
                    <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=firstname'); ?>"><?php echo($_GET['list_order'] == 'firstname' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                    <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=firstnamedesc'); ?>"><?php echo($_GET['list_order'] == 'firstnamedesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>
                <td width='14%' class="dataTableHeadingContent" align="center">
                    <?php echo TABLE_HEADING_FULLNAME; ?><br>
                    <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=lastname'); ?>"><?php echo($_GET['list_order'] == 'lastname' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                    <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=lastnamedesc'); ?>"><?php echo($_GET['list_order'] == 'lastnamedesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>
                <td width='14%' class="dataTableHeadingContent" align="center">
                    <?php echo TABLE_HEADING_EMAIL; ?><br>
                    <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=email'); ?>"><?php echo($_GET['list_order'] == 'email' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                    <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=emaildesc'); ?>"><?php echo($_GET['list_order'] == 'emaildesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>

                <td width='14%' class="dataTableHeadingContent" align="center">
                    <?php echo TABLE_HEADING_TELEPHONE; ?><br>
                    <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=telephone'); ?>"><?php echo($_GET['list_order'] == 'telephone' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                    <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=telephonedesc'); ?>"><?php echo($_GET['list_order'] == 'telephonedesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>

                <td width='14%' class="dataTableHeadingContent" align="center">
                    <?php echo TABLE_CONTACT_PERSON; ?><br>
                    <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=person'); ?>"><?php echo($_GET['list_order'] == 'person' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                    <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=persondesc'); ?>"><?php echo($_GET['list_order'] == 'persondesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>
                <td width='10%' class="dataTableHeadingContent" align="center">
                    <?php echo TABLE_ADMIN_ID; ?>
                </td>


                <td width='5%' class="dataTableHeadingContent" align="right"><?php echo ACTION; ?>&nbsp;<br>

            </tr>



            <?php

            $cid = $_GET['cID'];
            if ($cid == '') {
                $row2 = $db->Execute("SELECT s.subcontractors_id, alias,  name, email_address,  telephone, contact_person, sc.admin_id FROM " . TABLE_SUBCONTRACTORS_SHIPPING . " s LEFT JOIN " . TABLE_SUBCONTRACTORS_TO_ADMINS . " sc ON s.subcontractors_id = sc.subcontractors_id LIMIT 1");

                $cid = $row2->fields['subcontractors_id'];
            }
            $row = $db->Execute("SELECT s.subcontractors_id, alias,  name, email_address,  telephone, contact_person, sc.admin_id FROM " . TABLE_SUBCONTRACTORS_SHIPPING . " s LEFT JOIN " . TABLE_SUBCONTRACTORS_TO_ADMINS . " sc ON s.subcontractors_id = sc.subcontractors_id order by $disp_order");
            $k = 0;
            while (!$row->EOF) {
                if ($cid != $row->fields['subcontractors_id']) {
                    echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(edit_subcontrac, zen_get_all_get_params(array('cID', 'action')) . 'cID=' . $row->fields['subcontractors_id'] . '&action=edit', 'NONSSL') . '\'">' . "\n";
                } else {
                    echo '<tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(edit_subcontrac, zen_get_all_get_params(array('cID', 'action')) . 'cID=' . $row->fields['subcontractors_id'] . '&action=edit', 'NONSSL') . '\'">' . "\n";


                }

                echo "<td align='center'>" . $row->fields['subcontractors_id'] . "</td>";
                echo "<td align='center'>" . $row->fields['alias'] . "</td>";
                echo "<td align='center'>" . $row->fields['name'] . "</td>";
                echo "<td align='center'>" . $row->fields['email_address'] . "</td>";
                echo "<td align='center'>" . $row->fields['telephone'] . "</td>";
                echo "<td align='center'>" . $row->fields['contact_person'] . "</td>";
                echo "<td align='center'>" . $row->fields['admin_id'] . "</td>";
                if ($k == '0') {
                    $fond[0] = $row->fields['subcontractors_id'];
                    $fond[1] = $row->fields['alias'];
                }
                $k++;

                ?>
                <td align="right"> <?php if ($cid != $row->fields['subcontractors_id']) {
                        ?>
                        <img src="images/icon_info.gif" border="0" alt="Info" title=" Info ">
                    <?php
                    } else {
                        ?>
                        <img src="images/icon_arrow_right.gif" border="0" alt="">
                    <?php
                    }
                    ?>

                </td></tr>
                <?php
                $row->MoveNext();
            }
            ?>


        </table>
    </td>
    <td valign="top">

        <table border="0" width='100%' cellspacing="0" cellpadding="2" align="center">
            <tr>
                <?php
                $row2 = $db->Execute("SELECT s.subcontractors_id, alias,  name, email_address,  street1, city, state, zip, country, telephone, contact_person, sc.admin_id FROM " . TABLE_SUBCONTRACTORS_SHIPPING . "  s LEFT JOIN " . TABLE_SUBCONTRACTORS_TO_ADMINS . " sc ON s.subcontractors_id = sc.subcontractors_id WHERE s.subcontractors_id='$cid'");

                // projekt szablonu do wyswietlania subcontracotow oraz wyswietlanie ich
                ?>
                <td colspan="2" width='' class="infoBoxHeading">
                    <?php echo "ID:" . $row2->fields['subcontractors_id'] . " Full name: " . $row2->fields['name']; ?>
                </td>
            </tr>

            <form name='form1' action="edit_subcontrac.php" METHOD="get">
                <tr>
                    <td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_SHORTNAME; ?></td>
                    <td width='75%' align="left" class="infoBoxContent"><input type='text' name="alias"
                                                                               value="<?php echo $row2->fields['alias']; ?>">
                    </td>
                </tr>
                <tr>
                    <td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_FULLNAME; ?></td>
                    <td align="left" class="infoBoxContent"><input type='text' name="name"
                                                                   value="<?php echo $row2->fields['name']; ?>"></td>
                </tr>
                <tr>
                    <td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_STREET; ?></td>
                    <td align="left" class="infoBoxContent"><input type='text' name="street"
                                                                   value="<?php echo $row2->fields['street1']; ?>"></td>
                </tr>
                <tr>
                    <td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_CITY; ?></td>
                    </td>
                    <td align="left" class="infoBoxContent"><input type='text' name="city"
                                                                   value="<?php echo $row2->fields['city']; ?>"></td>
                </tr>
                <tr>
                    <td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_STATE; ?></td>
                    </td>
                    <td align="left" class="infoBoxContent"><input type='text' name="state"
                                                                   value="<?php echo $row2->fields['state']; ?>"></td>
                </tr>
                <tr>
                    <td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_ZIP; ?></td>
                    </td>
                    <td align="left" class="infoBoxContent"><input type='text' name="zip"
                                                                   value="<?php echo $row2->fields['zip']; ?>"></td>
                </tr>
                <tr>
                    <td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_EMAIL; ?></td>
                    <td align="left" class="infoBoxContent"><input type='text' name="e_mail"
                                                                   value="<?php echo $row2->fields['email_address']; ?>">
                    </td>
                </tr>
                <tr>
                    <td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_TELEPHONE; ?></td>
                    <td align="left" class="infoBoxContent"><input type='text' name="telephone"
                                                                   value="<?php echo $row2->fields['telephone']; ?>">
                    </td>
                </tr>
                <tr>
                    <td align="right" class="infoBoxContent"><?php echo TABLE_CONTACT_PERSON; ?></td>
                    <td align="left" class="infoBoxContent"><input type='text' name="contact_person"
                                                                   value="<?php echo $row2->fields['contact_person']; ?>">
                    </td>
                </tr>
                <tr>
                    <td align="right" class="infoBoxContent"><?php echo TABLE_ADMIN_ID; ?></td>
                    <td align="left" class="infoBoxContent"><input type='text' name="admin_id"
                                                                   value="<?php echo $row2->fields['admin_id']; ?>">
                    </td>
                </tr>
                <input type="hidden" name="pole"><input type='hidden' name="key"
                                                        value="<?php echo $row2->fields['subcontractors_id']; ?>">
                <tr>
                    <td colspan="2" class="infoBoxContent">
                        <input type="image" src="includes/languages/english/images/buttons/button_insert.gif"
                               name='insert' ONCLICK="javascript: document.form1.pole.value=1;document.form1.submit();">
                        <input type="image" src="includes/languages/english/images/buttons/button_save.gif"
                               ONCLICK="javascript:document.form1.pole.value=0;document.form1.submit();">
                        <br/>
                        <a href="edit_subcontrac.php?what=delete&did=<?php echo $row2->fields['subcontractors_id']; ?>"><img
                                src="includes/languages/english/images/buttons/button_delete.gif" border="0"
                                alt="Delete" title=" Delete "></a>
            </form>

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
