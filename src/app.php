<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use RedBeanPHP\R;

$app = new Application();

$app->register(new TwigServiceProvider());
$app->register(new MonologServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new TranslationServiceProvider());

$root = __DIR__ . '/..';
$app['monolog.logfile'] = $root . '/var/logs/silex.log';
$app['twig.path'] = [ $root . '/app/templates'];
$app['session.storage.handler'] = null;
$app['debug'] = true;
R::setup();

return $app;
