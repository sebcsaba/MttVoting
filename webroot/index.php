<?php

require_once('classes/core/_functions.php');
require_once('classes/core/Autoloader.php');

$autoloader = new Autoloader('temp/autoload.cache.dat');
spl_autoload_register(array($autoloader,'load'));
$autoloader->addDirectory('classes');

$config = require_once('config/config.php');
$di = new DI($config['impl'], array());

$handler = $di->create('RequestHandler');
$handler->run();
