<?php
use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
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
        $this->silex->register(new \Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__ . '/../views',
            'twig.class_path' => __DIR__.'/vendor/twig/lib',));
        // service d'url
        $this->silex->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        // form
        $this->silex->register(new \Silex\Provider\FormServiceProvider());
        $this->silex->register(new Silex\Provider\TranslationServiceProvider(), array(
            'translator.messages' => array(),
        ));
        $this->silex->register(new Silex\Provider\SwiftmailerServiceProvider());
    }

    public function registerRoutes()
    {
        $app = $this->silex;

        // l'url "/" va rendre le twig hompeage.html.twig
        $this->silex->get('/', function () use ($app) {
            return $app['twig']->render('main/homepage.html.twig', array());
        })->bind('homepage');

        $this->silex->get('/ayurveda-lyon', function () use ($app) {
            return $app['twig']->render('main/ayurveda-lyon.html.twig', array());
        })->bind('ayurvedalyon');

        $this->silex->get('/demo-massage', function () use ($app) {
            return $app['twig']->render('main/demo-massage.html.twig', array());
        })->bind('demomassage');

        $this->silex->get('/espace-ayurveda-lyon', function () use ($app) {
            return $app['twig']->render('main/espace-ayurveda-lyon.html.twig', array());
        })->bind('espaceayurvedalyon');

        $this->silex->get('/kapha', function () use ($app) {
            return $app['twig']->render('main/kapha.html.twig', array());
        })->bind('kapha');

        $this->silex->get('/vata', function () use ($app) {
            return $app['twig']->render('main/vata.html.twig', array());
        })->bind('vata');

        $this->silex->get('/pitta', function () use ($app) {
            return $app['twig']->render('main/pitta.html.twig', array());
        })->bind('pitta');

        $this->silex->get('/massage-bienetre', function () use ($app) {
            return $app['twig']->render('main/massage-bienetre.html.twig', array());
        })->bind('massagebienetre');

        $this->silex->get('/massage-domicil-lyon', function (Request $request) use ($app) {
            $data = array('Nom' => 'Nom / Prénom','email' => 'Email','message' => 'Votre message');
            $form = $app['form.factory']->createBuilder('form', $data)
                ->add('Nom')
                ->add('email')
                ->add('message', 'textarea')
                ->getForm();

            $form->handleRequest($request);

            if ($form->isValid()) {

                $form->getData();
                $request = $app['request'];

                $message = \Swift_Message::newInstance()
                    ->setSubject($request->get($form['Nom']).' : Contact Ayurveda Concept')
                    ->setFrom(array($request->get($form['email'])))
                    ->setTo(array('hello@usine-creative.com'))
                    ->setBody($request->get($form['message']));

                $app['mailer']->send($message);

                return new Response('Merci pour votre message!', 201);
            }

            // display the form
            return $app['twig']->render('main/massage-domicil-lyon.html.twig', array('form' => $form->createView()));
        })->bind('massagedomicillyon');

        $this->silex->get('/masseur-lyon', function () use ($app) {
            return $app['twig']->render('main/masseur-lyon.html.twig', array());
        })->bind('masseurlyon');

        $this->silex->get('/relaxation-lyon', function () use ($app) {
            return $app['twig']->render('main/relaxation-lyon.html.twig', array());
        })->bind('relaxationlyon');

        $this->silex->get('/tarif-massage-lyon', function () use ($app) {
            return $app['twig']->render('main/tarif-massage-lyon.html.twig', array());
        })->bind('tarifmassagelyon');


    }

    public function run()
    {
        $this->silex->run();
    }

} 
