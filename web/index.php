<?php 
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->get('{user}', function ($user) use ($app) {
	return 'hello '.$app->escape($user);
});

$app->run();