<?
if (!defined("_NC_MARK")) define("_NC_MARK", "1");
else return;

if (!defined("_NC_DIR")) return;

foreach(array("conf/conf","lib/fce","lib/os","lib/browser") as $i){
	if (!is_readable(_NC_DIR."$i.php")) return;
	else require_once(_NC_DIR."$i.php");
}

ignore_user_abort(1);
nc_sql_connect();
nc_sql_pridej1("stranky","stranka",_NC_NAME);
$vysl=nc_os_browser();
$pokracovat=nc_sql_pridej_host(gethostbyaddr($REMOTE_ADDR),$vysl);
if ($pokracovat && ($vysl[browser]!="other")) 
	nc_sql_pridej1("prohlizece","prohlizec",$vysl[browser]);
if ($pokracovat && ($vysl[os]!="other"))
	nc_sql_pridej1("systemy","os",$vysl[os]);
if ($pokracovat && ($vysl[browser]!="other") && ($vysl[os]!="other")) 
	nc_sql_pridej2("proh_os","prohlizec","os",$vysl[browser],$vysl[os]);
mysql_close();
ignore_user_abort(0);
?>

