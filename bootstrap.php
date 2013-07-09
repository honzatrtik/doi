<?php

$loader = require_once __DIR__.'/autoload.php';

$app = new Silex\Application();

if (!defined('ENVIRONMENT'))
{
	$environment = isset($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'devel';
	define('ENVIRONMENT', $environment);
}

if (!defined('BASE_DIR'))
{
	define('BASE_DIR', __DIR__);
}

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

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app['url_generator']->setStrictRequirements(false);


$app->register(new Silex\Provider\MonologServiceProvider(), array(
	'monolog.logfile' => __DIR__.'/log/access.log',
	'monolog.name' => 'doi',
));
$app['monolog'] = $app->share($app->extend('monolog', function($monolog, $app) {
    return $monolog;
}));


$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), $app['db.options']);

$doctrineOrmServiceProvider = new \Dflydev\Pimple\Provider\DoctrineOrm\DoctrineOrmServiceProvider();
$doctrineOrmServiceProvider->register($app);

require __DIR__ . '/services.php';

return $app;

