<?php

$app['doi.numberGenerator'] = $app->share(function($app) {
	return new \Doi\DoiNumberGenerator(new \Doi\SequenceGenerator($app['orm.em']), $app['doi.prefix']);
});


$app['doi.factory'] = $app->share(function($app) {
	return new \Doi\Model\DoiFactory($app['doi.numberGenerator']);
});


$app['security.authenticator'] = $app->share(function($app) {
	$repository = $app['orm.em']->getRepository('\Doi\Entity\User');
	return new \Doi\Security\Authenticator($repository);
});

$app['guzzle.client'] = function($app)  {
	return new \Guzzle\Http\Client();
};

$app['journalConfig'] = $app->share(function($app) {
	$configFile = __DIR__ . '/config/journals.json';

	if (!$contents = file_get_contents($configFile))
	{
		throw new RuntimeException(sprintf('Can not load journal config file "%".', $configFile));
	}

	if (!$data = json_decode($contents, true))
	{
		throw new RuntimeException(json_last_error());
	}

	$journalConfig = new \Doi\JournalConfigProvider();
	foreach($data as $config)
	{
		$journalConfig->add($config);
	}

	return $journalConfig;
});

$app['crossref.depositor'] = $app->share(function($app) {

	$client = $app['guzzle.client'];
	$client->setBaseUrl($app['crossref.baseUrl']);

	$depositor = new \Doi\Crossref\Depositor(
		$client,
		$app['crossref.username'],
		$app['crossref.password']
	);

	$depositor->setDispatcher($app['dispatcher']);

	return $depositor;
});