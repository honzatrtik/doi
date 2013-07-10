<?php

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['controller.doi'] = $app->share(function($app) {
	return new \Doi\Controller\Doi($app['orm.em'], $app['doi.factory']);
});

$app['controller.batch'] = $app->share(function($app) {
	return new \Doi\Controller\Batch($app['orm.em'], $app['crossref.depositor'], $app['journalConfig']);
});


$app->post('/batch', 'controller.batch:post');

$app->get('/batch/{batch}', 'controller.batch:get')
	->bind('batch.get');

$app->post('/doi', 'controller.doi:post');


$app->get('/doi/{doi}', 'controller.doi:get')
	->bind('doi.get')
	->assert('doi', \Doi\Controller\Doi::DOI_PATTERN_REGEX);

$app->delete('/doi/{doi}', 'controller.doi:delete')
	->assert('doi', \Doi\Controller\Doi::DOI_PATTERN_REGEX);

$app->get('/batch/{doi}/batches', 'controller.batch:getAllByDoi')
	->assert('doi', \Doi\Controller\Doi::DOI_PATTERN_REGEX)
	->bind('batch.getAllByDoi');


$app->error(function(\Symfony\Component\HttpKernel\Exception\HttpException $e, $code) use ($app) {

	return $app->json(array(
		'message' => $e->getMessage(),
	), $e->getStatusCode());

});