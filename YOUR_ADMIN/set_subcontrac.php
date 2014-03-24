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


if(isset($_GET['list_order']))
{
	if($_GET['list_order']=='modelname') $disp_order = "products_model ASC";
	if($_GET['list_order']=='modelnamedesc') $disp_order = "products_model DESC";

	

}else
{

$disp_order = "products_id ASC";
}


if(isset($_POST[krotnosc]))
{

$krotnosc=$_POST[krotnosc];

for($i=1; $i<=$krotnosc; $i++)
{
$id_product[$i]=$_POST[$i];

}

//zapisywanie wszytskich ustawien dla odpowiednich wierszy w tabeli products

for($i=1; $i<count($id_product); $i++)
{

$sub="sub".$id_product[$i];
if($_POST[$sub]!='own_stock')
{

$numer=$_POST[$sub];
$result=mysql_query("UPDATE ".TABLE_PRODUCTS." SET default_subcontractor='$numer' WHERE  products_id ='$id_product[$i]' LIMIT 1")
or die("Nie mozna sie polaczyc z baza danych5");

}else
{

$numer="0";
$result=mysql_query("UPDATE ".TABLE_PRODUCTS." SET default_subcontractor='$numer' WHERE  products_id ='$id_product[$i]' LIMIT 1")
or die("Nie mozna sie polaczyc z baza danych5");


}

}


}
//projekt szablonu 

?><tr><td class="pageHeading" colspan="2"><br><?php  echo TABLE_SET_SUBC_HEADING; ?><br><br></td></tr>
           <tr>  <td valign="top" width='80%'>
		   <table border="0" width='100%' cellspacing="0" cellpadding="0">
              <tr class="dataTableHeadingRow">
                <td width='5%' class="dataTableHeadingContent" align="center" valign="top">
                  <?php  echo ID; ?>
                </td>
                <td width='20%' class="dataTableHeadingContent" align="center">
                  <?php echo TABLE_SET_SUBC_MODEL;  ?><br>
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=modelname'); ?>"><?php echo ($_GET['list_order']=='modelname' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=modelnamedesc'); ?>"><?php echo ($_GET['list_order']=='modelnamedesc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>
				<td width='20%' class="dataTableHeadingContent" align="center" valign="top">
                  <?php echo TABLE_SET_SUBC_PRODUCTS_NAME; ?><br>
				</td>
                <td width='20%' class="dataTableHeadingContent"  align='center' valign="top">
                  <?php echo TABLE_SET_SUBC_MANUFACTURER; ?><br>
				                  </td>
				
               	<td width='20%' class="dataTableHeadingContent" align="center" valign='top'>
                  <?php echo TABLE_SET_SUBC_DEFAULT; ?><br>
				 </td>
					
              </tr>
       <form name='set' action='set_subcontrac.php?' method='post'>
                 
                    <?php
	//wyswietlanie pola typu select dla odpowiednich subcotractorow				
					function sub($name)
			{
			$query33=mysql_query("SELECT  default_subcontractor, products_id FROM ".TABLE_PRODUCTS." WHERE products_id='$name'");
			$row33=mysql_fetch_array($query33, MYSQL_NUM);
			
			

			echo "<select name='sub$name'>";
			
			/*if($row33[0]==0)
			{
			echo "<option value='own_stock'>Own stock</option>";
			$query2=mysql_query("SELECT  subcontractors_id,short_name FROM ".TABLE_SUBCONTRACTORS." ORDER BY short_name")
			or die('Nie mozna sie polaczyc z baza danych');
			while($row22=mysql_fetch_array($query2, MYSQL_NUM))
			{
			echo "<option value='$row22[0]'>$row22[1]</option>";
			}
			echo "</select>";
			}
			else
			{ */
			
			$query2=mysql_query("SELECT  subcontractors_id,short_name FROM ".TABLE_SUBCONTRACTORS." ORDER BY short_name")
			or die('Nie mozna sie polaczyc z baza danych');
			while($row22=mysql_fetch_array($query2, MYSQL_NUM))
			{
			
			if($row22[0]==$row33[0]) 
			{
			echo "<option value='$row22[0]' selected>$row22[1]</option>";
			}else
			{
			echo "<option value='$row22[0]'>$row22[1]</option>";		
			}
			
			}
						echo "</select>";
			}
				
			// }			
