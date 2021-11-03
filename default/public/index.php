<?php
require '../functions/autoload.php';

$routes = new \Jobs\Routes();

$entryPoint = new \CSY2028\EntryPoint($routes);

$entryPoint->run();
