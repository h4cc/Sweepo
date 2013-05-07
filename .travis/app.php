<?php
use Symfony\Component\HttpFoundation\Request;

set_time_limit(0); // let travis expire itself

// set default date time
date_default_timezone_set('UTC');

// xdebug configuration
ini_set('xdebug.cli_color', 2);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.max_nesting_level', 2000);

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
