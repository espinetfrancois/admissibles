<?php

require_once('classes/autoload.php');
$layout = new Layout();
$layout->addContent('coucou');
$layout->setId(1);
echo $layout;
