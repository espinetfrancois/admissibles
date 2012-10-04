<?php
echo $_SERVER['DOCUMENT_ROOT'];
require_once('classes/autoload.php');
$layout = new Layout();
$layout->addContent('coucou');
echo $layout;
?>
