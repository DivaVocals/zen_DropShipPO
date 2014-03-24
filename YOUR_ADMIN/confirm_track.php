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


<tr><td class="pageHeading" colspan="2"><?php  echo HEADING_TITLE_TRACKING; ?><br><br></td></tr>
<td valign="top">
<?php
$sorder = $_GET[sorder];
if ($sorder == 1)
   echo "<a href='".HTTP_SERVER.DIR_WS_ADMIN."confirm_track.php'>".SHOW_OLDEST_PO_FIRST."</a>"; 
else
   echo "<a href='".HTTP_SERVER.DIR_WS_ADMIN."confirm_track.php?sorder=1'>".SHOW_NEWEST_PO_FIRST."</a>";
?></td>
           <tr>  <td valign="top">
		   <table border="0" width="100%" align="center" cellspacing="0" cellpadding="0">
              <tr class="dataTableHeadingRow">
                <td  class="dataTableHeadingContent" align="center" valign="top">
                  <?php  echo NUMBER_POS_TRACKING; ?>
                </td>
			    <td  class="dataTableHeadingContent" align="center" valign="top">
                  <?php  echo DATA_POS_TRACKING; ?>
                </td>
    <td  class="dataTableHeadingContent" align="center" valign="top">
                  <?php  echo PO_SENT_TO_NAME; ?>
                </td>
				<td  class="dataTableHeadingContent" align="center" valign="top">
                  <?php  echo DELIVER_NAME_TRACKING; ?>
                </td>
			   	</tr>
				<?php

				$sort_orders=array();
if ($sorder == 1)
    $query210b=mysql_query("SELECT p.po_number FROM ".TABLE_ORDERS_PRODUCTS." as p, ".TABLE_ORDERS." as o WHERE p.orders_id=o.orders_id AND p.po_sent=1 AND p.item_shipped=0 AND o.orders_status !=3 ORDER by p.orders_id DESC")
    or die("Nie mozna sie polaczyc z baza danych");  
else
    $query210b=mysql_query("SELECT p.po_number FROM ".TABLE_ORDERS_PRODUCTS." as p, ".TABLE_ORDERS." as o WHERE p.orders_id=o.orders_id AND p.po_sent=1 AND p.item_shipped=0 AND o.orders_status !=3 ORDER by p.orders_id ASC")
    or die("Nie mozna sie polaczyc z baza danych");
$p=0;
while($row210b=mysql_fetch_array($query210b, MYSQL_NUM))
{
$sort_orders[$p]=$row210b[0];
$p++; 
}


$temp=array_unique($sort_orders);
$wyjscie=array_values($temp);

				for($h=0; $h<count($wyjscie); $h++)
{  
				$query=mysql_query("SELECT orders_id, po_number, po_date, po_sent_to_subcontractor FROM ".TABLE_ORDERS_PRODUCTS." WHERE po_sent=1 AND item_shipped=0 AND po_number='$wyjscie[$h]'")
				or die("Nie mozna sie polaczyc z baza danych1");
				$i=0;
				$row=mysql_fetch_array($query, MYSQL_NUM);

				$query1=mysql_query("SELECT delivery_name, delivery_company, delivery_street_address, delivery_city, delivery_postcode, delivery_state, delivery_country, delivery_suburb FROM ".TABLE_ORDERS." WHERE orders_id='$row[0]'")
				or die("Nie mozna sie polaczcy z baza danych");
				$row1=mysql_fetch_array($query1, MYSQL_NUM);

			$subcontractor_query = mysql_query("SELECT full_name FROM ".TABLE_SUBCONTRACTORS." WHERE subcontractors_id = '$row[3]'");
			$subcontractor = mysql_fetch_assoc($subcontractor_query);
				if($h%2==1)
				     echo "<tr class='dataTableRowSelected'>";
                                else
                                     echo "<tr class='dataTableRow'>";
?>
                <td align="center" valign="center">
                  <?php  echo "<a href='".HTTP_SERVER.DIR_WS_ADMIN."confirm_track_sub.php?x=".$row[3]."&y=".$row[1]."' target='_blank'>".$row[0]."-".$row[1]."</a>"; ?>
                </td>
			    <td align="center" valign="center">
                  <?php  echo $row[2]; ?>
                </td>
  <td align="center" valign="center">
                  <?php  echo $subcontractor[full_name]; ?>
                </td>
				<td align="center" valign="top">
                  <?php
if ($row1[6] == zen_get_country_name(STORE_COUNTRY))
	$orderaddresscountry="";
else
	$orderaddresscountry="<br />".$row1[6];
if ($row1[7] == "" || $row1[7] == NULL)
	$orderaddresssuburb="";
else
	$orderaddresssuburb="<br />".$row1[7];
if ($row1[1] == "" || $row1[1] == NULL)
	$orderaddresscompany="";
else
	$orderaddresscompany=$row1[1]."<br />";
echo $row1[0]."<br />".$orderaddresscompany.$row1[2].$orderaddresssuburb."<br />".$row1[3].", ".$row1[5]." ".$row1[4]."<br />".$orderaddresscountry;
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