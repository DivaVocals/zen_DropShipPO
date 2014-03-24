<?php
/**
* send po to unknown customer
*/

require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');

define('FPDF_FONTPATH','includes/classes/fpdf/font/');
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
<!-- body //--><br>


<!-- body_text //-->
<?php
if ($_POST[numberofproducts] >= 1)
    $numberofproducts = $_POST[numberofproducts];
else
    $numberofproducts = 0;
$step = zen_db_prepare_input($_POST['step']);
$add_product_categories_id = zen_db_prepare_input($_POST['add_product_categories_id']);
$add_product_products_id = zen_db_prepare_input($_POST['add_product_products_id']);
$add_product_quantity = zen_db_prepare_input($_POST['add_product_quantity']);
$partialship = $_POST[partialship];
$sub = $_POST[sub];
if ($_POST[includepackinglistoption] != "" || $_POST[includepackinglistoption] != NULL) {
    if (PO_SEND_PACKING_LISTS != 2 && PO_SEND_PACKING_LISTS != 3)
        $includeplistoption = "";
    else
        $includeplistoption = $_POST[includepackinglistoption];	
} else {
    if (PO_SEND_PACKING_LISTS == 2)
        $includeplistoption = "yes";
    if (PO_SEND_PACKING_LISTS == 2 && ($numberofproducts > 0 || $_POST[step] > 1))
        $includeplistoption = "no";
    if (PO_SEND_PACKING_LISTS == 3)
        $includeplistoption = "no";
    if (PO_SEND_PACKING_LISTS != 2 && PO_SEND_PACKING_LISTS != 3)
        $includeplistoption = "";
}
for ($i=0; $i<=$numberofproducts; $i++) {
         $passiton = "productlistid".$i;
	 $productlistid[$i] = $_POST[$passiton];
         $passiton = "quantitylist".$i;
	 $quantitylist[$i] = $_POST[$passiton]; 
         $passiton = "attributelist".$i;
	 $attributelist[$i] = $_POST[$passiton];
         $passiton = "manufacturerlist".$i;
	 $manufacturerlist[$i] = $_POST[$passiton];
         $passiton = "productnamelist".$i;
	 $productnamelist[$i] = $_POST[$passiton];
         $passiton = "productmodellist".$i;
	 $productmodellist[$i] = $_POST[$passiton];
$attributelist[$i] = stripslashes($attributelist[$i]);
$manufacturerlist[$i] = stripslashes($manufacturerlist[$i]);
$productnamelist[$i] = stripslashes($productnamelist[$i]);
$productmodellist[$i] = stripslashes($productmodellist[$i]);

}
if($_POST[postonc]=='yes') {
if(!$wp1)
	{
		echo "Nie mo&#191;na otworzyc pliku";
		exit;
	} else {
		$i=0;

		while(!feof($wp1))
		{
			$zamowienie[$i]=fgets($wp1,999);
			$i++;
		}

		fclose($wp1);
		for ($i=0; $i<count($zamowienie); $i++) {
			$zawartosc=$zawartosc.$zamowienie[$i];
		}
	}

	if(!$wp2)
	{
		echo "Nie mo&#191;na otworzyc pliku";
		exit;
	} else {
		$i=0;
		while(!feof($wp2))
		{
			$tresc_robij[$i]=fgets($wp2,999);
			$i++;
		}
	}

	fclose($wp2);
	$t=0;

	if(!$wp3)
	{
		echo "Nie mo&#191;na otworzyc pliku";
		exit;
	} else {
		while(!feof($wp3))
		{
			$tracking_link[$t]=fgets($wp3,999);
			$t++;
		}
	}

	fclose($wp3);

	for($i=0; $i<count($tresc_robij); $i++)
	{
		$tresc_robij1=$tresc_robij1.$tresc_robij[$i];
	}

                        $billto = $_POST[billingaddress];
                        $billto = stripslashes($billto);
                        if ($_POST[shippingaddress] == '' || $_POST[shippingaddress] == NULL) {
                             $shipto=$billto; }
                        else {
                             $shipto = $_POST[shippingaddress];
                             $shipto = stripslashes($shipto); }
                        $pdf = new INVOICE( 'P', 'mm', 'Letter' );
			$pdf->Open();
			$pdf->AddPage();
			$storeaddressnocr = str_replace(STORE_NAME.chr(13).chr(10),"",STORE_NAME_ADDRESS);
                        $storeaddressnocr = str_replace(STORE_NAME.chr(13),"",$storeaddressnocr);
                        $storeaddressnocr = str_replace(STORE_NAME." ","",$storeaddressnocr);
                        $storeaddressnocr = str_replace(STORE_NAME,"",$storeaddressnocr);
                        $pdf->addSociete( STORE_NAME,
                  			
              			          $storeaddressnocr );
			$pdf->fact_dev( PACKING_LIST_FIRST_WORD." ", PACKING_LIST_SECOND_WORD );
			$invdate=date("m-d-Y");
			$pdf->addDate($invdate);
			$pdf->addClient(stripslashes($_POST[ponumber]));
			$pdf->addClientShipAdresse($shipto);
			$pdf->addClientBillAdresse($billto);
			
			$cols=array( PACKING_LIST_MODEL_NUMBER    => 40,
           			     PACKING_LIST_PRODUCT_DESCRIPTION  => 120.9,
        			     PACKING_LIST_QUANTITY     => 25 );
			$pdf->addCols($cols);
			$cols=array( PACKING_LIST_MODEL_NUMBER    => "L",
       			      	     PACKING_LIST_PRODUCT_DESCRIPTION  => "L",
       			             PACKING_LIST_QUANTITY     => "C" );
			$pdf->addLineFormat($cols);
			$pdf->addLineFormat($cols);
			$y    = 89;
                        for ($i=1; $i<=$numberofproducts; $i++) {
                        $line = array( PACKING_LIST_MODEL_NUMBER    => $productmodellist[$i],
              		    PACKING_LIST_PRODUCT_DESCRIPTION  => $productnamelist[$i] . " " . $attributelist[$i],
             		    PACKING_LIST_QUANTITY     => $quantitylist[$i] );
		        $size = $pdf->addLine( $y, $line );
			$y   += $size + 2;  }
if ($_POST[shippingoption] == '' or $_POST[shippingoption] == NULL)
    $shipping=PO_CHANGE_SHIPPING_TO;
else
    $shipping=stripslashes($_POST[shippingoption]);
if ($_POST[partialship] == 1)
     $pdf->addNotes(SHIPPING_OPTION.": ".$shipping."\n\n".PO_PARTIALSHIP_PACKINGLIST."\n".stripslashes($_POST[plistcomments]));        
else
     $pdf->addNotes(SHIPPING_OPTION.": ".$shipping."\n\n".PO_FULLSHIP_PACKINGLIST."\n".stripslashes($_POST[plistcomments]));
      $pdf->Output(PO_PACKINGLIST_FILENAME, "F");
if (PO_UNKNOWN_OMIT1 != '' && PO_UNKNOWN_OMIT1 != NULL)
   $zawartosc=str_replace (PO_UNKNOWN_OMIT1,"",$zawartosc);
if (PO_UNKNOWN_OMIT2 != '' && PO_UNKNOWN_OMIT2 != NULL)
   $zawartosc=str_replace (PO_UNKNOWN_OMIT2,"",$zawartosc);
if (PO_UNKNOWN_OMIT3 != '' && PO_UNKNOWN_OMIT3 != NULL)
   $zawartosc=str_replace (PO_UNKNOWN_OMIT3,"",$zawartosc);
$zawartosc=str_replace("{customers_name}","",$zawartosc);
			$zawartosc=str_replace("{order_number}",$_POST[ponumber],"$zawartosc");
			$zawartosc=str_replace("{customers_adres}",$billto,"$zawartosc");
			$zawartosc=str_replace("{customers_phone}","Not Available","$zawartosc");
			$zawartosc=str_replace("{customers_email}","","$zawartosc");
			$zawartosc=str_replace("{delivery_name}","","$zawartosc");
			$zawartosc=str_replace("{po_comments}",stripslashes($_POST[posubcomments]),"$zawartosc");
                        $zawartosc=str_replace("{customers_comments}","","$zawartosc");
			$zawartosc=str_replace("{delivery_company}","","$zawartosc");
			$zawartosc=str_replace("{delivery_adress}",$shipto,"$zawartosc");	
			$zawartosc=str_replace("{billing_company}","","$zawartosc");
			$zawartosc=str_replace("{billing_name}","","$zawartosc");
			$zawartosc=str_replace("{billing_address}",$billto,"$zawartosc");
			$zawartosc=str_replace("{payment_method}","","$zawartosc");
			$zawartosc=str_replace("{date_purchased}",$invdate,"$zawartosc");
			$zawartosc=str_replace("{shipping_method}",$shipping,"$zawartosc");
$tresc_ostateczna='';
			$trescik='';
			$newzawartosc='';
for($i=1; $i<=$numberofproducts; $i++)
			{
				$trescik=$tresc_robij1;
if (PO_UNKNOWN_OMIT1 != '' && PO_UNKNOWN_OMIT1 != NULL)
   $trescik=str_replace (PO_UNKNOWN_OMIT1,"",$trescik);
if (PO_UNKNOWN_OMIT2 != '' && PO_UNKNOWN_OMIT2 != NULL)
   $trescik=str_replace (PO_UNKNOWN_OMIT2,"",$trescik);
if (PO_UNKNOWN_OMIT3 != '' && PO_UNKNOWN_OMIT3 != NULL)
   $trescik=str_replace (PO_UNKNOWN_OMIT3,"",$trescik);
				$trescik=str_replace("{manufacturers_name}",$manufacturerlist[$i],$trescik);
				$trescik=str_replace("{products_name}",$productnamelist[$i],$trescik);
				$trescik=str_replace("{products_model}",$productmodellist[$i],$trescik);
				$trescik=str_replace("{final_price}","",$trescik);
				$trescik=str_replace("{products_quantity}",$quantitylist[$i],$trescik);
				$trescik=str_replace("{products_attributes}",$attributelist[$i]." ",$trescik);

				$tresc_ostateczna=$tresc_ostateczna.$trescik;
				$newzawartosc=$zawartosc.$tresc_ostateczna;

			}
$dlaemaila= ($sub!='0') ? $sub : 0;
$query22=mysql_query("SELECT * FROM ".TABLE_SUBCONTRACTORS." WHERE subcontractors_id='$dlaemaila'")
			or die("Failed to connect database: 1");
			$subcontractor=mysql_fetch_assoc($query22);
			$adresdo=$subcontractor['email_address'];
			/* if ($dlaemaila==0) $adresdo=PO_OWN_STOCK_EMAIL; */

$newzawartosc=str_replace("{po_number}",$_POST[ponumber],$newzawartosc);
			$tematk=PO_SUBJECT;
			$tematk=str_replace("{po_number}",$_POST[ponumber],$tematk);
			$tematk=str_replace("{contact_person}",$subcontractor['contact_person'],$tematk);
			$tematk=str_replace("{full_name}",$subcontractor['full_name'],$tematk);
		   $tematk=str_replace("{short_name}",$subcontractor['short_name'],$tematk);
$tematk = str_replace("{order_number}",$_POST[ponumber],$tematk);

			for($t=0; $t<=count($tracking_link); $t++)
			{
if (PO_UNKNOWN_OMIT1 != '' && PO_UNKNOWN_OMIT1 != NULL)
   $tracking_link_good=str_replace (PO_UNKNOWN_OMIT1,"",$tracking_link_good);
if (PO_UNKNOWN_OMIT2 != '' && PO_UNKNOWN_OMIT2 != NULL)
   $tracking_link_good=str_replace (PO_UNKNOWN_OMIT2,"",$tracking_link_good);
if (PO_UNKNOWN_OMIT3 != '' && PO_UNKNOWN_OMIT3 != NULL)
   $tracking_link_good=str_replace (PO_UNKNOWN_OMIT3,"",$tracking_link_good);
				$tracking_link_good=$tracking_link_good.str_replace("{tracking_link}","",$tracking_link[$t]);
			}
			$newzawartosc=$newzawartosc.$tracking_link_good;
if (PO_UNKNOWN_OMIT1 != '' && PO_UNKNOWN_OMIT1 != NULL)
   $newzawartosc=str_replace (PO_UNKNOWN_OMIT1,"",$newzawartosc);
if (PO_UNKNOWN_OMIT2 != '' && PO_UNKNOWN_OMIT2 != NULL)
   $newzawartosc=str_replace (PO_UNKNOWN_OMIT2,"",$newzawartosc);
if (PO_UNKNOWN_OMIT3 != '' && PO_UNKNOWN_OMIT3 != NULL)
   $newzawartosc=str_replace (PO_UNKNOWN_OMIT3,"",$newzawartosc);
		  $newzawartosc = str_replace("{contact_person}",$subcontractor['contact_person'],$newzawartosc);
		  $newzawartosc = str_replace("{full_name}",$subcontractor['full_name'],$newzawartosc);
		  $newzawartosc = str_replace("{short_name}",$subcontractor['short_name'],$newzawartosc);
		  $newzawartosc = str_replace("{subcontractors_id}",$subcontractor['subcontractors_id'],$newzawartosc);
		  $newzawartosc = str_replace("{street}",$subcontractor['street1'],$newzawartosc);
		  $newzawartosc = str_replace("{city}",$subcontractor['city'],$newzawartosc);
		  $newzawartosc = str_replace("{state}",$subcontractor['state'],$newzawartosc);
		  $newzawartosc = str_replace("{zip}",$subcontractor['zip'],$newzawartosc);
		  $newzawartosc = str_replace("{telephone}",$subcontractor['telephone'],$newzawartosc);
		  $newzawartosc = str_replace("{email_address}",$subcontractor['email_address'],$newzawartosc);
		  $newzawartosc = str_replace("{shipping_method}",$shipping,$newzawartosc);
                  $html_msg['EMAIL_MESSAGE_HTML'] = str_replace('
','<br />',$newzawartosc);
                  if ($_POST[includepackinglistoption] == 'yes')
			zen_mail($adresdo,$adresdo,$tematk,$newzawartosc,PO_FROM_EMAIL_NAME,PO_FROM_EMAIL_ADDRESS,$html_msg, NULL, PO_PACKINGLIST_FILENAME, 'application/pdf');
                  else
                        zen_mail($adresdo,$adresdo,$tematk,$newzawartosc,PO_FROM_EMAIL_NAME,PO_FROM_EMAIL_ADDRESS,$html_msg, NULL);
 ?> <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo PO_SENT_MESSAGE; ?></td>
          </tr>
        </table><br /><br />
<?php }



/* ***   Begin Main Page *** */
for ($i=0; $i<=$numberofproducts; $i++) {

$attributelist[$i] = htmlspecialchars($attributelist[$i],ENT_QUOTES);
$manufacturerlist[$i] = htmlspecialchars($manufacturerlist[$i],ENT_QUOTES);
$productnamelist[$i] = htmlspecialchars($productnamelist[$i],ENT_QUOTES);
$productmodellist[$i] = htmlspecialchars($productmodellist[$i],ENT_QUOTES); }
$shippingoption = stripslashes($_POST[shippingoption]);
$shippingoption = htmlspecialchars($shippingoption,ENT_QUOTES);
$ponumber = stripslashes($_POST[ponumber]);
$ponumber = htmlspecialchars($ponumber,ENT_QUOTES);
$shippingaddress = stripslashes($_POST[shippingaddress]);
$shippingaddress = htmlspecialchars($shippingaddress,ENT_QUOTES);
$billingaddress = stripslashes($_POST[billingaddress]);
$billingaddress = htmlspecialchars($billingaddress,ENT_QUOTES);
$posubcomments = stripslashes($_POST[posubcomments]);
$posubcomments = htmlspecialchars($posubcomments,ENT_QUOTES);
$plistcomments = stripslashes($_POST[plistcomments]);
$plistcomments = htmlspecialchars($plistcomments,ENT_QUOTES);
function sub($cont)
			{

			$query2=mysql_query("SELECT  subcontractors_id,short_name FROM ".TABLE_SUBCONTRACTORS." ORDER BY short_name")
			or die('Failed to connect database: ');

			echo "<select name='sub'>";
			while($row22=mysql_fetch_array($query2, MYSQL_NUM))
			{

			echo "<option value='$row22[0]'";

			if ($cont == NULL) {

                           if($row22[0] == 0)
			   {
			     echo "selected";
			   }
                        } else {
                           if($row22[0] == $cont)
			   {
			     echo "selected";
			   } }
			     echo ">$row22[1]</option>";
			   }
			echo "</select>";
			}

if ($_POST[step] == 5) {
    $numberofproducts++;
    $productlistid[$numberofproducts] = $_POST[add_product_products_id];
$result7a=mysql_query("SELECT p.products_model, o.products_name FROM ".TABLE_PRODUCTS." as p, ".TABLE_PRODUCTS_DESCRIPTION." as o WHERE p.products_id=o.products_id and p.products_id='$productlistid[$numberofproducts]'")
				or die("Failed to connect database: ");
				while($row7a=mysql_fetch_array($result7a, MYSQL_NUM)) {
                                        $productmodellist[$numberofproducts] = stripslashes($row7a[0]);                         
					$productnamelist[$numberofproducts] = stripslashes($row7a[1]);
$productmodellist[$numberofproducts] = htmlspecialchars($productmodellist[$numberofproducts],ENT_QUOTES);
$productnamelist[$numberofproducts] = htmlspecialchars($productnamelist[$numberofproducts],ENT_QUOTES);
}
    $manufacturerlist[$numberofproducts]=zen_get_products_manufacturers_name($productlistid[$numberofproducts]);
    $manufacturerlist[$numberofproducts]=stripslashes($manufacturerlist[$numberofproducts]);
    $manufacturerlist[$numberofproducts] = htmlspecialchars($manufacturerlist[$numberofproducts],ENT_QUOTES);
    $quantitylist[$numberofproducts] = stripslashes($_POST[add_product_quantity]);
    $quantitylist[$numberofproducts] = htmlspecialchars($quantitylist[$numberofproducts],ENT_QUOTES);
$attributelist[$numberofproducts] = '';
    if ($_POST[optionstoadd] != NULL) {
      for ($i=1; $i<=$_POST[optionstoadd]; $i++) {
        $sendoptionon = "add_product_options".$i;
	$result9a=mysql_query("SELECT products_options_values_name FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id='$_POST[$sendoptionon]' ")
				or die("Failed to connect database: ");
				while($row9a=mysql_fetch_array($result9a, MYSQL_NUM)) {
					$attributes=$row9a[0]; }
$result8a=mysql_query("SELECT products_options_id FROM ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." WHERE  products_options_values_id='$_POST[$sendoptionon]'")
			or die('Failed to connect database: 8');
while($row8a=mysql_fetch_array($result8a, MYSQL_NUM)) {
					$attributestypenumber=$row8a[0]; }
$result8c=mysql_query("SELECT products_options_name FROM ".TABLE_PRODUCTS_OPTIONS." WHERE products_options_id='$attributestypenumber'")
			or die('Failed to connect database: 8');
while($row8c=mysql_fetch_array($result8c, MYSQL_NUM)) {
					$attributestype=$row8c[0]; }
				
    if ($i == 1)
       $attributelist[$numberofproducts] = $attributestype.": ".$attributes;
    else
       $attributelist[$numberofproducts] .= " " . $attributestype.": ".$attributes;
    
}
$attributelist[$numberofproducts]=stripslashes($attributelist[$numberofproducts]);
$attributelist[$numberofproducts]=htmlspecialchars($attributelist[$numberofproducts],ENT_QUOTES);
}
} 
?>

   <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo BOX_CUSTOMERS_SEND_POS_NC; ?><br><br></td>
          </tr><tr>  <td valign="top"><?php echo REFRESH_WARNING; ?></td></tr>
        </table><br /><br />

<form name='pos' method='post' action='send_pos_nc.php'>
<table border="0" width="100%" align="center" cellspacing="0" cellpadding="0">
<tr>
<td width="20%" align="center"><?php echo TABLE_SEND_PO_TO; ?></td>
<td width="20%" align="center"><?php echo TABLE_PO_ORDER_NUMBER; ?></td>
<td width="60%" align="center"><?php echo TABLE_PO_SHIPPING_CHOICE." ".PO_CHANGE_SHIPPING_TO; ?></td>
</tr><tr>
<td width="20%" align="center"><?php sub($sub); ?></td><?php
echo "<td width='20%' align='center'><input type='text' name='ponumber' value='$ponumber' size='25%' />"; ?> </td>
<td width="60%" align="center"><?php echo "<input type='text' name='shippingoption' value='$shippingoption' size='100%'/>"; ?> </td>
</tr></table><br /><br />
<table border="0" width="100%" align="center" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" align="center"><?php echo TABLE_PO_BILLING_ADDRESS; ?></td>
<td width="50%" align="center"><?php echo TABLE_PO_SHIPPING_ADDRESS; ?></td>
</tr><tr>
<td width="50%" align="center"><textarea rows="6" name="billingaddress"><?php echo $billingaddress; ?></textarea></td>
<td width="50%" align="center"><textarea rows="6" name="shippingaddress"><?php echo $shippingaddress; ?></textarea></td>
</tr></table><br /><br />
<table border="0" width="100%" align="center" cellspacing="0" cellpadding="0">
<tr>
<td width="10%" align="center"><?php echo TABLE_PO_PRODUCTS_QUANTITY; ?></td>
<td width="10%" align="center"><?php echo TABLE_PO_PRODUCTS_MODEL_NUMBER; ?></td>
<td width="20%" align="center"><?php echo TABLE_PO_PRODUCTS_MANUFACTURER; ?></td>
<td width="40%" align="center"><?php echo TABLE_PO_PRODUCTS_DESCRIPTION; ?></td>
<td width="10%" align="center"><?php echo TABLE_PO_PRODUCTS_OPTIONS; ?></td>
</tr><tr> <?php
for ($i=1; $i<=$numberofproducts; $i++) {
   echo "<td width='10%' align='center'><input type='text' name='quantitylist$i' value='$quantitylist[$i]' size='10%'></td>";
   echo "<td width='10%' align='center'><input type='text' name='productmodellist$i' value='$productmodellist[$i]' size='20%'></td>";
   echo "<td width='20%' align='center'><input type='text' name='manufacturerlist$i' value='$manufacturerlist[$i]' size='20%'></td>";
   echo "<td width='40%' align='center'><input type='text' name='productnamelist$i' value='$productnamelist[$i]' size='75%'></td>";
   echo "<td width='10%' align='center'><input type='text' name='attributelist$i' value='$attributelist[$i]' size='20%'></td></tr>"; 
   echo "<input type='hidden' name='productlistid$i' value='$productlistid[$i]'>"; } ?>
</tr></table><br /><br />
<center>
<?php if ($_POST[step] == 5) {
echo "<input type='hidden' name='step' value='2'>"; ?>
<input type="button" name="postonc" value="Add Another Product" ONCLICK="javascript:document.pos.submit();">
<br /><br />  <?php } ?>
<?php echo TABLE_COMMENTS_FOR_POS; ?>:&nbsp;<?php echo "<input type='text' name='posubcomments' value='$posubcomments' size='90' />"; ?> <br /><br />
<?php if (PO_SEND_PACKING_LISTS != 0) { ?>
<?php echo TABLE_COMMENTS_FOR_PACKING_LISTS; ?>:&nbsp;<?php echo "<input type='text' name='plistcomments' value='$plistcomments' size='90' maxlength='90' />"; ?> <br /><br /><?php } ?>
<input type="hidden" name="numberofproducts" value="<?php echo $numberofproducts ?>">
<?php if (PO_SEND_PACKING_LISTS == 0) { ?>
<input type="hidden" name="includepackinglistoption" value="no"> <?php } ?>
<?php if (PO_SEND_PACKING_LISTS == 1) { ?>
<input type="hidden" name="includepackinglistoption" value="yes"> <?php } ?>
<?php if (PO_SEND_PACKING_LISTS == 2 || PO_SEND_PACKING_LISTS == 3) { ?>
<?php echo TABLE_INCLUDE_PACKINGLIST_OPTION; ?><input type="checkbox" name="includepackinglistoption" value="yes" <?php if ($includeplistoption == "yes")  echo "CHECKED"; ?> >&nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
<?php if (PO_SEND_PACKING_LISTS != 0) { ?>
<?php echo TABLE_PARTIAL_SHIPMENT_OPTION; ?>
<?php if ($partialship)       
echo "<input type='checkbox' name='partialship' value='1' CHECKED />";
else
echo "<input type='checkbox' name='partialship' value='1' />";
 ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
<input class="normal_button button" type="button" name='postonc' value="<?php echo IMAGE_SEND; ?>" ONCLICK="javascript:document.pos.submit();">
<?php if ($POST[step] == 5)
         echo "</form>";  ?>
</center>
<?php 
if (($postonc == "add_product" || $postonc == "") && ($_POST[step] != "5"))
{ ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo ADD_A_PRODUCT_HEADER; ?></td>
          </tr>
        </table><br /><br />  <?php
	// ############################################################################
	//   Get List of All Products
	// ############################################################################

		//$result = zen_db_query("SELECT products_name, p.products_id, x.categories_name, ptc.categories_id FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id=p.products_id LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON ptc.products_id=p.products_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id=ptc.categories_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " x ON x.categories_id=ptc.categories_id ORDER BY categories_id");
		$result = $db -> Execute("SELECT products_name, p.products_id, categories_name, ptc.categories_id FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id=p.products_id LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON ptc.products_id=p.products_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id=ptc.categories_id ORDER BY categories_name");
		#hile($row = zen_db_fetch_array($result)) 		{
      while (!$result -> EOF){
 		   extract($result->fields,EXTR_PREFIX_ALL,"db");
			$ProductList[$db_categories_id][$db_products_id] = $db_products_name;
			$CategoryList[$db_categories_id] = $db_categories_name;
			$LastCategory = $db_categories_name;
         $result -> MoveNext();
		}

		// ksort($ProductList);

		$LastOptionTag = "";
		$ProductSelectOptions = "<option value='0'>Don't Add New Product" . $LastOptionTag . "\n";
		$ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
		foreach($ProductList as $Category => $Products)
		{
			$ProductSelectOptions .= "<option value='0'>$Category" . $LastOptionTag . "\n";
			$ProductSelectOptions .= "<option value='0'>---------------------------" . $LastOptionTag . "\n";
			asort($Products);
			foreach($Products as $Product_ID => $Product_Name)
			{
				$ProductSelectOptions .= "<option value='$Product_ID'> &nbsp; $Product_Name" . $LastOptionTag . "\n";
			}

			if($Category != $LastCategory)
			{
				$ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
				$ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
			}
		}


	// ############################################################################
	//   Add Products Steps
	// ############################################################################

		echo "<table border='0' align'center'>\n";

		// Set Defaults
			if(!IsSet($add_product_categories_id))
			$add_product_categories_id = 0;

			if(!IsSet($add_product_products_id))
			$add_product_products_id = 0;

		// Step 1: Choose Category
			echo "<tr class=\"dataTableRow\">\n";
			echo "<td class='dataTableContent' align='right'><b>STEP 1:</b></td><td class='dataTableContent' valign='top'>";
			echo ' ' . zen_draw_pull_down_menu('add_product_categories_id', zen_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
			echo "<input type='hidden' name='step' value='2'>";
			echo "</td>\n";
			echo "</form></tr>\n";
			echo "<tr><td colspan='3'>&nbsp;</td></tr>\n";

		// Step 2: Choose Product
		if(($step > 1) && ($add_product_categories_id > 0))
		{
			echo "<tr class=\"dataTableRow\"><form action='$PHP_SELF' method='POST'>\n";
			echo "<td class='dataTableContent' align='right'><b>STEP 2:</b></td><td class='dataTableContent' valign='top'><select name=\"add_product_products_id\" onChange=\"this.form.submit();\">";
			$ProductOptions = "<option value='0'>" .  ADDPRODUCT_TEXT_SELECT_PRODUCT . "\n";
			asort($ProductList[$add_product_categories_id]);
			foreach($ProductList[$add_product_categories_id] as $ProductID => $ProductName)
			{
			$ProductOptions .= "<option value='$ProductID'> $ProductName\n";
			}
			$ProductOptions = str_replace("value='$add_product_products_id'","value='$add_product_products_id' selected", $ProductOptions);
			echo $ProductOptions;
			echo "</select></td>\n";
			echo "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
echo "<input type='hidden' name='ponumber' value='$ponumber'>";
echo "<input type='hidden' name='shippingoption' value='$shippingoption'>";
echo "<input type='hidden' name='shippingaddress' value='$shippingaddress'>";
echo "<input type='hidden' name='billingaddress' value='$billingaddress'>";
echo "<input type='hidden' name='sub' value='$sub'>";
echo "<input type='hidden' name='posubcomments' value='$posubcomments'>";
echo "<input type='hidden' name='plistcomments' value='$plistcomments'>";
echo "<input type='hidden' name='partialship' value='$partialship'>";
			echo "<input type='hidden' name='step' value='3'>";
                        echo "<input type='hidden' name='numberofproducts' value='$numberofproducts'>";
echo "<input type='hidden' name='includepackinglistoption' value='$includeplistoption'>";
                           for ($i=0; $i<=$numberofproducts; $i++) {
	                    echo "<input type='hidden' name='productlistid$i' value='$productlistid[$i]'>";
                            echo "<input type='hidden' name='quantitylist$i' value='$quantitylist[$i]'>";
                            echo "<input type='hidden' name='manufacturerlist$i' value='$manufacturerlist[$i]'>";
                            echo "<input type='hidden' name='productmodellist$i' value='$productmodellist[$i]'>";
                            echo "<input type='hidden' name='productnamelist$i' value='$productnamelist[$i]'>";
                            echo "<input type='hidden' name='attributelist$i' value='$attributelist[$i]'>"; }
			echo "</form></tr>\n";
			echo "<tr><td colspan='3'>&nbsp;</td></tr>\n";
		}

		// Step 3: Choose Options
		if(($step > 2) && ($add_product_products_id > 0))
		{
			// Get Options for Products
			$result = $db -> Execute("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON po.products_options_id=pa.options_id LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON pov.products_options_values_id=pa.options_values_id WHERE products_id='$add_product_products_id'");

			// Skip to Step 4 if no Options
			if($result->RecordCount() == 0)
			{
				echo "<tr class=\"dataTableRow\">\n";
				echo "<td class='dataTableContent' align='right'><b>STEP 3:</b></td><td class='dataTableContent' valign='top' colspan='2'><i>No Options - Skipped...</i></td>";
				echo "</tr>\n";
				$step = 4;
			}
			else
			{
	#			while($row = zen_db_fetch_array($result))  {
            while (!$result -> EOF){
 					extract($result->fields,EXTR_PREFIX_ALL,"db");
					$Options[$db_products_options_id] = $db_products_options_name;
					$ProductOptionValues[$db_products_options_id][$db_products_options_values_id] = $db_products_options_values_name;
               $result -> MoveNext();
				}

				echo "<tr class=\"dataTableRow\"><form action='$PHP_SELF' method='POST'>\n";
				echo "<td class='dataTableContent' align='right'><b>STEP 3:</b></td><td class='dataTableContent' valign='top'>";
                                $optionstoadd=0;
				foreach($ProductOptionValues as $OptionID => $OptionValues)
				{       $optionstoadd++;
                                        $sendoptionon = "add_product_options".$optionstoadd;                    
	   		       		$OptionOption = "<b>" . $Options[$OptionID] . "</b> - <select name='$sendoptionon'>";
					foreach($OptionValues as $OptionValueID => $OptionValueName)
					{
					$OptionOption .= "<option value='$OptionValueID'> $OptionValueName\n";
					}
					$OptionOption .= "</select><br>\n";

					if(IsSet($_POST[$sendoptionon]))
					$OptionOption = str_replace("value='" . $_POST[$sendoptionon] . "'","value='" . $_POST[$sendoptionon] . "' selected",$OptionOption);

					echo $OptionOption;
				}
				echo "<input type='hidden' name='optionstoadd' value='$optionstoadd'></td>";
				echo "<td class='dataTableContent' align='center'><input type='submit' value='" . ADDPRODUCT_TEXT_OPTIONS_CONFIRM . "'>";
				echo "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
				echo "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
                                echo "<input type='hidden' name='numberofproducts' value='$numberofproducts'>";
echo "<input type='hidden' name='ponumber' value='$ponumber'>";
echo "<input type='hidden' name='shippingoption' value='$shippingoption'>";
echo "<input type='hidden' name='shippingaddress' value='$shippingaddress'>";
echo "<input type='hidden' name='billingaddress' value='$billingaddress'>";
echo "<input type='hidden' name='sub' value='$sub'>";
echo "<input type='hidden' name='posubcomments' value='$posubcomments'>";
echo "<input type='hidden' name='plistcomments' value='$plistcomments'>";
echo "<input type='hidden' name='partialship' value='$partialship'>";
echo "<input type='hidden' name='includepackinglistoption' value='$includeplistoption'>";
                                 for ($i=0; $i<=$numberofproducts; $i++) {
	                            echo "<input type='hidden' name='productlistid$i' value='$productlistid[$i]'>";
                                    echo "<input type='hidden' name='quantitylist$i' value='$quantitylist[$i]'>";
                                    echo "<input type='hidden' name='manufacturerlist$i' value='$manufacturerlist[$i]'>";
                                    echo "<input type='hidden' name='productmodellist$i' value='$productmodellist[$i]'>";
                                    echo "<input type='hidden' name='productnamelist$i' value='$productnamelist[$i]'>";
                                    echo "<input type='hidden' name='attributelist$i' value='$attributelist[$i]'>"; }
				echo "<input type='hidden' name='step' value='4'>";
				echo "</td>\n";
				echo "</form></tr>\n";
			}

			echo "<tr><td colspan='3'>&nbsp;</td></tr>\n";
		}

		// Step 4: Confirm
		if($step > 3)
		{
			echo "<tr class=\"dataTableRow\"><form action='$PHP_SELF' method='POST'>\n";
			echo "<td class='dataTableContent' align='right'><b>STEP 4:</b></td>";
			echo "<td class='dataTableContent' valign='top'><input name='add_product_quantity' size='2' value='1'>" . ADDPRODUCT_TEXT_CONFIRM_QUANTITY . "</td>";
			echo "<td class='dataTableContent' align='center'><input type='submit' value='" . ADDPRODUCT_TEXT_CONFIRM_ADDNOW . "'>";

			if($_POST[optionstoadd] != NULL)
			{
                                for ($i=1; $i<=$_POST[optionstoadd]; $i++) {
                                $sendoptionon = "add_product_options".$i;
                                echo "<input type='hidden' name='$sendoptionon' value='$_POST[$sendoptionon]'>"; }
			}
                        echo "<input type='hidden' name='optionstoadd' value='$_POST[optionstoadd]'>";
			echo "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
			echo "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
			echo "<input type='hidden' name='step' value='5'>";
                        echo "<input type='hidden' name='numberofproducts' value='$numberofproducts'>";
echo "<input type='hidden' name='ponumber' value='$ponumber'>";
echo "<input type='hidden' name='shippingoption' value='$shippingoption'>";
echo "<input type='hidden' name='shippingaddress' value='$shippingaddress'>";
echo "<input type='hidden' name='billingaddress' value='$billingaddress'>";
echo "<input type='hidden' name='sub' value='$sub'>";
echo "<input type='hidden' name='posubcomments' value='$posubcomments'>";
echo "<input type='hidden' name='plistcomments' value='$plistcomments'>";
echo "<input type='hidden' name='partialship' value='$partialship'>";
echo "<input type='hidden' name='includepackinglistoption' value='$includeplistoption'>";
                          for ($i=0; $i<=$numberofproducts; $i++) {
	                            echo "<input type='hidden' name='productlistid$i' value='$productlistid[$i]'>";
                                    echo "<input type='hidden' name='quantitylist$i' value='$quantitylist[$i]'>";
                                    echo "<input type='hidden' name='manufacturerlist$i' value='$manufacturerlist[$i]'>";
                                    echo "<input type='hidden' name='productmodellist$i' value='$productmodellist[$i]'>";
                                    echo "<input type='hidden' name='productnamelist$i' value='$productnamelist[$i]'>";
                                    echo "<input type='hidden' name='attributelist$i' value='$attributelist[$i]'>"; }
			echo "</td>\n";
			echo "</form></tr>\n";
		}

		echo "</table></td></tr>\n";
}  ?>

<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
