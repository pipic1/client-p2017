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
  return $app['twig']->render('book.twig',array('book' => $result));
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
  $isbn = $request->get('isbn');
  $qte = $request->get('quantity');
  $url_buy = 'https://shopping-service-p2017.herokuapp.com/buy?isbn='.$isbn.'&quantity='.$qte;
  $curl_buy = curl_init();
  curl_setopt($curl_buy, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl_buy, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl_buy, CURLOPT_URL,$url_buy);
  $result_buy=curl_exec($curl_buy);
  curl_close($curl_buy);
  $message = json_decode($result_buy);
  var_dump($result_buy);
  //POST HEREE
  $url = 'https://shopping-service-p2017.herokuapp.com/book?isbn='.$isbn;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL,$url);
  $result=curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  $result=json_decode($result);
  var_dump($result);
  $message = "<strong>".$message."</strong>Le livre ".$result.name." a bien été achete en ".$qte." exemplaire(s).";
  return $app['twig']->render('book.twig',array('book' => $result, 'message' => $message));
});

$app->get('/cowsay', function() use($app) {
  $app['monolog']->addDebug('cowsay');
  return "<pre>".\Cowsayphp\Cow::say("Cool beans")."</pre>";
});

$app->run();
