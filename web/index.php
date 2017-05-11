<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;
$bookservice = "https://shopping-service-p2017.herokuapp.com/book/";
$stockservice = "";
$wholesalerservice = "";

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
  $subRequest = Request::create($bookservice,{isbn:123456789});
  var_dump($subRequest);
  $book = $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
  var_dump($book);
  return $app['twig']->render('books.twig');
});

$app->get('/cowsay', function() use($app) {
  $app['monolog']->addDebug('cowsay');
  return "<pre>".\Cowsayphp\Cow::say("Cool beans")."</pre>";
});

$app->run();
