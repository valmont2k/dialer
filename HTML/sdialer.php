<?php

/************* Поступившие переменные для использования ***************/
if(isset($_POST['campname'])){

$calls=$_POST['calls'];
$campname=$_POST['campname'];
$retry=$_POST['retry'] - 1 ;
$retrytime=$_POST['retrytime'];
$type=$_POST['type'];
$destination=$_POST['destination'];
//echo $type;

/******************* Соединение с БД **********************/
require_once("config.php");
$link = mysql_connect($host,$user,$pass) or die(mysql_error());
mysql_select_db($db, $link);


/******************** Выборка LastIdDial ***************/
	$sql="SELECT LastIdDial from Campaign WHERE CampaignName='" .$campname. "'";
	$res=mysql_query($sql,$link) or die("sql1".mysql_error());
	$row = mysql_fetch_assoc($res);
        $lastID = $row['LastIdDial'];
	
/****************** Выборка значений из БД для генерации каждой записи файла **********************/
	$sql2="SELECT ID,Name,LastName,NameCamp,Tel from " .$campname. " WHERE NameCamp='" .$campname. "'  ORDER BY ID ASC";
	$res2=mysql_query($sql2,$link) or die ("sql2".mysql_error());
	$fields_num = mysql_num_rows($res2);

/***********************Для каждого вызываемого номера генерируется Call File***********************/

	for($i=0; $i<$fields_num; $i++){
		while ($fila = mysql_fetch_assoc($res2)) {
 			$callfile=fopen("/var/lib/asterisk/agi-bin/DialerCamps/" .$campname. "/" .$fila['ID']. "_" .$campname. "_" .$fila['Name']. "" .$fila['LastName']. "_" .$fila['Tel']. ".call","w")or die("error");
			fputs($callfile,"Channel: LOCAL/s@dialercheck");
			fputs($callfile,"\n");
			fputs($callfile,"CallerID:" .$fila['Tel']. "");
			fputs($callfile,"\n");
			fputs($callfile,"MaxRetries: " .$retry. "");
			fputs($callfile,"\n");
			fputs($callfile,"RetryTime: " .$retrytime. "");
			fputs($callfile,"\n");
			fputs($callfile,"WaitTime: 30");
			fputs($callfile,"\n");
			fputs($callfile,"SET: NUM=" .$fila['Tel']. "");
			fputs($callfile,"\n");
			fputs($callfile,"SET: CAMPN=" .$fila['NameCamp']. "");
			fputs($callfile,"\n");
			fputs($callfile,"SET: ID=" .$fila['ID']. "");
			fputs($callfile,"\n");
			fputs($callfile,"SET: TRY= 1");
			fputs($callfile,"\n");
			fputs($callfile,"Application: AGI");
			fputs($callfile,"\n");
			fputs($callfile,"DATA: dialert.agi," .$fila['ID']. "," .$fila['NameCamp']. "," .$fila['Tel']. ",1");
			fputs($callfile,"\n");
			fputs($callfile,"Archive: Yes");
			fputs($callfile,"\n");
			fclose($callfile);
		}
	}



/***************** Кол-во одновременных вызовов ********************/ 

 $sqlz="UPDATE Campaign SET MaxCalls='" .$calls. "'  WHERE CampaignName='" .$campname. "'";
 mysql_query($sqlz,$link) or die("sql3".mysql_error());
 
/*****************точка приема вызова ENDPOINTS***********************************************************/ 
 $sqlz="UPDATE Campaign SET destination='" .$destination. "'  WHERE CampaignName='" .$campname. "'";
 mysql_query($sqlz,$link) or die("sql3".mysql_error());

/******************* Генерация базового файла уникальной Кампании *************/
 exec("cp /var/lib/asterisk/agi-bin/DialerCamps/maincron.php /var/lib/asterisk/agi-bin/DialerCamps/" .$campname. "/cron_" .$campname. ".php");
 exec ("chmod +x /var/lib/asterisk/agi-bin/DialerCamps/" .$campname. "/cron_" .$campname. ".php");
 exec ("chown asterisk.asterisk /var/lib/asterisk/agi-bin/DialerCamps/" .$campname. "/cron_" .$campname. ".php");

/***************** Генерация SH срипта для выполнения Кампании ***********************/
 $trigger=fopen("/var/lib/asterisk/agi-bin/DialerCamps/" .$campname. "/execd_" .$campname. ".sh","w")or die("error");
 fputs($trigger,"#!/bin/bash");
 fputs($trigger,"\n");
 fputs($trigger,"cd /var/lib/asterisk/agi-bin/DialerCamps/" .$campname. "");
 fputs($trigger,"\n");
 fputs($trigger,"/usr/bin/php /var/lib/asterisk/agi-bin/DialerCamps/" .$campname. "/cron_" .$campname. ".php");
 fclose($trigger);

/**************** Права на запуск SH скрипта ***********************/
 exec ("chmod +x /var/lib/asterisk/agi-bin/DialerCamps/" .$campname. "/execd_" .$campname. ".sh");
 
/********************** Перемещение Call Files в директорию ./outgoing *****************/
// отключено мной!!!!!
// for($i=0;$i<=$calls;$i++){
// for($i=0;$i<=$fields_num;$i++){
//	 exec("mv /var/lib/asterisk/agi-bin/DialerCamps/" .$campname. "/" .$i. "_* /var/spool/asterisk/outgoing/");
// }

//первый звонок отправляем сразу!
	 exec("mv /var/lib/asterisk/agi-bin/DialerCamps/" .$campname. "/1_* /var/spool/asterisk/outgoing/");

/*********************** Обновление данных о последнем вызове *****************/
 $lastID = ($lastID + $calls);
 $sqlu="UPDATE Campaign SET LastIdDial='" .$lastID. "' WHERE CampaignName='" .$campname. "'";
 mysql_query($sqlu,$link) or die("sql3".mysql_error());

/*********************** Añadimos el archivo SH que ejecuta el cron de la campaña al Crontab de asterisk ****/
$output = shell_exec('crontab -l');
file_put_contents('/tmp/crontab.txt', $output."*/1 * * * * /var/lib/asterisk/agi-bin/DialerCamps/" .$campname. "/execd_" .$campname. ".sh".PHP_EOL);
echo exec('crontab /tmp/crontab.txt');
/************************* Se ejecutara cada minuto *********************************/

header("location:start.php?pin=$campname");

}

?>


