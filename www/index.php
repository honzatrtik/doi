<?php

$app = require(__DIR__  . '/../bootstrap.php');

// Security (X-Username & X-Password)
$app->register(new RequestSecurityServiceProvider($this->app['security.authenticator']));

// Events
$dispatcher = $app['dispatcher'];


require(__DIR__  . '/../routing.php');

\Tracy\Debugger::enable(NULL, $app['log.dir']);

$app->run();