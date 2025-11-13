<?PHP

$server = "mysql.webzdarma.cz";
$jmeno = "pisar";
$heslo = "momenta";
$db = "pisar";

mysql_connect($server, $jmeno, $heslo) or 
  die("Nepripojili jsme se k serveru");
mysql_select_db($db);

mysql_query("CREATE TABLE `stranky` (
`stranka` VARCHAR(20) NOT NULL ,
`pocet` INT UNSIGNED DEFAULT '0' NOT NULL ,
PRIMARY KEY ( `stranka` ) 
) COMMENT = 'sledovane stranky'");

mysql_query("CREATE TABLE `systemy` (
`os` VARCHAR(20) NOT NULL ,
`pocet` INT UNSIGNED DEFAULT '0' NOT NULL ,
PRIMARY KEY ( `os` ) 
) COMMENT = 'operacni systemy'");

mysql_query("CREATE TABLE `prohlizece` (
`prohlizec` VARCHAR(20) NOT NULL ,
`pocet` INT UNSIGNED DEFAULT '0' NOT NULL ,
PRIMARY KEY ( `prohlizec` ) 
) COMMENT = 'pouzite webove prohlizece'");

mysql_query("CREATE TABLE `proh_os` (
`prohlizec` VARCHAR(20) NOT NULL ,
`os` VARCHAR(20) NOT NULL ,
`pocet` INT UNSIGNED DEFAULT '0' NOT NULL ,
PRIMARY KEY ( `prohlizec` , `os` ) ,
INDEX ( `prohlizec` ) ,
INDEX ( `os` )
) COMMENT = 'statistika prohlizecu na jednotlivych os'");

mysql_query("CREATE TABLE `hoste` (
`host` varchar(30) NOT NULL,
`pocet` int(10) unsigned NOT NULL default '0',
`os` varchar(20) NOT NULL,
`prohlizec` varchar(20) NOT NULL,
`cas` datetime NOT NULL default '0000-00-00 00:00:00',
PRIMARY KEY  (`host`)
)COMMENT='podrobnosti o hostech stranek'");

echo "Provedl se Sqript s pokusem vytvoreni MySql databaze he hi";

?>

