<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../src/transcode.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/../views',
));

$app->register(new Instatom\TranscodeServiceProvider(), array(
	'transcode.default_name' => 'Gas',
));

$app->get('/u/{user}', function ($user) use ($app) {
	$instagram = $app['transcode']($user);
	return $app['twig']->render('atom.twig', array(
		'user' => $instagram->get(),
	));
});

$app->error(function (\Exception $e, $code) use ($app) {
	if ($app['debug']) {
		return;
	}
	switch ($code) {
		case 404:
			$message = 'The requested page could not be found.';
			break;
		default:
			$message = 'We are sorry, but something went terribly wrong.';
	}
	return new Response($message, $code);
});

$app->run();

