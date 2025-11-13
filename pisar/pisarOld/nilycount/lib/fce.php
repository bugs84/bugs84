
<?
//====================================================SQL====================================================

//pripoji se k MySQL serveru
function nc_sql_connect(){
	global $_NC_SQL;
	if(!mysql_connect($_NC_SQL['SERVER'],$_NC_SQL['JMENO'],$_NC_SQL['HESLO'])) exit("Nemuzu se pripojit k databazi!");
	mysql_select_db($_NC_SQL['DB']);
}

//pridava hodnoty do tabulek, ktere maji tvar $tabulka($klic,pocet)
function nc_sql_pridej1($tabulka,$klic,$radek){
	if(!($vysl = mysql_query("SELECT * FROM $tabulka WHERE $klic=\"$radek\""))) 
		exit('Chybny dotaz: '.mysql_error());
	if(!mysql_num_rows($vysl)) {
		if (!(mysql_query("INSERT INTO $tabulka ($klic,pocet) VALUES (\"".$radek."\",1)"))) 
			exit('Chybny dotaz: '.mysql_error());
	}
	else {
		$row = mysql_fetch_row($vysl);
		if (!(mysql_query("UPDATE $tabulka SET pocet=".($row[1]+1)." WHERE $klic=\"$row[0]\"")))
			exit('Chybny dotaz: '.mysql_error());
	}
}

//pridava hodnoty do tabulek, ktere maji tvar $tabulka($klic1,$klic2,pocet)
function nc_sql_pridej2($tabulka,$klic1,$klic2,$hodn1,$hodn2){
	if(!($vysl = mysql_query("SELECT * FROM $tabulka WHERE $klic1=\"$hodn1\" && $klic2=\"$hodn2\""))) 
		exit('Chybny dotaz: '.mysql_error());
	if(!mysql_num_rows($vysl)) {
		if (!(mysql_query("INSERT INTO $tabulka ($klic1,$klic2,pocet) VALUES (\"".$hodn1."\",\"".$hodn2."\",1)"))) 
			exit('Chybny dotaz: '.mysql_error());
	}
	else {
		$row = mysql_fetch_row($vysl);
		if (!(mysql_query("UPDATE $tabulka SET pocet=".($row[2]+1)." WHERE $klic1=\"$row[0]\" && $klic2=\"$row[1]\"")))
			exit('Chybny dotaz: '.mysql_error());
	}
}

//do databaze prida informace o hostovi
function nc_sql_pridej_host($dns,$proh_os){
	global $_NC_MAXTIME;
	if(!($vysl = mysql_query("SELECT * FROM hoste WHERE host=\"$dns\""))) 
		exit('Chybny dotaz: '.mysql_error());
	if(!mysql_num_rows($vysl)) {
		if (!(mysql_query("INSERT INTO hoste (host,pocet,os,prohlizec,cas) "
			."VALUES (\"$dns\",1,\"".$proh_os["os"]."\",\"".$proh_os["browser"]."\",NOW())"))) 
			exit('Chybny dotaz: '.mysql_error());
		return true;
	}
	else {
		$row = mysql_fetch_row($vysl);
		$now=getdate();
		$ret=(($now[0]-strtotime($row[4]))>$_NC_MAXTIME);
		//echo("<p>Byl jsi tu uz pred ".($now[0]-strtotime($row[4]))." sekundami. Dolni limit pro zapocteni je ".$_NC_MAXTIME."."); 
		if (!(mysql_query("UPDATE hoste "
			."SET pocet=".($row[1]+1).", os=\"".$proh_os["os"]."\", prohlizec=\"".$proh_os["browser"]."\", cas=NOW() "
			."WHERE host=\"".$row[0]."\"")))
			exit('Chybny dotaz: '.mysql_error());
		return $ret;
	}
}


//telo funkci vybirajicich z databaze
function nc_sql_sel($dotaz,$limit) {
	if(!($vysl = mysql_query($dotaz))) 
		exit('Chybny dotaz: '.mysql_error());
	for($i=0;$i<$limit;$i++){
		if($a=mysql_fetch_row($vysl))
			$ret[$i]=$a;
		else{
			$ret["max"]=$i;
			break;
		}
	}
	if($i==0) $ret["max"]=0;
	else if(empty($ret["max"])) $ret["max"]=$limit;
	return $ret;
}

//vyzadani informaci z databaze z tabulek typu (_,pocet)
function nc_sql_select($tabulka) {
	global $_NC_VYPIS;
	return nc_sql_sel("SELECT * FROM $tabulka ORDER BY pocet DESC LIMIT ".$_NC_VYPIS[$tabulka],$_NC_VYPIS[$tabulka]);
}

//vyzadani informaci z databaze z tabulky proh_os, kde os = $system
function nc_sql_select_prohl($system){
	global $_NC_VYPIS;
	return nc_sql_sel("SELECT * FROM proh_os WHERE os=\"$system\" ORDER BY pocet DESC LIMIT ".$_NC_VYPIS["proh_os"],$_NC_VYPIS["proh_os"]);
}

