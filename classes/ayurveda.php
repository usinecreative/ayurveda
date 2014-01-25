<?php

class Ayurveda
{
    /** @var \Silex\Application */
    protected $silex;

    public function __construct($isDebug = true)
    {
        $this->silex = new Silex\Application(array(
            'debug' => $isDebug
        ));
        $this->registerServices();
        $this->registerRoutes();
    }

    public function registerServices()
    {
        // service twig
        $this->silex->register(new \Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__ . '/../views'));
        // service d'url
        $this->silex->register(new Silex\Provider\UrlGeneratorServiceProvider());
    }

    public function registerRoutes()
    {
        $app = $this->silex;

        // l'url "/" va rendre le twig hompeage.html.twig
        $this->silex->get('/', function () use ($app) {
            return $app['twig']->render('main/homepage.html.twig', array());
        })->bind('homepage');


    }
    public function run()
    {
        $this->silex->run();
    }

} 
