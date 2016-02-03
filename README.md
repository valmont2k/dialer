# dialer

это копия(форк) 
http://netcologne.dl.sourceforge.net/project/asteriskautodialer/dialersrc.tar.gz
только под астериск, установленный из пакетов на дебиан 

Установка

MySQL

 mysqladmin -u root -p create dialerdb;
 
 mysql -u root -p -e "GRANT ALL PRIVILEGES ON dialerdb.* TO user1@localhost IDENTIFIED BY 'pass1';"
 
 mysql -u root -p -e "flush privileges;"
 
 mysql -u root -p
 
 mysql> use dialerdb;
 
DROP TABLE IF EXISTS `Campaign`;

   CREATE TABLE `Campaign` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CampaignName` varchar(80) CHARACTER SET utf8 NOT NULL,
  `LastIdDial` varchar(80) CHARACTER SET utf8 NOT NULL,
  `MaxCalls` varchar(80) CHARACTER SET utf8 NOT NULL,
  `destination` varchar(80) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


CREATE TABLE `login_admin` (
  `user_name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `user_pass` varchar(50) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

Создадим пользователя

 INSERT INTO login_admin(user_name,user_pass) VALUES('admin',SHA1('admin'));

HTML

Скачать

git clone https://github.com/valmont2k/dialer.git

Создадим веб директорию:

 mkdir -p  /var/www/html/dialer
Скопируем файлы:

 cp -r HTML/* /var/www/html/dialer
 chown -R asterisk. /var/www/html/dialer

Добавим контекст в диалплан Asterisk:

 cat Asterisk/extensions_custom.conf >> /etc/asterisk/extensions_custom.conf
 
Модифицируйте контекст для ваших условий: 

SIP/sip_trunk_name - замените на транк в вашей системе, например DAHDI/g1/${NUM}, если вы используете DAHDI.

[dialercheck]

exten => s,1,NoOp("**Dialerchek** Вызывает номер: ${NUM})

same => n,Set(CDR(accountcode)=DIALER)

same => n,Set(CDR(userfield)=${NUM})

same => n,Dial(SIP/sip_trunk_name/${NUM},60)

same => n, NoOp(SIP return code : ${HASH(SIP_CAUSE,${CDR(dstchannel)})})

same => n,NoOp(${DIALSTATUS})

same => n,Set(SIPC=${HASH(SIP_CAUSE,${CDR(dstchannel)})})

Скопируем AGI скрипты:

cp -r AGI/* /usr/share/asterisk/agi-bin/

 chown -R asterisk. /usr/share/asterisk/agi-bin/DialerCamps
 
 chmod -R a+x /usr/share/asterisk/agi-bin/DialerCamps
 
 chown  asterisk. /usr/share/asterisk/agi-bin/di*
 
 chmod  a+x /usr/share/asterisk/agi-bin/di*

Config

 mcedit /var/www/html/dialer/config.php
 
	$host="localhost";
	
	$user="user1";
	
	$pass="pass1";
	
	$db="dialerdb";

http://ip_address/html/dialer

Campaign Name - уникальное имя кампании.

Import CSV File - импорт файла со списком вызываемых абонентов.

формат CSV

Name,Last Name,Number

Test,Testov,8121234567
Testy,Testova,8127654321

Maximum calls - Кол-во одновременных вызовов

Maximum Retries - Кол-во попыток дозвона на уникальный номер.

Retry Time - Интервал между попытками.

Endpoint - Назначение для приема вызова (Например, у вас есть очередь номер 701 назначенная в интерфейсе FreePBX, тогда вы пишите: 701@from-internal)

У нас ivr-1@from-pstn
