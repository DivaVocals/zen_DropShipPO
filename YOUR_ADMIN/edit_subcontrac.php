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
//  $Id: customers.php 1612 2005-07-19 21:09:38Z ajeh $
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
if(isset($_GET['what']) AND $_GET['what']=='delete')
{
$did=$_GET['did'];
$result=mysql_query("DELETE FROM ".TABLE_SUBCONTRACTORS." WHERE subcontractors_id='$did' LIMIT 1")
or die("Nie mozna usunac danych z bazy");
}

//save
if(isset($_GET['what']) AND $_GET['what']=='save')
{

$sid=$_GET['sid'];
$result=mysql_query("UPDATE ".TABLE_SUBCONTRACTORS." SET WHERE subcontractors_id='$sid' LIMIT 1")
or die("Nie mozna poprawic bazy");


}


//pobieranie danych dla polecenia inser oraz wykannaie go
if(isset($_GET['pole']) AND  $_GET['pole']==1)
{
$short_name=$_GET['short_name'];
$full_name=$_GET['full_name'];
$street=$_GET['street'];
$zip=$_GET['zip'];
$city=$_GET['city'];
$state=$_GET['state'];
$telephone=$_GET['telephone'];
$contact_person=$_GET['contact_person'];
$e_mail=$_GET['e_mail'];

$result=mysql_query("INSERT INTO ".TABLE_SUBCONTRACTORS."(short_name, full_name, street1, zip, email_address, telephone, contact_person, city, state)
VALUES('$short_name','$full_name','$street','$zip','$e_mail','$telephone','$contact_person', '$city', '$state')")
or die("Nie mozna ustawic nowych rekordow1");
echo "<meta http-equiv=\"refresh\" content=\"0 url=edit_subcontrac.php\">";
}

//save

if(isset($_GET['pole']) AND  $_GET['pole']==0)
{
$short_name=$_GET['short_name'];
$full_name=$_GET['full_name'];
$street=$_GET['street'];
$zip=$_GET['zip'];
$city=$_GET['city'];
$state=$_GET['state'];
$telephone=$_GET['telephone'];
$contact_person=$_GET['contact_person'];
$e_mail=$_GET['e_mail'];
$key=$_GET['key'];

$result=mysql_query("UPDATE ".TABLE_SUBCONTRACTORS." SET short_name='$short_name', full_name='$full_name',
 street1='$street', zip='$zip', city='$city', state='$state', email_address='$e_mail', telephone='$telephone', contact_person='$contact_person'
 WHERE subcontractors_id='$key' LIMIT 1")
or die("Nie mozna ustawic nowych rekordow2");
}


//ustawianie zmiennej w celu sortowania danych w odpowiednie sposob
if(isset($_GET['list_order']))
{
	if($_GET['list_order']=='firstname') $disp_order = "short_name ASC";
	if($_GET['list_order']=='firstnamedesc') $disp_order = "short_name DESC";
	if($_GET['list_order']=='lastname') $disp_order = "full_name ASC";
	if($_GET['list_order']=='lastnamedesc') $disp_order = "full_name DESC";
	if($_GET['list_order']=='company') $disp_order = "street1 ASC";
	if($_GET['list_order']=='companydesc') $disp_order = "street1 DESC";
	if($_GET['list_order']=='email') $disp_order = "email_address ASC";
	if($_GET['list_order']=='emaildesc') $disp_order = "email_address DESC";
	if($_GET['list_order']=='zip') $disp_order = " city_state_zip ASC";
	if($_GET['list_order']=='zipdesc') $disp_order = "city_state_zip DESC";
	if($_GET['list_order']=='telephone') $disp_order = "telephone ASC";
	if($_GET['list_order']=='telephonedesc') $disp_order = "telephone DESC";
	if($_GET['list_order']=='person') $disp_order = "contact_person ASC";
	if($_GET['list_order']=='persondesc') $disp_order = "contact_person DESC";

}else
{

$disp_order = "subcontractors_id ASC";
}



// ustawianie linkow dla naglowkow szablonu ktore pozwalaja na sortowanie kolumn


?><tr><td class="pageHeading" colspan="2"><br><?php  echo HEADING_TITLE_EDIT_SUBCONTRACTORS; ?><br><br></td></tr>
           <tr>  <td valign="top" width='80%'>
		   <table width="100%" cellspacing="0" cellpadding="5" border="0">
              <tr class="dataTableHeadingRow">
                <td width='3%' class="dataTableHeadingContent" align="left" valign="top">
                  <?php  echo ID; ?>
                </td>
                <td width='14%' class="dataTableHeadingContent" align="left">
                  <?php echo TABLE_HEADING_SHORTNAME;  ?><br>
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=firstname'); ?>"><?php echo ($_GET['list_order']=='firstname' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=firstnamedesc'); ?>"><?php echo ($_GET['list_order']=='firstnamedesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>
                <td width='14%' class="dataTableHeadingContent" align="left">
                  <?php echo TABLE_HEADING_FULLNAME; ?><br>
				  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=lastname'); ?>"><?php echo ($_GET['list_order']=='lastname' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=lastnamedesc'); ?>"><?php echo ($_GET['list_order']=='lastnamedesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>
               	<td width='14%' class="dataTableHeadingContent" align="left">
                  <?php echo TABLE_HEADING_EMAIL; ?><br>
				  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=email'); ?>"><?php echo ($_GET['list_order']=='email' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=emaildesc'); ?>"><?php echo ($_GET['list_order']=='emaildesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>

                <td width='14%' class="dataTableHeadingContent" align="left">
                  <?php echo TABLE_HEADING_TELEPHONE; ?><br>
				  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=telephone'); ?>"><?php echo ($_GET['list_order']=='telephone' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=telephonedesc'); ?>"><?php echo ($_GET['list_order']=='telephonedesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>

                <td width='14%' class="dataTableHeadingContent" align="left">
                  <?php echo TABLE_CONTACT_PERSON; ?><br>
				  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=person'); ?>"><?php echo ($_GET['list_order']=='person' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=persondesc'); ?>"><?php echo ($_GET['list_order']=='persondesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>


                <td width='15%' class="dataTableHeadingContent" align="right"><?php echo ACTION; ?>&nbsp;<br>

              </tr>



					<?php

					$cid=$_GET['cID'];
	if($cid=='')
	{
	$query2=mysql_query("SELECT subcontractors_id, short_name,  full_name, email_address,  telephone, contact_person FROM ".TABLE_SUBCONTRACTORS." LIMIT 1")
		or die("Nie mozna sie polaczyc z baza danych");

	$row2=mysql_fetch_array($query2, MYSQL_NUM);
	$cid=$row2[0];
	}
					$query=mysql_query("SELECT subcontractors_id, short_name,  full_name, email_address,  telephone, contact_person	FROM ".TABLE_SUBCONTRACTORS." order by $disp_order")
											or die("Nie mozna polaczyc");
											$k=0;
					while($row=mysql_fetch_array($query, MYSQL_NUM))
					{
					if($cid!=$row[0])
					{
 echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(edit_subcontrac, zen_get_all_get_params(array('cID', 'action')) . 'cID=' . $row[0] . '&action=edit', 'NONSSL') . '\'">' . "\n";
 					}
					else{
 echo '<tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(edit_subcontrac, zen_get_all_get_params(array('cID', 'action')) . 'cID=' . $row[0] . '&action=edit', 'NONSSL') . '\'">' . "\n";


					}

						for($i=0; $i<count($row); $i++)
						{

						echo "<td align='left'>$row[$i]</td>";
						}
						if($k=='0')
						{
						$fond[0]=$row[0];
						$fond[1]=$row[1];
						}
						$k++;

									?>
					<td align="right"> <?php if($cid!=$row[0])

					{  ?>
					<img src="images/icon_info.gif" border="0" alt="Info" title=" Info ">
					<?php } else
					{ ?>
					<img src="images/icon_arrow_right.gif" border="0" alt="">
					<?php
					}
					?>

					</td></tr>
				<?php	}?>


        </table>
		</td>
		<td valign="top" >

		<table border="0" width='100%' cellspacing="0" cellpadding="2" align="left">
		<tr>
		<?php
