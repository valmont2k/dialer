<?php


$q=$_GET['q'];



// $con = mysql_connect('localhost', 'dialeruser', 'dialerpass');
require_once("config.php");
$con = mysql_connect($host,$user,$pass);
 if (!$con)
   {
   die('Could not connect: ' . mysql_error());
   }

 mysql_select_db($db, $con);

 $sql="DELETE FROM  Campaign WHERE CampaignName like '" .$q. "'";

 $result = mysql_query($sql)or die(mysql_error());



echo "Campaign " .$q. " removed!";




?>


result