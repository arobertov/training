<?php
/**
 * 
 * @copyright Copyright (C) 2012 SuperHosting.BG. All rights reserved.
 * 
 */
header('Content-type: text/html; charset=UTF-8');
define('VERSION', '1.0.3');

$version	= '';
$fp			= fopen("http://help.superhosting.bg/faq/attachments/version.txt", "r");
$path		= __FILE__;
$fileName	= explode(DIRECTORY_SEPARATOR, $path);
$user		= $fileName[2];

while ($line = fgets($fp)) {
	$version = $version . $line;
}

if(isset($_REQUEST['download']) && 1==$_REQUEST['download']){
	$file = 'http://help.superhosting.bg/faq/attachments/php.php';
	header("Content-type: force-download");
	header('Content-Description: File Transfer');
	header("Cache-control: private");
	header('Content-Disposition: attachment; filename=' . rawurlencode(basename($file)));
	readfile($file);
}

if(isset($_REQUEST['upgrade']) && 1==$_REQUEST['upgrade']){

    $url		= 'http://help.superhosting.bg/faq/attachments/php.txt';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $data = curl_exec($ch);

    curl_close($ch);

    file_put_contents($path, $data);
}

if(isset($_REQUEST['restartfcgi']) && 1==$_REQUEST['restartfcgi']){
	exec('/usr/bin/pkill -f php-fcgi');
}
?>
<html>
	<head>
		<title>Проверка на протокол за обработка на PHP при хостинг акаунт от СуперХостинг.БГ</title>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	</head>
	<body>
		<div align="center">
		<div style="width:700px; border:1px solid #5599BF;">
			<div style="padding-top:15px;">
				<a href="http://www.superhosting.bg" title="Хостинг и Домейни от СуперХостинг.БГ">
					<img src="http://www.superhosting.bg/img/sh_logo.gif" title="Ново поколение хостинг - SuperHosting.BG" alt="Ново поколение хостинг - SuperHosting.BG" border="0"/>
				</a>
			</div>
			<div style="padding:15px;margin:10px;" align="left">
			<span style="text-align:center;font-size:16px;font-family:Verdana;">
