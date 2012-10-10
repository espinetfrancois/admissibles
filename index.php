<?php
require_once('inc/autoload.php');
session_start();
require_once('inc/sql.php');


$layout = new Layout();
$layout->addContent('coucou');
$layout->setId(1);
echo $layout;
