<?php

date_default_timezone_set('America/Mexico_city');

require_once __DIR__ . "/../vendor/autoload.php";

$app = require_once __DIR__ . "/../src/app.php";
require_once __DIR__ . "/../src/controllers.php";

$app->run();