<?php
if (VERSION != $version) {
?>
			<div id="upgrade">
				<p>Налична е нова версия на скрипта.<br /><br />
				<a href="?download=1">Свалете последна версия</a> или <a href="?upgrade=1">обновете автоматично</a>.
			</div>
<?php
}
if (0==strcasecmp($_SERVER['FCGI_ROLE'],"RESPONDER") ) {
?>
			<br />
			<br />
			<span style="text-align:center;font-size:16px;font-family:Verdana;">
				Хостинг акаунтът ползва <b>FastCGI</b> протокол за обработка на PHP.
			</span>
			<br />
			<br />
			<br />
			По-долу можете да намерите допълнителни насоки за промени по директиви на PHP:
			<br />
			<br />
			<div id="on" style="text-align:justify; border: 1px solid #5599BF;padding: 5px;">
				1. За промяна на <b>memory_limit</b> натиснете <a href="javascript:switchit('First')">тук</a>.
			</div>
			<div id="First" style="text-align:justify; font-size:14px; display:none">
				<p>
					1. Увеличението на паметта може да бъде направено в php.ini файл. В home
					директорията на акаунта ще намерите системен php.ini файл, който е с име
					<b>php-fcgi.ini</b>. Можете да го редактирате посредством файловия мениджър
					или чрез текстов редактор. Необходимо е да промените директивата <b>memory_limit</b>
					по следния начин:
				</p>
				<p>
					<b>memory_limit = 64 M</b>
				</p>
				<p>
					В случая е зададена примерна стойност от 64 MB, но може да бъде зададена
					и по-висока стойност от 64 MB.
				</p>
				<p>
					Препоръчително е да увеличавате memory_limit с толкова, колкото е необходимо.
					Не е добра практика да се задава твърде голяма стойност.
				</p>
				<p>
					2. Указване на php-fcgi.ini да важи за хостинг акаунта
				<p>
				<p>
					За да може php-fcgi.ini файла да важи за хостинг акаунта, е необходимо да
					бъде указано във файл <b>php.fcgi</b>. Проверете дали в home директорията на
					акаунта има съществуващ файл с това име. В случай, че не е наличен, то можете да
					го създадете. <b>Важно: Правата на файла php.fcgi трябва да бъдат 755</b>.
					Проверете дали във файла има следните редове и ако ги няма, то е необходимо да ги добавите:
				</p>
				<p>
					<b>
						#!/bin/bash
						<br />
						DEFAULTPHPINI=/home/<?php echo $user; ?>/php-fcgi.ini
						<br />
						exec /usr/bin/php -c &#36;{DEFAULTPHPINI}
					</b>
				</p>
				<p>
					3. За да влязат в сила промените за целия хостинг акаунт, е необходимо да проверите дали в <b>.htaccess</b>
					файл в home директорията има добавени следните редове. Ако ги няма, то е необходимо
					да ги добавите:
				</p>
				<p>
					<b>
						&lt;IfModule mod_fcgid.c&gt;
						<br />
						AddHandler fcgid-script .php
						<br />
						FcgidWrapper /home/<?php echo $user; ?>/php.fcgi .php
						<br />
						&lt;/IfModule&gt;
					</b>
				</p>
				<p>
					След като сте извършили посочените действия, можете да се уверите че стойността
					за директивата memory_limit е променена, като използвате функцията <a href="http://bg2.php.net/manual/en/function.phpinfo.php">phpinfo()</a>.
				</p>
			</div>
			<br />
			<div id="on" style="text-align:left; border: 1px solid #5599BF;padding: 5px;">
				2. За промяна на <b>upload_max_filesize</b> и <b>post_max_size</b> натиснете <a href="javascript:switchit('Second')">тук</a>.
			</div>
			<div id="Second" style="text-align:justify; font-size:14px; display:none">
				<p>
					1. Увеличението на размера на файловете качвани през PHP може да бъде направено в php.ini файл.
					В home директорията на акаунта ще намерите системен php.ini файл, който е с име <b>php-fcgi.ini</b>.
					Можете да го редактирате посредством файловия мениджър или чрез текстов редактор. Необходимо е да
					промените директивите <b>upload_max_filesize</b> и <b>post_max_size</b> по следния начин:
				</p>
				<p>
					<b>upload_max_filesize = 50 M</b>
				</p>
				<p>
					<b>post_max_size = 50 M</b>
				</p>
				<p>
					В случая е зададена примерна стойност от 50 MB, но може да бъде зададена и по-висока стойност.
				</p>
				<p>
					2. Указване на php-fcgi.ini да важи за хостинг акаунта
				<p>
				<p>
					За да може php-fcgi.ini файла да важи за хостинг акаунта, е необходимо да бъде указано във файл
					<b>php.fcgi</b>. Проверете дали в home директорията на акаунта има съществуващ файл с това име.
					В случай, че не е наличен, то можете да го създадете. <b>Важно: Правата на файла php.fcgi трябва
					да бъдат 755</b>. Проверете дали във файла има следните редове и ако ги няма, то е
					необходимо да ги добавите:
				</p>
				<p>
					<b>
						#!/bin/bash
						<br />
						DEFAULTPHPINI=/home/<?php echo $user; ?>/php-fcgi.ini
						<br />
						exec /usr/bin/php -c &#36;{DEFAULTPHPINI}
					</b>
				</p>
				<p>
					3. За да влязат в сила промените за целия хостинг акаунт, е необходимо да проверите дали в <b>.htaccess</b> файл в home директорията има добавени следните редове. Ако ги няма, то е необходимо да ги добавите:
				</p>
				<p>
					<b>
						&lt;IfModule mod_fcgid.c&gt;
						<br />
						AddHandler fcgid-script .php
						<br />
						FcgidWrapper /home/<?php echo $user; ?>/php.fcgi .php
						<br />
						&lt;/IfModule&gt;
					</b>
				</p>
				<p>
					След като сте извършили посочените действия, можете да се уверите че стойността за директивите
					е променена, като използвате функцията <a href="http://bg2.php.net/manual/en/function.phpinfo.php">phpinfo()</a>.
				</p>
			</div>
			<br />
			<div id="on" style="text-align:left; border: 1px solid #5599BF;padding: 5px;">
				3. За активиране на <b>ZipArchive (zip.so)</b> натиснете <a href="javascript:switchit('Third')">тук</a>.
			</div>
			<div id="Third" style="text-align:justify; font-size:14px; display:none">
				<p>
					1. На всички Linux хостинг планове се поддържа модула ZipArchive. Този модул позволява чрез
					код да се четат или пишат ZIP компресирани архиви и файлове вътре в тях. За да ползвате модула
					zip.so, е необходимо той да бъде активиран. Това може да бъде направено в php.ini файл. В home
					директорията на акаунта ще намерите системен php.ini файл, който е с име <b>php-fcgi.ini</b>.
					Можете да го редактирате посредством файловия мениджър или чрез текстов редактор. Необходимо е
					да добавите следния ред:
				</p>
				<p>
					<b>extension=zip.so</b>
				</p>
				<p>
					2. Указване на php-fcgi.ini да важи за хостинг акаунта
				<p>
				<p>
					За да може php-fcgi.ini файла да важи за хостинг акаунта, е необходимо да бъде указано във
					файл <b>php.fcgi</b>. Проверете дали в home директорията на акаунта има съществуващ файл с това име.
					В случай, че не е наличен, то можете да го създадете. <b>Важно: Правата на файла php.fcgi трябва да бъдат 755</b>.
					Проверете дали във файла има следните редове и ако ги няма, то е необходимо да ги добавите:
				</p>
				<p>
					<b>
						#!/bin/bash
						<br />
						DEFAULTPHPINI=/home/<?php echo $user; ?>/php-fcgi.ini<br />
						exec /usr/bin/php -c &#36;{DEFAULTPHPINI}
					</b>
				</p>
				<p>
					3. За да влязат в сила промените за целия хостинг акаунт, е необходимо да проверите дали в <b>.htaccess</b> файл в home директорията има добавени следните редове. Ако ги няма, то е необходимо да ги добавите:
				</p>
				<p>
					<b>
						&lt;IfModule mod_fcgid.c&gt;
						<br />
						AddHandler fcgid-script .php
						<br />
						FcgidWrapper /home/<?php echo $user; ?>/php.fcgi .php
						<br />
						&lt;/IfModule&gt;
					</b>
				</p>
				<p>
					След като сте извършили посочените действия, можете да се уверите че zip.so е активиран, като използвате функцията <a href="http://bg2.php.net/manual/en/function.phpinfo.php">phpinfo()</a>.
				</p>
			</div>
			<br />
			<div id="on" style="text-align:left; border: 1px solid #5599BF;padding: 5px;">
				4. За активиране на <b>Zend Optimizer</b> натиснете <a href="javascript:switchit('Fourth')">тук</a>.
			</div>
			<div id="Fourth" style="text-align:justify; font-size:14px; display:none">
				<p>
					1. На всички Linux хостинг планове се поддържа Zend Optimizer. За да go ползвате, е необходимо той да
					бъде активиран. Това може да бъде направено в php.ini файл. В home директорията на акаунта ще намерите
					системен php.ini файл, който е с име <b>php-fcgi.ini</b>. Можете да го редактирате посредством файловия
					мениджър или чрез текстов редактор. Необходимо е да добавите следния ред:
				</p>
				<p>
					<b>
						zend_extension=/usr/local/Zend/lib/Optimizer/php-<?php echo floatval(phpversion()); ?>.x/ZendOptimizer.so
					</b>
				</p>
				<p>
					2. Указване на php-fcgi.ini да важи за хостинг акаунта
				<p>
				<p>
					За да може php-fcgi.ini файла да важи за хостинг акаунта, е необходимо да бъде указано във файл <b>php.fcgi</b>.
					Проверете дали в home директорията на акаунта има съществуващ файл с това име. В случай, че не е наличен,
					то можете да го създадете. <b>Важно: Правата на файла php.fcgi трябва да бъдат 755</b>. Проверете дали във
					файла има следните редове и ако ги няма, то е необходимо да ги добавите:
				</p>
				<p>
					<b>
						#!/bin/bash
						<br />
						DEFAULTPHPINI=/home/<?php echo $user; ?>/php-fcgi.ini
						<br />
						exec /usr/bin/php -c &#36;{DEFAULTPHPINI}
					</b>
				</p>
				<p>
					3. За да влязат в сила промените за целия хостинг акаунт, е необходимо да проверите дали в <b>.htaccess</b> файл в home директорията има добавени следните редове. Ако ги няма, то е необходимо да ги добавите:
				</p>
				<p>
					<b>
						&lt;IfModule mod_fcgid.c&gt;
						<br />
						AddHandler fcgid-script .php
						<br />
						FcgidWrapper /home/<?php echo $user; ?>/php.fcgi .php
						<br />
						&lt;/IfModule&gt;
					</b>
				</p>
				<p>
					След като сте извършили посочените действия, можете да се уверите че Zend е активиран, като използвате функцията <a href="http://bg2.php.net/manual/en/function.phpinfo.php">phpinfo()</a>.
				</p>
			</div>
			<br />
			<div id="on" style="text-align:left; border: 1px solid #5599BF;padding: 5px;">
				5. За активация на <b>IonCube Loader</b> натиснете <a href="javascript:switchit('Fifth')">тук</a>.
			</div>
			<div id="Fifth" style="text-align:justify; font-size:14px; display:none">
				<p>
					1. На всички Linux хостинг планове се поддържа IonCube Loader. За да go ползвате, е необходимо той да
					бъде активиран. Това може да бъде направено в php.ini файл. В home директорията на акаунта ще намерите
					системен php.ini файл, който е с име <b>php-fcgi.ini</b>. Можете да го редактирате посредством файловия
					мениджър или чрез текстов редактор. Необходимо е да добавите следния ред:
				</p>
				<p>
					<b>
						zend_extension = /usr/local/ioncube/ioncube_loader_lin_<?php echo floatval(phpversion()); ?>.so
					</b>
				</p>
				<p>
					<b>2. Указване на php-fcgi.ini да важи за хостинг акаунта</b>
					<br />
					За да може php-fcgi.ini файла да важи за хостинг акаунта, е необходимо да бъде указано във файл <b>php.fcgi</b>. 
					Проверете дали в home директорията на акаунта има съществуващ файл с това име. В случай, че не е наличен, то можете
					да го създадете. <b>Важно: Правата на файла php.fcgi трябва да бъдат 755</b>. Проверете дали във файла има следните
					редове и ако ги няма, то е необходимо да ги добавите:
				</p>
				<p>
					<b>
						#!/bin/bash
						<br />
						DEFAULTPHPINI=/home/<?php echo $user; ?>/php-fcgi.ini
						<br />
						exec /usr/bin/php -c &#36;{DEFAULTPHPINI}
					</b>
				</p>
				<p>
					<b>3. За да влязат в сила промените за целия хостинг акаунт</b>, е необходимо да проверите дали в <b>.htaccess</b> файл в home директорията има добавени следните редове. Ако ги няма, то е необходимо да ги добавите:
				</p>
				<p>
					<b>
						&lt;IfModule mod_fcgid.c&gt;
						<br />
						AddHandler fcgid-script .php
						<br />
						FcgidWrapper /home/<?php echo $user; ?>/php.fcgi .php
						<br />
						&lt;/IfModule&gt;
					</b>
				</p>
				<p>
					След като сте извършили посочените действия, можете да се уверите че IonCube е активиран, като използвате функцията <a href="http://bg2.php.net/manual/en/function.phpinfo.php">phpinfo()</a>.
				</p>
			</div>
			<br />
			<div id="on" style="text-align:left; border: 1px solid #5599BF;padding: 5px;">
				6. За промяна на други директиви на PHP посетете нашата помощна страница
				<a href="http://help.superhosting.bg/category/linux-hosting/php" target="_blank">http://help.superhosting.bg/category/linux-hosting/php</a>.
			</div>
			<br />
			<br />
			Имайте предвид, че след като бъдат направени промени по PHP е необходимо известно време те да влязат в сила. 
			Можете да натиснете <a href="?restartfcgi=1" ONCLICK="alert('Натиснете ОК, за да влязат в сила промените.');">тук</a>, за да влязат промените в сила веднага.
			</span>

<?php
}
else {
?>
			<br />
			<br />
			<span style="text-align:center;font-size:16px;font-family:Verdana;">
				Акаунтът ползва <b>CGI</b> протокол за обработка на PHP.
			</span>
			<br />
			<br />
			<br />
				По-долу можете да намерите допълнителни насоки за промени по директиви на PHP:
			<br />
			<br />
			<span style="text-align:left;font-size:16px;font-family:Verdana;">
				<div id="on" style="text-align:justify; border: 1px solid #5599BF;padding: 5px;">
					1. За промяна на <b>memory_limit</b> натиснете  <a href="javascript:switchit('First')">тук</a>.
				</div>
				<div id="First" style="text-align:justify; font-size:14px; display:none">
					<p>
						1. Увеличението на паметта може да бъде направено в php.ini файл. В home директорията на акаунта
						ще намерите системен php.ini файл, който е с име <b>php.ini</b>. Можете да го редактирате
						посредством файловия мениджър или чрез текстов редактор. Необходимо е да промените
						директивата <b>memory_limit</b> по следния начин:
					</p>
					<p>
						<b>memory_limit = 64 M</b>
					</p>
					<p>
						В случая е зададена примерна стойност от 64 MB, но може да бъде зададена и по-висока стойност от 64 MB.
					</p>
					<p>
						Препоръчително е да увеличавате memory_limit с толкова, колкото е необходимо. Не е добра практика да се задава твърде голяма стойност.
					</p>
					<p>
						2. Стандартно php.ini файла има действие само за директорията, в която се намира. За да важи за целия хостинг акаунт,
						е необходимо да проверите дали в <b>.htaccess</b> файл в home директорията има добавени следните редове.
						Ако ги няма, то е необходимо да ги добавите:
					</p>
					<p>
						<b>
							&lt;IfModule mod_env.c&gt;
							<br />
							SetEnv PHPRC /home/<?php echo $user; ?>/php.ini
							<br />
							&lt;/IfModule&gt;
						</b>
					</p>
					<p>
						След като сте извършили посочените действия, можете да се уверите че стойността за директивата memory_limit е променена, като използвате функцията <a href="http://bg2.php.net/manual/en/function.phpinfo.php">phpinfo()</a>.
					</p>
				</div>
				<br />
				<div id="on" style="text-align:left; border: 1px solid #5599BF;padding: 5px;">
					2. За промяна на <b>upload_max_filesize</b> и <b>post_max_size</b> натиснете <a href="javascript:switchit('Second')">тук</a>.
				</div>
				<div id="Second" style="text-align:justify; font-size:14px; display:none">
					<p>1. Увеличението на размера на файловете качвани през PHP може да бъде направено в php.ini файл. 
						В home директорията на акаунта ще намерите системен php.ini файл, който е с име <b>php.ini</b>.
						Можете да го редактирате посредством файловия мениджър или чрез текстов редактор. Необходимо е да промените
						директивите <b>upload_max_filesize</b> и <b>post_max_size</b> по следния начин:
					</p>
					<p>
						<b>upload_max_filesize = 50 M</b>
					</p>
					<p>
						<b>post_max_size = 50 M</b>
					</p>
					<p>
						В случая е зададена примерна стойност от 50 MB, но може да бъде зададена и по-висока стойност.
					</p>
					<p>
						2. Стандартно php.ini файла има действие само за директорията, в която се намира. За да важи за целия хостинг акаунт,
						е необходимо да проверите дали в <b>.htaccess</b> файл в home директорията има добавени следните редове.
						Ако ги няма, то е необходимо да ги добавите:
					</p>
					<p>
						<b>
							&lt;IfModule mod_env.c&gt;
							<br />
							SetEnv PHPRC /home/<?php echo $user; ?>/php.ini
							<br />
							&lt;/IfModule&gt;
						</b>
					</p>
					<p>
						След като сте извършили посочените действия, можете да се уверите че стойността за директивите е променена, като използвате функцията <a href="http://bg2.php.net/manual/en/function.phpinfo.php">phpinfo()</a>.
					</p>
				</div>
				<br />
				<div id="on" style="text-align:left; border: 1px solid #5599BF;padding: 5px;">
					3. За активиране на <b>ZipArchive (zip.so)</b> натиснете <a href="javascript:switchit('Third')">тук</a>.
				</div>
				<div id="Third" style="text-align:justify; font-size:14px; display:none">
					<p>
						1. На всички Linux хостинг планове се поддържа модула ZipArchive. Този модул позволява чрез код да се
						четат или пишат ZIP компресирани архиви и файлове вътре в тях. За да ползвате модула zip.so, е
						необходимо той да бъде активиран. Това може да бъде направено в php.ini файл. В home директорията
						на акаунта ще намерите системен php.ini файл, който е с име <b>php.ini</b>. Можете да го редактирате
						посредством файловия мениджър или чрез текстов редактор. Необходимо е да добавите следния ред:
					</p>
					<p>
						<b>extension=zip.so</b>
					</p>
					<p>
						2. Стандартно php.ini файла има действие само за директорията, в която се намира. За да важи за целия
						хостинг акаунт, е необходимо да проверите дали в <b>.htaccess</b> файл в home директорията има добавени
						следните редове. Ако ги няма, то е необходимо да ги добавите:
					</p>
					<p>
						<b>
							&lt;IfModule mod_env.c&gt;
							<br />
							SetEnv PHPRC /home/<?php echo $user; ?>/php.ini
							<br />
							&lt;/IfModule&gt;
						</b>
					</p>
					<p>
						След като сте извършили посочените действия, можете да се уверите че zip.so е активиран, като използвате функцията <a href="http://bg2.php.net/manual/en/function.phpinfo.php">phpinfo()</a>.
					</p>
				</div>
				<br />
				<div id="on" style="text-align:left; border: 1px solid #5599BF;padding: 5px;">
					4. За активиране на <b>Zend Optimizer</b> натиснете <a href="javascript:switchit('Fourth')">тук</a>.
				</div>
				<div id="Fourth" style="text-align:justify; font-size:14px; display:none">
					<p>
						1. На всички Linux хостинг планове се поддържа Zend Optimizer. За да go ползвате, е необходимо той да бъде активиран.
						Това може да бъде направено в php.ini файл. В home директорията на акаунта ще намерите системен php.ini файл, който
						е с име <b>php.ini</b>. Можете да го редактирате посредством файловия мениджър или чрез текстов редактор.
						Необходимо е да добавите следния ред:
					</p>
					<p>
						<b>
							zend_extension=/usr/local/Zend/lib/Optimizer/php-<?php echo floatval(phpversion()); ?>.x/ZendOptimizer.so
						</b>
					</p>
					<p>
						2. Стандартно php.ini файла има действие само за директорията, в която се намира. За да важи за целия хостинг акаунт,
						е необходимо да проверите дали в <b>.htaccess</b> файл в home директорията има добавени следните редове. Ако ги няма,
						то е необходимо да ги добавите:
					</p>
					<p>
						<b>
							&lt;IfModule mod_env.c&gt;
							<br />
							SetEnv PHPRC /home/<?php echo $user; ?>/php.ini<br />
							&lt;/IfModule&gt;
						</b>
					</p>
					<p>
						След като сте извършили посочените действия, можете да се уверите че Zend е активиран, като използвате функцията <a href="http://bg2.php.net/manual/en/function.phpinfo.php">phpinfo()</a>.
					</p>
				</div>
				<br />
				<div id="on" style="text-align:left; border: 1px solid #5599BF;padding: 5px;">
					5. За активация на <b>IonCube Loader</b> натиснете <a href="javascript:switchit('Fifth')">тук</a>.
				</div>
				<div id="Fifth" style="text-align:justify; font-size:14px; display:none">
				<p>
				<p>
					1. На всички Linux хостинг планове се поддържа IonCube Loader. За да go ползвате, е необходимо той да
					бъде активиран. Това може да бъде направено в php.ini файл. В home директорията на акаунта ще намерите
					системен php.ini файл, който е с име <b>php-fcgi.ini</b>. Можете да го редактирате посредством файловия
					мениджър или чрез текстов редактор. Необходимо е да добавите следния ред:
				</p>
				<p>
					Необходимо е IonCube Loader да бъде активиран. Това може да бъде направено в php.ini файл.
					В home директорията на акаунта ще намерите системен php.ini файл, който е с име <b>php.ini</b>.
					Можете да го редактирате посредством файловия мениджър или чрез текстов редактор. Необходимо е да добавите следния ред:
				</p>
				<p>
					<b>
						zend_extension = /usr/local/ioncube/ioncube_loader_lin_<?php echo floatval(phpversion()); ?>.so
					</b>
				</p>
				<p>2. Стандартно php.ini файла има действие само за директорията, в която се намира. За да важи за целия хостинг акаунт,
					е необходимо да проверите дали в <b>.htaccess</b> файл в home директорията има добавени следните редове. Ако ги няма,
					то е необходимо да ги добавите:
				</p>
				<p>
					<b>
						&lt;IfModule mod_env.c&gt;
						<br />
						SetEnv PHPRC /home/<?php echo $user ?>/php.ini
						<br />
						&lt;/IfModule&gt;
					</b>
				</p>
				<p>
					След като сте извършили посочените действия, можете да се уверите че IonCube е активиран, като използвате функцията <a href="http://bg2.php.net/manual/en/function.phpinfo.php">phpinfo()</a>.
				</p>
			</div>
			<br />
			<div id="on" style="text-align:left; border: 1px solid #5599BF;padding: 5px;">
				6. За промяна на други директиви на PHP посетете нашата помощна страница
				<a href="http://help.superhosting.bg/category/linux-hosting/php" target="_blank">http://help.superhosting.bg/category/linux-hosting/php</a>.
			</div>
			</span>
			<br />
			<br />
<?php } ?>
			<br />
			<br />
			<span style="text-align:center;font-size:16px;font-family:Verdana;">
				В случай, че срещнете затруднение или имате нужда от съдействие, не се колебайте да се свържете с отдела по 
				<a href="https://support.superhosting.bg" target="_blank" title="Система за обслужване на клиенти на СуперХостинг.БГ">Техническа поддръжка</a>.
			</span>
                </div>
         </div>
         </div>
		<script type="text/javascript">
			var facing = "First";

			function switchit(list){
				var listElementStyle=document.getElementById(list).style;

				if (list != facing){
					document.getElementById(facing).style.display="none";
				};
				if (listElementStyle.display=="none"){
					$("#" + list).slideToggle(500);
				} else {
					$("#" + list).slideToggle(500);
				}
				facing = list;
			}
		</script>
	 </body>
</html>