function nc_sql_select_podrobne(){
	global $_NC_VYPIS;
	return nc_sql_sel("SELECT * FROM hoste ORDER BY cas DESC LIMIT ".$_NC_VYPIS["podrobne"],$_NC_VYPIS["podrobne"]);
}

//telo funkci zjistujicich soucet poctu v databazi
function nc_sql_sou($dotaz){
	if(!($vysl = mysql_query($dotaz))) 
		exit('Chybny dotaz: '.mysql_error());
	$a=mysql_fetch_row($vysl);
	return $a[0];
}

//zjisti celkovy pocet v tabulce databaze
function nc_sql_soucet($tab){
	return nc_sql_sou("SELECT SUM(pocet) FROM $tab");
}

//zjisti celkovy pocet v tabulce databaze, kde os = $system
function nc_sql_soucet_prohl($system){
	return nc_sql_sou("SELECT SUM(pocet) FROM proh_os WHERE os=\"$system\"");
}

//====================================================HTML====================================================

//hlavicka html dokumenu
function nc_html_begin() {
    return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" "
	."\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
	."<html>"
	."<head>\n"
	."<title>NilyCount</title>\n"
	."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-2\" />\n"
	."<link rel=\"stylesheet\" type=\"text/css\" href=\"lib/styl.css\"/>"
	."</head>\n"
	."<body>\n";
}

//paticka html dokumentu
function nc_html_end() {
	return "
</body>\n"
	."</html>\n";
}

//zobrazi navigaci na vrchu stranky
function nc_html_menu() {
	$str ="<table class=\"menu\">\n"
	// width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\"
		."<tr>\n"
		."<td class=\"navigace\">\n"
;//		."<span class=\"menu\">\n";
	foreach (array(".." => "Hlavni&nbsp;stranka", 
			"nc_hlavni.php" => "Souhrnna&nbsp;statistika", 
			"nc_os_prohl.php" => "Statistika&nbsp;OS&nbsp;a&nbsp;prohlizecu",
			"nc_podrobne.php" => "Podrobna&nbsp;statistika&nbsp;hostu")
			as $url => $popisek) {
		$str .= "<a href=\"$url\">$popisek</a>&nbsp;\n";
	}
	$str .= 
//	"</span>\n"
//		.
		"</td>\n"
		."</tr>\n"
		."<tr>\n"
		."<td class=\"nadpis\">\n"
		."Statistika pro ".$_SERVER["SERVER_NAME"]." z ".date("d.m.Y")
		."</td>\n"
		."</tr>\n"
		."</table>\n";
	return $str;
}

//halvicka tabulky s kategorii
function nc_tab_hlavicka($img,$pocet, $kategorie) {
	return "<table class=\"kategorie\">\n"
	."<tr class=\"prvni\">\n"
	."<td>$img</td>"
	."<td>Top $pocet ".$kategorie."</td>\n"
	."</tr>\n";
}

//jeden radek tabulky s kategorii
function nc_tab_radek($obr, $nazev, $pocet, $celkem) {
	return "<tr class=\"dalsi\">\n"
	.(!empty($obr) ? "<td><img src=\"".$obr."\" height=\"14\" width=\"14\" alt=\"$nazev\"/>&nbsp;&nbsp;</td>\n" : "<td></td>")
	."<td align=\"left\">$nazev&nbsp;</td>\n"
	."<td align=\"right\">&nbsp;$pocet </td>\n"
	."<td align=\"right\">&nbsp;".sprintf("%.2f%%", ($pocet /$celkem * 100))."</td>\n"
	."</tr>\n";
}

//jeden radek tabulky s podrobnym vypisem
function nc_tab_radek_podrobne($radek,$sude) {
	global $os,$browser;
	return "<tr class=\"".($sude ? "sudy" : "lichy")."\">\n"
	."<td align=\"right\">".$radek[4]."&nbsp;&nbsp;&nbsp;</td>\n"
	."<td align=\"left\">".$radek[0]."&nbsp;</td>\n"
	."<td align=\"right\">".$radek[1]."&nbsp;</td>\n"
	."<td align=\"left\"><img src=\"img/sys_".$os[$radek[2]]["icon"].".png\" height=\"14\" width=\"14\" "
	."alt=\"".$os[$radek[2]]["title"]."\"/>&nbsp;&nbsp;".$os[$radek[2]]["title"]."&nbsp;</td>\n"	
	."<td align=\"left\"><img src=\"img/prohl_".$browser[$radek[3]]["icon"].".png\" height=\"14\" width=\"14\" "
	."alt=\"".$browser[$radek[3]]["title"]."\"/>&nbsp;&nbsp;".$browser[$radek[3]]["title"]."&nbsp;</td>\n"	
	."</tr>\n";
}


