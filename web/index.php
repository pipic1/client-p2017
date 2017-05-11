<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->get('/books', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,"https://shopping-service-p2017.herokuapp.com/book");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,"isbn=123456789");
  curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $server_output = curl_exec ($ch);

  curl_close ($ch);
  $book = $server_output;
  // further processing ....
  if ($server_output == "OK") {
    return $app['twig']->render('books.twig');
  } else {
   return $app['twig']->render('error.twig');
  }
});

$app->get('/cowsay', function() use($app) {
  $app['monolog']->addDebug('cowsay');
  return "<pre>".\Cowsayphp\Cow::say("Cool beans")."</pre>";
});

$app->run();
