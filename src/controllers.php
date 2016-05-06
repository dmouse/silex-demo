<?php

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use RedBeanPHP\OODBBean;
use Silex\Application;
use RedBeanPHP\R;

$app->get("/", function() use($app) {
  return $app['twig']->render("index.html.twig");
})
->bind("home");

$app->get("/conferences", function() use($app) {
  $conferences = R::findAll('conference','ORDER BY schedule');

  return $app['twig']->render("conferences.html.twig", [
    "conferences" => $conferences
  ]);
})
->bind("conferences");

$app->match("/register", function(Request $request) use ($app) {

  $form = $app['form.factory']->createBuilder('form')
    ->add('title')
    ->add('author')
    ->add('description', 'textarea', [
      "attr" => [
      "rows" => 8,
        "class" => "form-control",
      ]
  ])
  ->add("schedule", "choice",[
    "choices" => [
      "10:00" => "10:00",
      "11:00" => "11:00",
      "12:00" => "12:00",
      "13:00" => "13:00",
      "14:00" => "14:00",
    ]
  ])
  ->getForm();

  // attach the request data to form
  $form->handleRequest($request);

  if ($form->isValid()) {
    $data = $form->getData();

    $conference = R::dispense("conference");
    $conference->title = $data['title'];
    $conference->author = $data['author'];
    $conference->description = $data['description'];
    $conference->schedule = $data['schedule'];
    $id = R::store( $conference );

    return $app->redirect(
      $app["url_generator"]->generate("conferences")
    );
  }

  return $app['twig']->render('register.html.twig', [
    "form" => $form->createView(),
  ]);

})
->bind("register");

// NUNCA HAGAS ESTO EN PRODUCCION
$app->get('/login', function () use ($app) {
    $username = $app['request']->server->get('PHP_AUTH_USER', false);
    $password = $app['request']->server->get('PHP_AUTH_PW');

    if ('flisol' === $username && 'nuncaPongasEstoEnProduccion' === $password) {
        $app['session']->set('user', [
          'username' => $username
        ]);

        return $app->redirect('/conference');
    }

    $response = new Response();
    $response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', 'site_login'));
    $response->setStatusCode(401, 'Please sign in.');
    return $response;
})
->bind("login");

$app->match("/conference/{id}/edit", function(Request $request, $id) use ($app) {
  $conference = R::findOne("conference", "id = ?", [ $id ]);

  $form = $app['form.factory']->createBuilder('form', $conference)
    ->add('title')
    ->add('author')
    ->add('description', 'textarea', [
      "attr" => [
      "rows" => 8,
        "class" => "form-control",
      ]
    ])
    ->add("schedule", "choice",[
      "choices" => [
      "10:00" => "10:00",
      "11:00" => "11:00",
      "12:00" => "12:00",
      "13:00" => "13:00",
      "14:00" => "14:00",
    ]
  ])
  ->getForm();

  // attach the request data to form
  $form->handleRequest($request);

  if ($form->isValid()) {
    $data = $form->getData();

    $conference->title = $data['title'];
    $conference->author = $data['author'];
    $conference->description = $data['description'];
    $conference->schedule = $data['schedule'];
    $id = R::store( $conference );

    return $app->redirect(
      $app["url_generator"]->generate("conferences")
    );
  }

  return $app['twig']->render("register.html.twig", [
    'form' => $form->createView(),
  ]);
})
->before(function (Request $request, Application $app) {
  $user = $app['session']->get('user');
  if (null === $user) {
    return new RedirectResponse('/login');
  }
})
->method("GET|POST")
->assert("id", '\d+')
->bind("conference-edit");

$app->get("/conference/{conference}", function(OODBBean $conference) use($app) {

  return $conference;
})
->convert('conference', function($id) {
  return R::findOne("conference", "id = ?", [ $id ]);
})
->bind("conference");
