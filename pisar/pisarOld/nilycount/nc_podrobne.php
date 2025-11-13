<?
foreach(array("conf/conf","lib/fce","lib/os","lib/browser") as $i){
	if (!is_readable("$i.php")) return;
	else require_once("$i.php");
}
nc_sql_connect();
echo(nc_html_begin());
echo(nc_html_menu());
echo("<table class=\"hlavni\">\n"
	."<tr>\n"
	."<td>\n"
	."<table class=\"podrobna\">\n"
	."<tr class=\"nadpis\">\n"
	."<td>Posledni&nbsp;navsteva</td>\n"
	."<td>Host</td>\n"
	."<td>#&nbsp;stranek</td>\n"
	."<td>Operacni&nbsp;system</td>\n"
	."<td>Prohlizec</td>\n"
	."</tr>\n");	
$tab=nc_sql_select_podrobne();
for($i=0;$i<$tab["max"];$i++){
	echo(nc_tab_radek_podrobne($tab[$i],($i % 2)));
}
echo("</table>\n"
	."</td>\n"
	."</tr>\n");
echo(nc_html_podpis());
echo("</table>\n");
echo(nc_html_end());
mysql_close();

?>
