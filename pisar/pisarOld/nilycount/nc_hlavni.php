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
	."<td>\n");
echo(nc_tab_spoj2(
	nc_tab_vypis("systemy",$os,"sys","systemu"),
	nc_tab_vypis("prohlizece",$browser,"prohl","prohlizecu")
	));
echo("</td>\n"
	."</tr>\n"
	."<tr>\n"
	."<td>\n");
echo(nc_tab_spoj2(
	nc_tab_jednoduchyvypis("stranky","stranek"),
	nc_tab_jednoduchyvypis("hoste","hostu")
	));
echo("</td>\n"
	."</tr>\n");
echo(nc_html_podpis());
echo("</table>\n");
echo(nc_html_end());
mysql_close();

?>
