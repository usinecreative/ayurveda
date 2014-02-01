<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__ . '/../classes/ayurveda.php';
use Silex\Provider\FormServiceProvider;

$app = new Ayurveda();
$app->run();
