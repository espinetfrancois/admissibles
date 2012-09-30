<?php
require_once('classes/autoload.php');
$layout = new Layout();
$layout->addContent('coucou');
echo $layout;