//paticka tabulky s kategorii
function nc_tab_paticka($celkem) {
	return "<tr class=\"posledni\">\n"
	."<td></td>"
	."<td align=\"left\">Celkem&nbsp;</td>\n"
	."<td align=\"right\">&nbsp;$celkem</td>\n"
	."</tr>\n"
	."</table>\n";
}

//vytisteni 2 tabulek vedle sebe
function nc_tab_spoj2($tab1,$tab2) {
	return "<table class=\"spojene\">\n"
	."<tr>\n"
	."<td>\n"
	.$tab1
	."</td>\n"
	."<td>\n"
	.$tab2
	."</td>\n"
	."</tr>"
	."</table>";
}

//vytisteni 3 tabulek vedle sebe
function nc_tab_spoj3($tab1,$tab2,$tab3) {
	return "<table class=\"spojene\">\n"
	."<tr>\n"
	."<td>\n"
	.$tab1
	."</td>\n"
	."<td>\n"
	.$tab2
	."</td>\n"
	."<td>\n"
	.$tab3
	."</td>\n"
	."</tr>"
	."</table>";
}


//vytvori jednu tabulku s kategorii
function nc_tab_vypis($tab,$pole,$predpona,$nazev) {
	global $_NC_VYPIS;
	$tabulka=nc_sql_select($tab);
	$soucet=nc_sql_soucet($tab);
	$str=nc_tab_hlavicka("",$_NC_VYPIS[$tab],$nazev);
	for ($i=0;$i<$tabulka["max"];$i++)
		$str.=nc_tab_radek("img/".$predpona."_".$pole[$tabulka[$i][0]]["icon"].".png",$pole[$tabulka[$i][0]]["title"],$tabulka[$i][1],$soucet);
	$str.=nc_tab_paticka($soucet);
	return $str;
}

//vytvori jednu tabulku s jednoduchou kategorii (=vypisuji se primo hodnoty z databaze)
function nc_tab_jednoduchyvypis($tab,$nazev) {
	global $_NC_VYPIS;
	$tabulka=nc_sql_select($tab);
	$soucet=nc_sql_soucet($tab);
	$str=nc_tab_hlavicka("",$_NC_VYPIS[$tab],$nazev);
	for ($i=0;$i<$tabulka["max"];$i++)
		$str.=nc_tab_radek("",$tabulka[$i][0],$tabulka[$i][1],$soucet);
	$str.=nc_tab_paticka($soucet);
	return $str;
}

//vytvori jednu tabulku s kategorii
function nc_tab_vypis_prohl($system) {
	if(empty($system)) return "";
	global $_NC_VYPIS,$os,$browser;
	$tabulka=nc_sql_select_prohl($system);
	$soucet=nc_sql_soucet_prohl($system);
	if(empty($soucet))
		$soucet=0;
	$str=nc_tab_hlavicka(
		"<img src=\"img/sys_".$os[$system]["icon"].".png\" height=\"14\" width=\"14\" alt=\"".$os[$system]["title"]."\"/>\n",
		$_NC_VYPIS["proh_os"],
		$os[$system]["title"]);
	for ($i=0;$i<$tabulka["max"];$i++)
		$str.=nc_tab_radek("img/prohl_".$browser[$tabulka[$i][0]]["icon"].".png",$browser[$tabulka[$i][0]]["title"],$tabulka[$i][2],$soucet);
	$str.=nc_tab_paticka($soucet);
	return $str;
}

//podpis pod strankou
function nc_html_podpis() {
	return "<tr class=\"podpis\">\n"
		."<td>\n"
		."NilyCount 0.1 &copy; <a href=\"http://nily.wz.cz\">Daniel Kratochvil</a> \n"
		."- projekt je siren pod licenci <a href=\"http://www.gnu.org/copyleft/gpl.html\">GPL</a> \n"
		."<a href=\"http://validator.w3.org/check/referer\">\n"
		."<img src=\"img/valid-xhtml10.png\" height=\"15\" width=\"80\" alt=\"Valid XHTML 1.0\"/>\n"
		."</a> \n"
		."<a href=\"http://jigsaw.w3.org/css-validator/check/referer\">\n"
		."<img src=\"img/valid-css.png\" height=\"15\" width=\"80\" alt=\"Valid CSS\"/>\n"
		."</a>\n"
		."</td>\n"
		."</tr>\n";
}


//====================================================pomocne====================================================

//z $HTTP_USER_AGENT se pokusi vytahnout informace o OS a prohlizeci
function nc_os_browser() {
	global $os, $browser, $HTTP_USER_AGENT;
	foreach (array("browser","os") as $i) {
		reset($$i);
		foreach($$i as $k =>$j) {
			//echo($j['rule']."<br>");
			//echo($HTTP_USER_AGENT);
			if (eregi($j['rule'],$HTTP_USER_AGENT)) {
				$vysl[$i] = $k;
				break;
			}
		}
	}
	return $vysl;
}


?>
