<?php

require_once('webroot/classes/core/Autoloader.php');
$autoloader = new Autoloader('webroot/temp/autoload.cache.dat');
spl_autoload_register(array($autoloader,'load'));
$autoloader->addDirectory('webroot/classes');
$autoloader->addDirectory('tests');