$query2=mysql_query("SELECT * FROM ".TABLE_SUBCONTRACTORS." WHERE subcontractors_id='$cid'")
or die("Nie mozna sie polaczyc z baza danych");
$row2=mysql_fetch_array($query2, MYSQL_NUM);

// projekt szablonu do wyswietlania subcontracotow oraz wyswietlanie ich
?>
<td colspan="2" width='' class="infoBoxHeading">
<?php echo "ID:$row2[0] Full name:$row2[2] "; ?>
</td>
</tr>

		<form name='form1' action="edit_subcontrac.php" METHOD="get">
		<tr>
		<td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_SHORTNAME; ?></td>
		<td width='75%' align="left" class="infoBoxContent"><input type='text' name="short_name" value="<?php echo $row2[1]; ?>"></td>
		</tr>
		<tr>
		<td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_FULLNAME; ?></td>
		<td align="left" class="infoBoxContent"><input type='text' name="full_name" value="<?php echo $row2[2]; ?>"></td>
		</tr>
		<tr>
		<td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_STREET; ?></td>
		<td align="left" class="infoBoxContent"><input type='text' name="street" value="<?php echo $row2[3]; ?>"></td>
		</tr>
		<tr>
		<td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_CITY; ?></td></td>
		<td align="left" class="infoBoxContent"><input type='text' name="city" value="<?php echo $row2[4]; ?>"></td>
		</tr>
		<tr>
		<td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_STATE; ?></td></td>
		<td align="left" class="infoBoxContent"><input type='text' name="state" value="<?php echo $row2[5]; ?>"></td>
		</tr>
		<tr>
		<td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_ZIP; ?></td></td>
		<td align="left" class="infoBoxContent"><input type='text' name="zip" value="<?php echo $row2[6]; ?>"></td>
		</tr>
		<tr>
		<td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_EMAIL; ?></td>
		<td align="left" class="infoBoxContent"><input type='text' name="e_mail" value="<?php echo $row2[7]; ?>"></td>
		</tr>
		<tr>
		<td align="right" class="infoBoxContent"><?php echo TABLE_HEADING_TELEPHONE; ?></td>
		<td align="left" class="infoBoxContent"><input type='text' name="telephone" value="<?php echo $row2[8]; ?>"></td>
		</tr>
		<tr>
		<td align="right" class="infoBoxContent"><?php echo TABLE_CONTACT_PERSON; ?></td>
		<td align="left" class="infoBoxContent"><input type='text' name="contact_person" value="<?php echo $row2[9]; ?>"></td>
		</tr><input type="hidden" name="pole"><input type='hidden' name="key" value="<?php echo $row2[0]; ?>">
<tr>
<td colspan="2" class="infoBoxContent">
<input class="normal_button button" type="button" value="<?php echo IMAGE_INSERT; ?>" name='insert' ONCLICK="javascript: document.form1.pole.value=1;document.form1.submit();">
<input class="normal_button button" type="button" value="<?php echo IMAGE_SAVE; ?>" name='insert' ONCLICK="javascript:document.form1.pole.value=0;document.form1.submit();">
<a href="edit_subcontrac.php?what=delete&did=<?php echo $row2[0]; ?>"><input class="normal_button button" type="button" value="<?php echo IMAGE_DELETE; ?>"></a>
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