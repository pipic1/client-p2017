<?php

require('../vendor/autoload.php');
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

$current_book = null;
$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->register(new Silex\Provider\FormServiceProvider());


$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->get('/book_sample', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  $url = 'https://shopping-service-p2017.herokuapp.com/book?isbn=123456789'; // sample
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL,$url);
  $result=curl_exec($ch);
  curl_close($ch);
  $result=json_decode($result);
  $current_book = $result;
  return $app['twig']->render('book.twig',array('book' => $result));
});

$app->get('/book/{isbn}', function($isbn) use($app) {
  $app['monolog']->addDebug('logging output.');
  $url = 'https://shopping-service-p2017.herokuapp.com/book?isbn='.$isbn;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL,$url);
  $result=curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  $result=json_decode($result);
  $current_book = $result;
  if ($httpcode === 404) {
    return $app['twig']->render('error.twig',array('message' =>  $result));
  }
  return $app['twig']->render('book.twig',array('book' => $result, 'message'=>""));
});

$app->get('/books', function() use($app) {
  $url = 'https://shopping-service-p2017.herokuapp.com/books'; // sample
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL,$url);
  $result=curl_exec($ch);
  curl_close($ch);
  return $app['twig']->render('books.twig',array('books' => json_decode($result) ));
});

$app->post('/buy', function(Request $request) use($app) {
  $test = $request->get('isbn');
  $test2 = $request->get('quantity');
  var_dump($test);
  var_dump(quantity);
  $result = "le livre a bien été achete.";
  return $app['twig']->render('book.twig',array('book' => $current_book, 'message' => $result));
});

$app->get('/cowsay', function() use($app) {
  $app['monolog']->addDebug('cowsay');
  return "<pre>".\Cowsayphp\Cow::say("Cool beans")."</pre>";
});

$app->run();
