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
<table border="0" width='100%' cellspacing="0" cellpadding="0">

<!-- body_text //-->
<?php

if(isset($_GET['list_order']))
{
	if($_GET['list_order']=='costumers_name') $disp_order = "customers_name ASC";
	if($_GET['list_order']=='costumers_namedesc') $disp_order = " customers_name DESC";



}else
{

$disp_order = "subcontractors_id ASC";
}




?>
<?php
// send reviewed e-mail
if($_POST[ereview]=='yes') {
$html_msg['EMAIL_MESSAGE_HTML'] = str_replace('
','<br />',$_POST[ebody]);
if ($_POST[includepackinglistoption] == 'yes')
   zen_mail($_POST[eaddress],$_POST[eaddress],$_POST[etitle],$_POST[ebody],PO_FROM_EMAIL_NAME,PO_FROM_EMAIL_ADDRESS, $html_msg, NULL ,PO_PACKINGLIST_FILENAME, 'application/pdf');
else
   zen_mail($_POST[eaddress],$_POST[eaddress],$_POST[etitle],$_POST[ebody],PO_FROM_EMAIL_NAME,PO_FROM_EMAIL_ADDRESS, $html_msg, NULL);
}


// send e-mail or review email before sending
if((isset($_POST[what]) and $_POST[what]=='send' and $_GET['resend'] != 'yes') || ($_GET['resend'] == 'yes')) {
	$k=$_POST[krotnosc];
	$n=0;

	for($p=1; $p<$k; $p++) {
		$pos="pos".$p;
		$sub="sub".$p;
		$id="id".$p;
		$opi="opi".$p;
		$posk[$n]=$_POST[$pos];

		if($posk[$n]=='on') {
			$subk[$n]=$_POST[$sub];
			$idk[$n]=$_POST[$id];
			$opik[$n]=$_POST[$opi];
			$n++;
		}
	}

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

	//zbieranie danych na temat produktow i zamawiajacego
	for($i=0; $i<count($idk); $i++)
	{
		$query=mysql_query("SELECT p.orders_products_id, p.products_model, p.products_name, p.final_price, p.products_quantity,
		o.customers_name, o.customers_street_address, o.customers_city, o.customers_postcode, o.customers_state, o.customers_country, o.customers_telephone, o.customers_email_address,
		o.delivery_name, o.delivery_company, o.delivery_street_address, o.delivery_city, o.delivery_state, o.delivery_postcode, o.delivery_country,
		o.billing_name, o.billing_company, o.billing_street_address, o.billing_city, o.billing_state, o.billing_postcode, o.billing_country,
		o.payment_method,  o.date_purchased, o.currency, o.customers_id, o.orders_id, o.shipping_method, o.orders_status, o.customers_suburb, o.delivery_suburb, o.billing_suburb, o.customers_company
		FROM ".TABLE_ORDERS_PRODUCTS." as p, ".TABLE_ORDERS." as o
		WHERE
		p.orders_id=o.orders_id
		AND
		p.orders_products_id='$idk[$i]'")
		or die('Failed to connect database:  3');
		  // $shipway = '';
		while($row4=mysql_fetch_array($query, MYSQL_NUM))
		{       
			if ($row4[37] != '' && $row4[37] != NULL)
				$adres = $row4[37]."\n";
			else
				$adres = "";
			if ($row4[34] != '' && $row4[34] != NULL)
				$adres .= $row4[6]."\n".$row4[34]."\n".$row4[7].", ".$row4[9]." ".$row4[8]."\n".$row4[10];	
			else
				$adres .= $row4[6]."\n".$row4[7].", ".$row4[9]." ".$row4[8]."\n".$row4[10];
			if ($row4[14] != '' && $row4[14] != NULL)
				$adres_deliver = $row4[14]."\n";
			else
				$adres_deliver = "";
			if ($row4[35] != '' && $row4[35] != NULL)
				$adres_deliver .= $row4[15]."\n".$row4[35]."\n".$row4[16].", ".$row4[17]." ".$row4[18]."\n".$row4[19];
			else
				$adres_deliver .= $row4[15]."\n".$row4[16].", ".$row4[17]." ".$row4[18]."\n".$row4[19];
			if ($row4[21] != '' && $row4[21] != NULL)
				$adres_biling = $row4[21]."\n";
			else
				$adres_biling = "";
			if ($row4[36] != '' && $row4[36] != NULL)
				$adres_biling .= $row4[22]."\n".$row4[36]."\n".$row4[23].", ".$row4[24]." ".$row4[25]."\n".$row4[26];
			else
				$adres_biling .= $row4[22]."\n".$row4[23].", ".$row4[24]." ".$row4[25]."\n".$row4[26];
			$price=$row4[3].' '.$row4[29];
			// $shipway=$row4[32];
			$zawartosc2=array();
			//podmiana tagow dla pliku header
			$zawartosc2[$i]=str_replace("{customers_name}",$row4[5],$zawartosc);
			$zawartosc2[$i]=str_replace("{order_number}",$row4[31],"$zawartosc2[$i]");
			$zawartosc2[$i]=str_replace("{customers_adres}",$adres,"$zawartosc2[$i]");
			$zawartosc2[$i]=str_replace("{customers_phone}",$row4[11],"$zawartosc2[$i]");
			$zawartosc2[$i]=str_replace("{customers_email}",$row4[12],"$zawartosc2[$i]");
			$zawartosc2[$i]=str_replace("{delivery_name}",$row4[13],"$zawartosc2[$i]");
			$zawartosc2[$i]=str_replace("{po_comments}",$_POST[posubcomments],"$zawartosc2[$i]");
			$oatmeal = $db->Execute("select comments from " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = '" . zen_db_input($row4[31]) . "' order by date_added");
			$catmeow = nl2br(zen_db_output($oatmeal->fields['comments']));
			$catmeow=strip_tags($catmeow);
			$zawartosc2[$i]=str_replace("{customers_comments}",$catmeow,"$zawartosc2[$i]");
			if($row4[14]!='')
			{
				$zawartosc2[$i]=str_replace("{delivery_company}",$row4[14],"$zawartosc2[$i]");
			} else	{
				$zawartosc2[$i]=str_replace("{delivery_company}",'-',"$zawartosc2[$i]");
			}

			$zawartosc2[$i]=str_replace("{delivery_adress}",$adres_deliver,"$zawartosc2[$i]");

			if($row4[21]!='')
			{
				$zawartosc2[$i]=str_replace("{billing_company}",$row4[21],"$zawartosc2[$i]");
			} else {
				$zawartosc2[$i]=str_replace("{billing_company}",'-',"$zawartosc2[$i]");
			}

			$zawartosc2[$i]=str_replace("{billing_name}",$row4[20],"$zawartosc2[$i]");
			$zawartosc2[$i]=str_replace("{billing_address}",$adres_biling,"$zawartosc2[$i]");
			$zawartosc2[$i]=str_replace("{payment_method}",$row4[27],"$zawartosc2[$i]");
			$zawartosc2[$i]=str_replace("{date_purchased}",$row4[28],"$zawartosc2[$i]");
if ($row4[32] != PO_CHANGE_SHIPPING_FROM) {

			$zawartosc2[$i] = str_replace("{shipping_method}",$row4[32],"$zawartosc2[$i]");
} else {
			$zawartosc2[$i] = str_replace("{shipping_method}",PO_CHANGE_SHIPPING_TO,"$zawartosc2[$i]");
}

			//ustawianie odpowiednich zmiennych do posortowania w celu uzyskania odpowiedneij ilosci numerow po
			//oraz wygenerowanai odpowiednije ilosci e-maili

			$wielowymiar[$i][0]=$subk[$i]; //id_subcontractors
			$wielowymiar[$i][1]=$row4[30]; //id_customers
			$wielowymiar[$i][2]=$idk[$i]; //id_produktu zamowionego
			$wielowymiar[$i][3]=$zawartosc2[$i]; //zawartosc
			$wielowymiar[$i][4]=$row4[31]; //id_orders
			$wielowymiar[$i][5]=$row4[13]."\n".$adres_deliver;
			$wielowymiar[$i][6]=$row4[20]."\n".$adres_biling;
			$wielowymiar[$i][7]=$row4[32]; //shipping
		}
	}

	$p=0;
	$byly=array();

	for ($i=0; $i<count($wielowymiar); $i++)
	{
		if ($byly[$i]==false)
		{
			$tmpt=array();
			$rowcounttmpt=0;
			$tmpt[$rowcounttmpt][0] = $wielowymiar[$i][0];
			$tmpt[$rowcounttmpt][1] = $wielowymiar[$i][1];
			$tmpt[$rowcounttmpt][2] = $wielowymiar[$i][2];
			$tmpt[$rowcounttmpt][3] = $wielowymiar[$i][3];
			$tmpt[$rowcounttmpt][4] = $wielowymiar[$i][4];
			$tmpt[$rowcounttmpt][5] = $wielowymiar[$i][5];
			$tmpt[$rowcounttmpt][6] = $wielowymiar[$i][6];
			$tmpt[$rowcounttmpt][7] = $wielowymiar[$i][7];
			$rowcounttmpt++;
			$byly[$i]=true;

			for ($j=$i+1; $j<count($wielowymiar); $j++)
			{
				if (($wielowymiar[$j][0]==$wielowymiar[$i][0]) && ($wielowymiar[$j][4]==$wielowymiar[$i][4]))
				{
					$tmpt[$rowcounttmpt][0] = $wielowymiar[$j][0];
 					$tmpt[$rowcounttmpt][1] = $wielowymiar[$j][1];
					$tmpt[$rowcounttmpt][2] = $wielowymiar[$j][2];
					$tmpt[$rowcounttmpt][3] = $wielowymiar[$j][3];
					$tmpt[$rowcounttmpt][4] = $wielowymiar[$i][4];
					$tmpt[$rowcounttmpt][5] = $wielowymiar[$i][5];
					$tmpt[$rowcounttmpt][6] = $wielowymiar[$i][6];
					$tmpt[$rowcounttmpt][7] = $wielowymiar[$i][7];
					$rowcounttmpt++;
					$byly[$j]=true;
				}
			}

			$tresc_ostateczna='';
			$trescik='';
			$newzawartosc='';

			//wybieranie dpowiedniego produktu i dodanie go do szablonu e-mail
			//odpowiednie tagi zpliku email_products sa zastepowane zmiennymi
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
			$pdf->addClient($tmpt[0][4]);
			$pdf->addClientShipAdresse($tmpt[0][5]);
			$pdf->addClientBillAdresse($tmpt[0][6]);
			$oatmeal = $db->Execute("select comments from " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = '" . zen_db_input($tmpt[0][4]) . "' order by date_added");
			$catmeow = nl2br(zen_db_output($oatmeal->fields['comments']));
			if ($catmeow != '' && $catmeow != NULL && $_POST[addcommentstoplist] == 1) {
				$catmeow=strip_tags($catmeow);
				$pdf->addReference($catmeow);  }
			$cols=array( PACKING_LIST_MODEL_NUMBER    => 40,
           			     PACKING_LIST_PRODUCT_DESCRIPTION  => 120.9,
        			     PACKING_LIST_QUANTITY     => 25 );
			$pdf->addCols( $cols);
			$cols=array( PACKING_LIST_MODEL_NUMBER    => "L",
       			      	     PACKING_LIST_PRODUCT_DESCRIPTION  => "L",
       			             PACKING_LIST_QUANTITY     => "C" );
			$pdf->addLineFormat( $cols);
			$pdf->addLineFormat($cols);
			$y    = 89;
			$countproductsonpo=0;
			for($h=0; $h<count($tmpt); $h++)
			{
				$tm=$tmpt[$h][2];
				$tm1=$tmpt[$h][4];
				$result9=mysql_query("SELECT products_model, products_name, final_price, products_quantity, products_id
									  FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_products_id='$tm'")
				or die("Failed to connect database: ");
				$row9=mysql_fetch_array($result9, MYSQL_NUM);
				$trescik=$tresc_robij1;
				$manufacturernamed=zen_get_products_manufacturers_name($row9[4]);
				$trescik=str_replace("{manufacturers_name}",$manufacturernamed,$trescik);
				$trescik=str_replace("{products_name}",$row9[1],$trescik);
				$trescik=str_replace("{products_model}",$row9[0],$trescik);
				$trescik=str_replace("{final_price}",$row9[2],$trescik);
				$trescik=str_replace("{products_quantity}",$row9[3],$trescik);
				$result9a=mysql_query("SELECT orders_id, orders_products_id, products_options, products_options_values
									  FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES."
									  WHERE orders_products_id='$tm' AND orders_id='$tm1'")
				or die("Failed to connect database: ");
				$attributes='';
				while($row9a=mysql_fetch_array($result9a, MYSQL_NUM))
				{
					$attributes=$attributes.$row9a[2].": ".$row9a[3]."\n";
				}

				$trescik=str_replace("{products_attributes}",$attributes,$trescik);

				$tresc_ostateczna=$tresc_ostateczna.$trescik;
				$newzawartosc=$tmpt[0][3].$tresc_ostateczna;
				$line = array( PACKING_LIST_MODEL_NUMBER    => $row9[0],
              				      PACKING_LIST_PRODUCT_DESCRIPTION  => $row9[1] . " " .
                   		              $attributes,
             				      PACKING_LIST_QUANTITY     => $row9[3]);
				$size = $pdf->addLine( $y, $line );
				$y   += $size + 2;
				$countproductsonpo++;
			}
$querycp=mysql_query("SELECT orders_products_id FROM ".TABLE_ORDERS_PRODUCTS."  WHERE  orders_id='$tm1'  ")
			or die('Failed to connect database: 8');

			$countproducts=0;
			while($rowcp=mysql_fetch_array($querycp, MYSQL_NUM))
			{
 $countproducts++;
}
			//wybieranie adresu pczty email poddostawcy
			$dlaemaila= ($tmpt[0][0]!='0') ? $tmpt[0][0] : 0;
			$query22=mysql_query("SELECT * FROM ".TABLE_SUBCONTRACTORS." WHERE subcontractors_id='$dlaemaila'")
			or die("Failed to connect database: 1");
			$subcontractor=mysql_fetch_assoc($query22);
			$adresdo=$subcontractor['email_address'];
			/* if ($dlaemaila==0) $adresdo=PO_OWN_STOCK_EMAIL; */

			//generowanie kodu, tracking link oraz, tematu
			$row110[0]='';
			//$vars = get_defined_vars();
			//print_r($vars);
			//$check_if_po_sent = mysql_query("SELECT * FROM orders_products WHERE orders_products_id = '$tm'");
			//$if_po_sent = mysql_fetch_assoc($check_if_po_sent);
			//$po_sent = $if_po_sent['po_sent'];

			//if ($po_sent == 0) {
			$query110=mysql_query("SELECT max(po_number) FROM ".TABLE_ORDERS_PRODUCTS."")
			or die("Failed to connect database: ");
			$row110=mysql_fetch_array($query110, MYSQL_NUM);
			$kod=$row110[0]+1;
			//} else {
			//	$query110=mysql_query("SELECT * FROM orders_products WHERE orders_products_id = '$tm'")
			//	or die("Failed to connect database: ");
			//	$row110=mysql_fetch_array($query110, MYSQL_NUM);
			//	$kod=$row110[0];
			//}
			if($row110[0]=='')
			{
				$kod=$kod."1";
			}	else {
				// TO TRY AND RID OF THE INCREMENTING PO_NUMBER -- $kod=$row110[0]+1;
				//$kod=$row110[0]+1;
				//$kod=$row110[0];
			}

			$newzawartosc=str_replace("{po_number}",$wielowymiar[$i][4]."-".$kod,$newzawartosc);
                        $tematk=PO_SUBJECT;
			$tematk=str_replace("{po_number}",$wielowymiar[$i][4]."-".$kod,$tematk);
			$tematk=str_replace("{contact_person}",$subcontractor['contact_person'],$tematk);
			$tematk=str_replace("{full_name}",$subcontractor['full_name'],$tematk);
		        $tematk=str_replace("{short_name}",$subcontractor['short_name'],$tematk);
                        $tematk = str_replace("{order_number}",$wielowymiar[$i][4],$tematk);
			$tracking_link_1='<a href="'.HTTP_SERVER.DIR_WS_ADMIN.'confirm_track_sub.php?x='.$dlaemaila.'&y='.$kod.'">'.HTTP_SERVER.DIR_WS_ADMIN.'confirm_track_sub.php?x='.$dlaemaila.'&y='.$kod.'</a>';

			for($t=0; $t<=count($tracking_link); $t++)
			{
				$tracking_link_good=$tracking_link_good.str_replace("{tracking_link}",$tracking_link_1,$tracking_link[$t]);
			}

		  $newzawartosc=$newzawartosc.$tracking_link_good;
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
if ($tmpt[0][7] != PO_CHANGE_SHIPPING_FROM) {

			$newzawartosc = str_replace("{shipping_method}",$tmpt[0][7],$newzawartosc);
} else {
			$newzawartosc = str_replace("{shipping_method}",PO_CHANGE_SHIPPING_TO,$newzawartosc);
}
$passitw=$wielowymiar[$i][4];
$query978=mysql_query("SELECT orders_status FROM ".TABLE_ORDERS." WHERE orders_id='$passitw'")
			or die("Failed to connect database: 1");
$row978=mysql_fetch_array($query978, MYSQL_NUM);
if ($row978[0] == 1) {			
$query555=mysql_query("INSERT INTO ".TABLE_ORDERS_STATUS_HISTORY."
				(orders_status_id, orders_id, date_added,
 					customer_notified, comments)
  				 values ('2','$tm1',now(),'0','".PO_SENT_COMMENTS."')")
				 or die(mysql_error());
mysql_query("update " . TABLE_ORDERS . "
                        set orders_status = '2', last_modified
 =
 now()
                        where orders_id ='$tm1'");
}
			//wysylanie e-maila
			if (PURCHASEORDERS_DEBUG == 'Yes') {
			echo "<br>DEBUG--><br>From   :".PO_FROM_EMAIL_NAME." &lt;".PO_FROM_EMAIL_ADDRESS."&gt;<br>To     :".$adresdo."<br>Subject:".$tematk."<br>Content:<br>".str_replace("\n","<br>",$newzawartosc);
		  }
if ($countproductsonpo != $countproducts)
     $pdf->addNotes(SHIPPING_OPTION.": ".$tmpt[0][7]."\n\n".PO_PARTIALSHIP_PACKINGLIST."\n".$_POST[plistcomments]."\n"."PO NUMBER:".$wielowymiar[$i][4]."-".$kod);        
else
     $pdf->addNotes(SHIPPING_OPTION.": ".$tmpt[0][7]."\n\n".PO_FULLSHIP_PACKINGLIST."\n".$_POST[plistcomments]."\n"."PO NUMBER:".$wielowymiar[$i][4]."-".$kod);
      $pdf->Output(PO_PACKINGLIST_FILENAME, "F");
if ($_POST[reviewthensend] == 'yes') {
?>
<form name="editpo" action="send_pos.php" method="POST">
<center><?php echo REVIEW_EMAIL_EMAIL_TITLE; ?>&nbsp;<input type="text" name="etitle" size="125" value="<?php echo $tematk; ?>" /><br /><br />
<?php echo REVIEW_EMAIL_SEND_EMAIL_TO; ?>&nbsp;<input type="text" name="eaddress" size="125" value="<?php echo $adresdo; ?>" /><br /><br />
<textarea rows="30" name="ebody"><?php echo $newzawartosc; ?></textarea>
<input type="hidden" name="includepackinglistoption" value="<?php echo $_POST[includepackinglistoption]; ?>" /><input type="hidden" name="ereview" value="yes" /><br /><br />
<input type="image" src="includes/languages/english/images/buttons/button_send.gif" name='insert' ONCLICK="javascript:document.pos.submit();"><br /><br />
<?php echo REVIEW_AND_SUBMIT_WARNING; ?></center>
</form><?php } else {   $html_msg['EMAIL_MESSAGE_HTML'] = str_replace('
','<br />',$newzawartosc);
                        if ($_POST[includepackinglistoption] == 'yes')
			   zen_mail($adresdo,$adresdo,$tematk,$newzawartosc,PO_FROM_EMAIL_NAME,PO_FROM_EMAIL_ADDRESS, $html_msg, NULL, PO_PACKINGLIST_FILENAME, 'application/pdf');
                        else
                           zen_mail($adresdo,$adresdo,$tematk,$newzawartosc,PO_FROM_EMAIL_NAME,PO_FROM_EMAIL_ADDRESS, $html_msg, NULL);
}
			// mail($adresdo, $tematk, $newzawartosc, $po_from);
			$tracking_link_good='';
			$date=date('Y-m-d');
// unlink($pdffilename); 
			for($m=0; $m<count($tmpt); $m++)
			{
				$tm=$tmpt[$m][2];
				$tm2=$tmpt[$m][0];

				// $check_if_po_sent = mysql_query("SELECT * FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_products_id = '$tm'");
				// $if_po_sent = mysql_fetch_assoc($check_if_po_sent);
				// $po_sent = $if_po_sent['po_sent'];

				
				$result=mysql_query("UPDATE ".TABLE_ORDERS_PRODUCTS." SET po_sent='1', item_shipped=0, po_number='$kod', po_sent_to_subcontractor='$tm2', po_date='$date' WHERE  orders_products_id='$tm' LIMIT 1")	or die("Failed to connect database: 5");
				
			}
		}
	}
} 
if($_POST[reviewthensend] != 'yes') { ?>
<tr><td class="pageHeading" colspan="2"><?php  echo BOX_CUSTOMERS_SEND_POS; ?><br><br></td></tr>
<tr>  <td valign="top"><?php echo REFRESH_WARNING; ?></td></tr>
           <tr>  <td valign="top"><br>

		   <?php
		   if($_POST[co]=='old')
			{
		   function sub2()
			{
			$query2=mysql_query("SELECT subcontractors_id,short_name FROM ".TABLE_SUBCONTRACTORS." ORDER BY short_name")
			or die('Failed to connect database: ');

			/*$query99=mysql_query("SELECT  subcontractors_id,short_name FROM subcontractors ORDER BY short_name")
			or die('Failed to connect database: ');*/

			echo "<select name='sub11'>".
			"<option value='%'>".TABLE_ALL_SUBCONTRACTORS."</option>";
			while($row22=mysql_fetch_array($query2, MYSQL_NUM))
			{
			echo "<option value='$row22[0]'>$row22[1]</option>";
			}
			echo '</select>';
			}

//przejscie do szbalonu ktory wyswietla wyslane juz e-maile z starymi numerami po
		   echo	"<form name='drugi' action='send_pos.php' method='post'>
		   <input type='submit' name='old' value='".BUTTON_NEW."'>
		   </form><br>&nbsp;";
//wyszukiwarka
		   echo '<form name="wyszukiwarka" action="send_pos.php" method="POST">
		   <input type="hidden" name="co" value="old">
		   <table border="0" width="100%" cellspacing="0" cellpadding="0">
		   		<tr><td align="center" colspan="2">'.TABLE_DATA_FROM_DATES.': <input type="text" size="10" name="data_od"> '.TABLE_TO.' <input type="text" size="10" name="data_do">
				&nbsp;&nbsp;'.TABLE_PO_PREOVIOUS_NUMBER.': <input type="text" size="10" name="po_number">&nbsp;&nbsp;
				'.TABLE_ORDER_NUMBER.': <input type="text" size="10" name="orders_num">&nbsp;&nbsp;
				'.TABLE_SUBCONTRACTOR.': ';
				sub2();
				echo '&nbsp;&nbsp;'.TABLE_SHOW_DELIVERED_ORDERS.' <input type="checkbox" name="showdeliv" value="1">&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="co2" value="wyswietl"></form><input type="image" src="includes/languages/english/images/buttons/button_search.gif" name="insert1" ONCLICK="javascript:document.wyszukiwarka.submit();"><br><br></td></tr></table></form>';


			}else
			{

			echo "<form name='pierwszy' action='send_pos.php' method='post'>
		   <input type='submit' name='old' value='".BUTTON_OLD."'>
		   <input type='hidden' name='co' value='old'>
		   </form><br>&nbsp;";
			}

		//wyglad szablonu dla staryc po
		   if($_POST[co]=='old')
			{
			?>
			 <table border="0" width="100%" align="center" cellspacing="0" cellpadding="0">
              <tr class="dataTableHeadingRow">
                <td width='12%' class="dataTableHeadingContent" align="center" >
                  <?php  echo TABLE_ORDER_NUMBER; ?>
                </td>
                <td width='12%' class="dataTableHeadingContent" align="center">
                  <?php echo TABLE_CUSTOMER_NAME;  ?><br>

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
			</tr><form name='pos' method='post' action='send_pos.php?resend=yes'>


			<?php
//pobieranie danych ktore sa wprowadzone w wyszukiwarce
			if(isset($_POST[co2]) AND $_POST[co2]=='wyswietl')
			{

			if($_POST[data_od]!='')
			{
			$data_od=$_POST[data_od];
			$zmienna2="AND (p.po_date>='$data_od')";
			}else
			{
			$zmienna2='';
			}

			if($_POST[data_do]!='')
			{
			$data_do=$_POST[data_do];
			$zmienna1="AND (p.po_date<='$data_do')";
			}else
			{
			$zmienna1='';
			}

			if($_POST[showdeliv]!='1')
			{
			$zmienna3="AND (o.orders_status != 3)";
			}else
			{
			$zmienna3='';
			}

			if($_POST[po_number]!='')
			{
			$po_number=$_POST[po_number];
			}else
			{
			$po_number='%';
			}

			if($_POST[orders_num]!='')
			{
			$orders_num=$_POST[orders_num];
			}else
			{
			$orders_num='%';
			}

			if($_POST[sub11]!='')
			{
			$sub1=$_POST[sub11];
			}else
			{
			$sub1='%';
			}



//generowanie pola typu select ktory ma za zadanie wyswietlanie odpowiedniego subcontracotra dla odpowiedniego produktu
			function sub($name, $i)
			{

			$query2=mysql_query("SELECT  subcontractors_id,short_name FROM ".TABLE_SUBCONTRACTORS." ORDER BY short_name")
			or die('Failed to connect database: ');
			$query232=mysql_query("SELECT products_id, default_subcontractor FROM ".TABLE_PRODUCTS." WHERE products_id='$name'")
			or die ("Nie mzona sie polaczcy z baza danych");
			$row232=mysql_fetch_array($query232, MYSQL_NUM);

			echo "<select name='sub$i'>";
			while($row22=mysql_fetch_array($query2, MYSQL_NUM))
			{

			echo "<option value='$row22[0]'";

			if($row232[1]==$row22[0])
			{
			echo "selected";
			}
			echo ">$row22[1]</option>";
			}
			echo "</select>";
			}






			$query=mysql_query("SELECT p.orders_products_id, p.orders_id, p.orders_products_id, p.products_name, p.po_number,  p.po_sent_to_subcontractor, p.products_id, p.po_date  FROM ".TABLE_ORDERS_PRODUCTS." as p, ".TABLE_ORDERS." as o WHERE  p.orders_id=o.orders_id AND p.po_sent='1'
			AND  (p.orders_id LIKE '$orders_num') AND (p.po_number LIKE '$po_number') AND  (p.po_sent_to_subcontractor LIKE '$sub1') $zmienna2 $zmienna1 $zmienna3 ORDER BY orders_id DESC")
			or die('Failed to connect database: 8');





//wyswietlanie danych
			$i=1;
			while($row2=mysql_fetch_array($query, MYSQL_NUM))
			{



			$query3=mysql_query("SELECT * FROM ".TABLE_ORDERS." as o, ".TABLE_ORDERS_PRODUCTS." as p WHERE o.orders_id = o.orders_id AND o.orders_id='$row2[1]'")
			or die('Failed to connect database: ');
			/* if ($row2[5]==0)
			{
			$row100[0]="Own stock";
			}else
			{ */
			$query100=mysql_query("SELECT short_name FROM ".TABLE_SUBCONTRACTORS." WHERE subcontractors_id='$row2[5]'")
			or die('Failed to connect database: ');
			$row100=mysql_fetch_array($query100, MYSQL_NUM);
			/* } */

			$row3=mysql_fetch_array($query3, MYSQL_NUM);


			if($i%2==1)
			{

echo "<tr class='dataTableRowSelected'>".
			"<td  align='center'>$row2[1]</td><td  align='center'>$row3[2]</td><td  align='center'>$row2[3]</td><td align='center'><input type='checkbox' name='pos$i'>".

			 "</td><td  align='center'>";
			sub($row2[6], $i);
			echo "</td>".
			"<td  align='center'>$row100[0]</td>".
			"<td  align='center'>$row2[4]</td>".
			"<td  align='center'>$row2[7]</td>".
			"</tr><input type='hidden' name='opi$i' value='$row2[2]'><input type='hidden' name='id$i' value=$row2[0]>";

			}

			if($i%2==0)
			{

echo "<tr class='dataTableRow'>".
			"<td  align='center'>$row2[1]</td><td  align='center'>$row3[2]</td><td  align='center'>$row2[3]</td><td align='center'><input type='checkbox' name='pos$i'>".

			 "</td><td  align='center'>";
			sub($row2[6], $i);
			echo "</td>".
			"<td  align='center'>$row100[0]</td>".
			"<td  align='center'>$row2[4]</td>".
			"<td  align='center'>$row2[7]</td>".
			"</tr><input type='hidden' name='opi$i' value='$row2[2]'><input type='hidden' name='id$i' value=$row2[0]>";

			}

			$i++;
			}
			echo "<input type='hidden' name='krotnosc' value='$i'>";




   ?><input type='hidden' name='what' value='send'>
 <tr><td colspan='8'align='center'><br><br></td></tr>
 <tr><td colspan='8'align='center'><?php echo TABLE_COMMENTS_FOR_POS; ?>:&nbsp;<input type="text" name="posubcomments" size="90"></td></tr>
<tr><td colspan='8'align='center'><br><br></td></tr>
<?php if (PO_SEND_PACKING_LISTS != 0) { ?>
<tr><td colspan='8'align='center'><?php echo TABLE_COMMENTS_FOR_PACKING_LISTS; ?>:&nbsp;<input type="text" name="plistcomments" size="90" maxlength="90"></td></tr><?php } ?>
<tr><td colspan='8'align='center'><br></td></tr>
<tr><td colspan='8'align='center'><?php echo COMMENTS_WARNING; ?></td></tr>
<tr><td colspan='8'align='center'><br><br></td></tr><tr><td colspan='8'align='center'>
<?php if (PO_SEND_PACKING_LISTS == 0) { ?>
<input type="hidden" name="includepackinglistoption" value="no"> <?php } ?>
<?php if (PO_SEND_PACKING_LISTS == 1) { ?>
<input type="hidden" name="includepackinglistoption" value="yes"> <?php } ?>
<?php if (PO_SEND_PACKING_LISTS == 2) { ?>
<?php echo TABLE_INCLUDE_PACKINGLIST_OPTION; ?><input type="checkbox" name="includepackinglistoption" value="yes" CHECKED>&nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
<?php if (PO_SEND_PACKING_LISTS == 3) { ?>
<?php echo TABLE_INCLUDE_PACKINGLIST_OPTION; ?><input type="checkbox" name="includepackinglistoption" value="yes">&nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
<?php if (PO_SEND_PACKING_LISTS != 0) { ?>
<?php echo TABLE_ADD_CUSTOMERS_COMMENTS_TO_PACKING_LIST; ?><input type="checkbox" name="addcommentstoplist" value="1" CHECKED>&nbsp;&nbsp;&nbsp;&nbsp;<?php } echo TABLE_REVIEW_EMAIL_OPTION; ?><input type="checkbox" name="reviewthensend" value="yes" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="includes/languages/english/images/buttons/button_send.gif" name='insert' ONCLICK="javascript:document.pos.submit();"></td></tr></form>

		</table>
<?php }


			}else
			{
//generowanie szablonu dla nie wysllanych numerow po
		   ?>
		   <table border="0" width="100%" align="center" cellspacing="0" cellpadding="0">
              <tr class="dataTableHeadingRow">
                <td width='10%' class="dataTableHeadingContent" align="center" >
                  <?php  echo TABLE_ORDER_NUMBER; ?>
                </td>
        
<td width='20%' class="dataTableHeadingContent" align="center">
                  <?php echo TABLE_ORDER_COMMENTS;  ?><br>
</td>
<td width='15%' class="dataTableHeadingContent" align="center">
                  <?php echo TABLE_ORDER_SHIPPING;  ?><br>

                </td>
 <td width='20%' class="dataTableHeadingContent" align="center">
                  <?php echo TABLE_ORDER_ADDRESS;  ?><br>

                </td>
<td width='5%' class="dataTableHeadingContent" align="center">
                  <?php echo TABLE_ORDER_PRODUCT_MANUFACTURER; ?><br>


                </td>
				<td width='15%' class="dataTableHeadingContent" align="center">
                  <?php echo TABLE_PRODUCTS_NAME; ?><br>


                </td>
                <td width='5%' class="dataTableHeadingContent" align="center">
                  <?php echo TABLE_SEND_PO; ?><br>
				</td>
                <td width='10%' class="dataTableHeadingContent" align="center">
                  <?php echo TABLE_PO_SUBCONTRACTOR; ?><br>

                </td>

			</tr><form name='pos' method='post' action='send_pos.php'>
			<?php


			function sub($name, $i)
			{

			$query2=mysql_query("SELECT  subcontractors_id,short_name FROM ".TABLE_SUBCONTRACTORS." ORDER BY short_name")
			or die('Failed to connect database: ');
			$query232=mysql_query("SELECT products_id, default_subcontractor FROM ".TABLE_PRODUCTS." WHERE products_id='$name'")
			or die ("Nie mzona sie polaczcy z baza danych");
			$row232=mysql_fetch_array($query232, MYSQL_NUM);

			echo "<select name='sub$i'>";
			while($row22=mysql_fetch_array($query2, MYSQL_NUM))
			{
			echo "<option value='$row22[0]'";

			if($row232[1]==$row22[0])
			{
			echo "selected";
			}
			echo ">$row22[1]</option>";
			}
			echo "</select>";
			}



			$a=$_GET["a"];
$l_odp_napasku='10';
$l_odp_nastronie='100';
$start=$a*$l_odp_nastronie;

$skrypt="send_pos.php?";


			$queryxx=mysql_query("SELECT p.orders_products_id, p.orders_id, p.orders_products_id, p.products_name, p.products_id, o.shipping_method, o.delivery_state, p.products_quantity, o.delivery_street_address, o.delivery_city, o.delivery_suburb, o.delivery_postcode, o.delivery_country, o.delivery_company, o.delivery_name, p.products_model FROM ".TABLE_ORDERS_PRODUCTS." as p, ".TABLE_ORDERS." as o WHERE  p.orders_id=o.orders_id AND po_sent='0' AND o.orders_status != 3 AND po_number  IS NULL")
			or die('Failed to connect database: 8');

			$l_odp = mysql_num_rows($queryxx);

			$query=mysql_query("SELECT p.orders_products_id, p.orders_id, p.orders_products_id, p.products_name, p.products_id, o.shipping_method, o.delivery_state, p.products_quantity, o.delivery_street_address, o.delivery_city, o.delivery_suburb, o.delivery_postcode, o.delivery_country, o.delivery_company, o.delivery_name, p.products_model FROM ".TABLE_ORDERS_PRODUCTS." as p, ".TABLE_ORDERS." as o WHERE  p.orders_id=o.orders_id AND po_sent='0' AND o.orders_status !=3 AND po_number  IS NULL LIMIT $start, $l_odp_nastronie")
			or die('Failed to connect database: 8');

			$i=1;
			while($row2=mysql_fetch_array($query, MYSQL_NUM))
			{
                        $resultpa=mysql_query("SELECT orders_id, orders_products_id, products_options, products_options_values
									  FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES."
									  WHERE orders_products_id='$row2[0]' AND orders_id='$row2[1]'")
				or die("Failed to connect database: ");
                        $attributes='';
                        while($rowpa=mysql_fetch_array($resultpa, MYSQL_NUM))
				{
					$attributes=$attributes."<br />".$rowpa[2].": ".$rowpa[3];
				}
			

$oatmeal = $db->Execute("select comments from " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = '" . zen_db_input($row2[1]) . "' order by date_added");
$catmeow = nl2br(zen_db_output($oatmeal->fields['comments']));
$manufacturernamed= zen_get_products_manufacturers_name($row2[4]);

if ($row2[12] == zen_get_country_name(STORE_COUNTRY))
	$orderaddresscountry="";
else
	$orderaddresscountry="<br />".$row2[12];
if ($row2[10] == "" || $row2[10] == NULL)
	$orderaddresssuburb="";
else
	$orderaddresssuburb="<br />".$row2[10];
if ($row2[13] == "" || $row2[13] == NULL)
	$orderaddresscompany="";
else
	$orderaddresscompany=$row2[13]."<br />";
$ordersaddress = $orderaddresscompany.$row2[8].$orderaddresssuburb."<br />".$row2[9].", ".$row2[6]." ".$row2[11].$orderaddresscountry; 
			if($i%2==1)
                           echo "<tr class='dataTableRowSelected'>";
                        else
                           echo "<tr class='dataTableRow'>";
			echo "<td  align='center'>$row2[1]</td><td  align='center'>$catmeow</td><td  align='center'>$row2[5]</td><td  align='center'>$row2[14]<br />$ordersaddress</td><td  align='center'>$manufacturernamed $row2[15]</td><td  align='center'>$row2[7] x $row2[3] $attributes</td><td align='center'><input type='checkbox' name='pos$i'>".

			 "</td><td  align='center'>";
			sub($row2[4], $i);
			echo "</td>".
			"</tr><input type='hidden' name='opi$i' value='$row2[2]'><input type='hidden' name='id$i' value=$row2[0]>";
			$i++;
			}
			echo "<input type='hidden' name='krotnosc' value='$i'>";




   ?><input type='hidden' name='what' value='send'>
   <?php pasek($l_odp,$l_odp_nastronie,$l_odp_napasku,$skrypt,$a);  ?>
 <tr><td colspan='9'align='center'><br><br></td></tr>
 <tr><td colspan='9'align='center'><?php echo TABLE_COMMENTS_FOR_POS; ?>:&nbsp;<input type="text" name="posubcomments" size="90"></td></tr>
<tr><td colspan='9'align='center'><br><br></td></tr>
<?php if (PO_SEND_PACKING_LISTS != 0) { ?>
<tr><td colspan='9'align='center'><?php echo TABLE_COMMENTS_FOR_PACKING_LISTS; ?>:&nbsp;<input type="text" name="plistcomments" size="90" maxlength="90"></td></tr><?php } ?>
<tr><td colspan='9'align='center'><br></td></tr>
<tr><td colspan='9'align='center'><?php echo COMMENTS_WARNING; ?></td></tr>
<tr><td colspan='9'align='center'><br><br></td></tr><tr><td colspan='8'align='center'>
<?php if (PO_SEND_PACKING_LISTS == 0) { ?>
<input type="hidden" name="includepackinglistoption" value="no"> <?php } ?>
<?php if (PO_SEND_PACKING_LISTS == 1) { ?>
<input type="hidden" name="includepackinglistoption" value="yes"> <?php } ?>
<?php if (PO_SEND_PACKING_LISTS == 2) { ?>
<?php echo TABLE_INCLUDE_PACKINGLIST_OPTION; ?><input type="checkbox" name="includepackinglistoption" value="yes" CHECKED>&nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
<?php if (PO_SEND_PACKING_LISTS == 3) { ?>
<?php echo TABLE_INCLUDE_PACKINGLIST_OPTION; ?><input type="checkbox" name="includepackinglistoption" value="yes">&nbsp;&nbsp;&nbsp;&nbsp; <?php } ?>
<?php if (PO_SEND_PACKING_LISTS != 0) { ?>
<?php echo TABLE_ADD_CUSTOMERS_COMMENTS_TO_PACKING_LIST; ?><input type="checkbox" name="addcommentstoplist" value="1" CHECKED />&nbsp;&nbsp;&nbsp;&nbsp;<?php } echo TABLE_REVIEW_EMAIL_OPTION; ?><input type="checkbox" name="reviewthensend" value="yes" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" src="includes/languages/english/images/buttons/button_send.gif" name='insert' ONCLICK="javascript:document.pos.submit();"></td></tr></form>
<tr><td colspan='9'align='center'><br><br></td></tr>
		</table>
<?php } ?>
		</td>

      </tr>
</table>

<?php } require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