//generowanie zmienncyh i przypisywanie zmiennych dla porcjowania danych
$a=$_GET["a"];
$l_odp_napasku='10';
$l_odp_nastronie='20';
$start=$a*$l_odp_nastronie;
$i=0;

//zapytanie ktore pobiera dane z bazy 						
					$query=mysql_query("SELECT products_id, products_model, manufacturers_id FROM ".TABLE_PRODUCTS." order by $disp_order LIMIT $start, $l_odp_nastronie")
					or die("Nie mozna sie polaczyc z baza danych1");
//zapytanie ktore pobiera ilosc wszystkich rekordow jakie spelnia warunki w tym zapytaniu dla porcjowania wynikow					
					$query33=mysql_query("SELECT products_id, products_model, manufacturers_id FROM ".TABLE_PRODUCTS."")
					or die("Nie mozna sie polaczyc z baza danych1");
					
					$l_odp = mysql_num_rows($query33);
					
					$query3=mysql_query("SELECT MAX(products_id) FROM ".TABLE_PRODUCTS." LIMIT $start, $l_odp_nastronie");
					$row4=mysql_fetch_array($query3, MYSQL_NUM);
					
					
					echo "<input type='hidden' name='ilosc' value='$row4[0]'>";
					
					
//wyswietlanie tych rekordow 
					$i=1;
					while($row2=mysql_fetch_array($query, MYSQL_NUM))
					{
					$query2=mysql_query("SELECT manufacturers_id, manufacturers_name FROM ".TABLE_MANUFACTURERS." WHERE manufacturers_id='$row2[2]'")
					or die("Nie mozna sie polaczyc z baza danych2");
					$row3=mysql_fetch_array($query2, MYSQL_NUM);
					
					$query5=mysql_query("SELECT products_name  FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id='$row2[0]'")
					or die("Nie mozna sie polaczcy z baza danych");					
					$row5=mysql_fetch_array($query5, MYSQL_NUM);
					
					if($i%2==1)
			{
			echo "<tr class='dataTableRow'>".
				"<td align='center'>$row2[0]</td><td align='center'>$row2[1]</td><td align='center'>$row5[0]</td><td  align='center'>$row3[1]</td><td align='center'>";
				 sub($row2[0]);
			echo	"</td>".
				"</tr><input type='hidden' name='$i' value='$row2[0]'>"	;
			}
										
					if($i%2==0)
			{
			echo "<tr class='dataTableRowSelected'>".
				"<td align='center'>$row2[0]</td><td align='center'>$row2[1]</td><td align='center'>$row5[0]</td><td  align='center'>$row3[1]</td><td align='center'>";
				
			sub($row2[0]);
			echo	"</td>".
				"</tr><input type='hidden' name='$i' value='$row2[0]'>"	;
				
				
				
				
			echo	"</td>".
				"</tr><input type='hidden' name='$i' value='$row2[0]'>"	;
			}
					$i++;
					
					}
			echo	"<input type='hidden' name='krotnosc' value='$i'>";

//ustawienie adresu
			$skrypt="set_subcontrac.php?";
//uruchomienie funkcji porcjujacej dane
			 pasek($l_odp,$l_odp_nastronie,$l_odp_napasku,$skrypt,$a);
					?>
					</form>
					 <tr><td colspan='5'align='center'><br></td></tr>
 <tr><td colspan='6'align='center'><input type="image" src="includes/languages/english/images/buttons/button_save.gif" name='insert' ONCLICK="javascript:document.set.submit();"></td></tr>
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