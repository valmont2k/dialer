<?php
session_start(); //Start the session
define(ADMIN,$_SESSION['name']); //Get the user name from the previously registered super global variable
if(!session_is_registered("admin")){ //If session not registered
header("location:index.php"); // Redirect to login.php page
}


$desc=$_GET["desc"];
require_once("config.php");
//$con = mysql_connect('localhost', 'dialeruser', 'dialerpass');
$con = mysql_connect($host,$user,$pass);
 if (!$con)
   {
   die('Could not connect: ' . mysql_error());
   }
mysql_select_db($db, $con);
// mysql_select_db("dialerdb", $con);

 $sql="DELETE  FROM login_admin WHERE user_name = '".$desc."'";
 $result = mysql_query($sql);

 header("Location: admins.php")

?>
