<?php

$app = require_once __DIR__ . '/bootstrap.php';

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
	'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($app['orm.em'])
));
