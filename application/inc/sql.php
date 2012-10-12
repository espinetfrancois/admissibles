<?php
$config = new Config();
$db     = new PDO('mysql:host='.$config->get_dbhost().';dbname='.$config->get_dbbase(),$config->get_dblogin(),$config->get_dbpass());
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$parametres = new Parametres($db);
$_SESSION['db'] = $db;