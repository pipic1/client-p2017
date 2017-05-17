<?php

require('../vendor/autoload.php');
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
  return $app['twig']->render('book.twig',array('book' => json_decode($result) ));
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
  return getForm($app->request);
});

public function getForm($request)
{
  $data = array(
    'quantity' => 'Your quantity',
  );

  $form = $app['form.factory']->createBuilder(FormType::class, $data)
  ->add('quantity')
  ->add('submit', SubmitType::class, [
    'label' => 'Save',
  ])
  ->getForm();

  $form->handleRequest($request);

  if ($form->isValid()) {
    $data = $form->getData();

    // do something with the data

    // redirect somewhere
    var_dump("test");
    return $app['twig']->render('book.twig',array('book' => $result));
  }

  // display the form
  return $app['twig']->render('books.twig',array('books' array('form' => $form->createView()));
  # code...
}

/*$app->post('/buybook', function() use($app) {
  $app->get('/buybook')->request->get('name');
  $post = array(
    'isbn' => $app->request->get('isbn'),
    'quantity'  => $app->request->get('quantity'),
  );
  var_dump($app->get('/buybook')->request->get('name'););
  return "<pre>".$app."</pre>";
});
*/
$app->get('/cowsay', function() use($app) {
  $app['monolog']->addDebug('cowsay');
  return "<pre>".\Cowsayphp\Cow::say("Cool beans")."</pre>";
});

$app->run();
