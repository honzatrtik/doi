<?php

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();


define('ENVIRONMENT', defined('ENVIRONMENT') ? ENVIRONMENT : 'devel');
define('BASE_DIR', __DIR__);

// Globalni konfigurace
$replacements = array(
	'baseDir' => BASE_DIR,
);


$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . '/config/global.json', $replacements));
$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . '/config/local.json', $replacements));

$environmentConfig = BASE_DIR . '/config/' . ENVIRONMENT . '.json';
if (is_readable($environmentConfig))
{
	$app->register(new Igorw\Silex\ConfigServiceProvider($environmentConfig, $replacements));
}

\Tracy\Debugger::enable(NULL, $app['log.dir']);


$app->register(new Silex\Provider\DoctrineServiceProvider(), $app['db.options']);


return $app;

