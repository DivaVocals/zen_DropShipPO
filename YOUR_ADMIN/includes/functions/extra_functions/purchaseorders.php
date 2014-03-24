<?
function pasek($l_odp,$l_odp_nastronie,$l_odp_napasku,$skrypt,$a) {
  $l_odp_podz = intval($l_odp / $l_odp_nastronie);
  $l_odp_podz_mod = $l_odp % $l_odp_nastronie;
  if ($l_odp_podz_mod>0) $l_odp_podz++;
  if ($a<0) $a=0;
  if ($a>=$l_odp_podz) $a=$l_odp_podz-1;
  $start = $a-1;
  if ($a>0) {$pop="<a href=\"".$skrypt."a=$start\" class='porc'>&lt;&lt;&lt;
    ".TABLE_SET_SUBC_LINK_PREVIOUS."</a> - &nbsp;";}
  else {$pop = "<font class='s2'>&lt;&lt;&lt; ".TABLE_SET_SUBC_LINK_PREVIOUS." </font> - &nbsp;";}
  if ($a<$l_odp_napasku) {$koniec = $l_odp_napasku*2+1;}
    else {$koniec = $a+$l_odp_napasku+1;}
  if ($a<=$koniec-$l_odp_napasku) {$star=$a-$l_odp_napasku;}
  if ($a>=$l_odp_podz-$l_odp_napasku) {$star=$l_odp_podz-$l_odp_napasku*2-1;}
  if ($koniec>$l_odp_podz) $koniec = $l_odp_podz;
  if ($star<0) $star=0;
  for ($i=$star; $i<$koniec; $i++) {
    if ($i <> $a) { $pasek .= "<a href=\"".$skrypt."a=$i\" class='porc'>";}
      else {  $pasek .= "<font color=red><b>"; }
    if ($l_odp_podz<>1) {$pomocniczy = $i+1;}
    if ($i<>$a) { $pasek .= "$pomocniczy</a> &nbsp;"; }
      else {$pasek .= "$pomocniczy</b></font> &nbsp;";}
  }
  $dalej = $a+1;
  if ($a<$l_odp_podz-1)
   {$nas="- <a href=\"".$skrypt."a=$dalej\" class='porc'>".TABLE_SET_SUBC_LINK_NEXT." &gt;&gt;&gt; </a>";}
    else { $nas = "- ".TABLE_SET_SUBC_LINK_NEXT." &gt;&gt;&gt; ";}
  if ($pomocniczy>0) {$br= "<br> $pop $pasek $nas"; }
  echo "<font class=s2><center> ".TABLE_SET_SUBC_TOTAL_FOUND." <b>$l_odp</b> ".TABLE_SET_SUBC_TOTAL_OUT_OF." <b>$l_odp_podz</b>
    ".TABLE_SET_SUBC_TOTAL_PAGES." $br</center>";
}
?>