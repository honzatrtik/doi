<?php

$loader = require __DIR__.'/vendor/autoload.php';
if ($loader instanceof \Composer\Autoload\ClassLoader)
{
	$loader->add('Doi', __DIR__ . '/src');
	$loader->add('Doi\Test', __DIR__ . '/tests');
}

new \Doi\Test\WebTestCase();

return $loader;
