<?
foreach(array("conf/conf","lib/fce","lib/os","lib/browser") as $i){
	if (!is_readable("$i.php")) return;
	else require_once("$i.php");
}
nc_sql_connect();
echo(nc_html_begin());
echo(nc_html_menu());
echo("<table class=\"hlavni\">");
$sys=nc_sql_select("systemy",100);
for($i=0,$a=(ceil(($sys["max"])/(3.0)));$i<$a;$i++){
		if($i!=0) echo("</td>\n</tr>\n");
		echo("<tr>\n<td>\n");
		echo(nc_tab_spoj3(
			nc_tab_vypis_prohl($sys[$i*3+0][0]),
			nc_tab_vypis_prohl($sys[$i*3+1][0]),
			nc_tab_vypis_prohl($sys[$i*3+2][0])));
}
echo("</td>\n"
	."</tr>\n");
echo(nc_html_podpis());
echo("</table>\n");
echo(nc_html_end());
mysql_close();

?>
