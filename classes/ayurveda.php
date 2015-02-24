<?php
use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
            'translator.messages' => array(),));
        $this->silex->register(new Silex\Provider\SwiftmailerServiceProvider());
        $this->silex->register(new Silex\Provider\ValidatorServiceProvider());
        $this->silex->register(new Silex\Provider\SessionServiceProvider(),
            array('swiftmailer.options'=>array(
                'host' => 'ns0.ovh.net',
                'port' => '587',
                'username' => 'postmaster@ayurveda-concept.com',
                'password' => 'i6QuqQ8K',
                'encryption' => null,
                'auth_mode' => null
            ))
        );
    }

    public function registerRoutes()
    {
        $app = $this->silex;

        // l'url "/" va rendre le twig hompeage.html.twig
        $this->silex->get('/', function () use ($app) {
            return $app['twig']->render('main/homepage.html.twig', array());
        })->bind('homepage');

        $this->silex->get('/ayurveda', function () use ($app) {
            return $app['twig']->render('main/ayurveda.html.twig', array());
        })->bind('ayurveda');

        $this->silex->match('/ayurveda-lyon', function (Request $request) use ($app) {
            $form = $app['form.factory']->createBuilder('form')
                ->add('vata', 'choice', array(
                    'choices' => array('Etes-vous trés grand, trés petit ou mince?', 'Votre stature est-elle légére ou étroite?',
                    'Etes-vous mince et trouvez-vous difficil de prendre du poids?', 'Votre teint est foncé?', 'Votre chevelure est normalement dense?',
                    'Vos yeux sont petits, étroits ou creux?','Vos yeux sont foncés ou gris?', 'Vos dents sont saillantes?',
                    'Vos dents sont trés petites ou trés grandes?', 'Vous avez peu d\'endurance?', 'Vous préféré le chaud ou froid?',
                    'Vous êtes souvent constipé?', 'Votre voix est faible, basse, rauque ou tremblotante?', 'Vous parlez vite?',
                    'Vous préférez les aliments sucrés, salés, lourds ou gras?','Votre pouls est supérieur a 70 pour un homme, 80 pour une femme?'),
                    'multiple'  => true,
                    'expanded'  => true,
                    'required'  => false,
                ))
                ->add('pitta', 'choice', array(
                    'choices' => array('Etes-vous de taille ou musculature moyenne?', 'Etes-vous de poid moyen?',
                        'Vous transpirez beaucoup?', 'Votre peau est douce et chaude?', 'Votre teint est blanc ou rose?',
                        'Vos cheveux sont fins, roux ou blonds?','Vos yeux ont une taille normale?', 'Vos yeux sont bleu, gris ou noisette?',
                        'Vos dents sont de taille normale et jaunissantes?', 'Vous possédez endurance et force?', 'Vous préféré la fraîcheur à la chaleur?',
                        'Vos intestins ont tendance à produire des selles molles?', 'Vous parlez d\'une manière convaincante et précise?', 'Vous préférez les aliments sucrés, légers, chauds et amères?',
                        'Avez-vous souvent faim et vous vous sentez mal si vous manquez un repas?','Votre pouls est entre 60-70 pour un homme, 70-80 pour une femme?'),
                    'multiple'  => true,
                    'expanded'  => true,
                    'required'  => false, ))
                ->add('kapha', 'choice', array(
                    'choices' => array('Etes-vous solide ou un peu gros?', 'Votre visage est large et plein?',
                        'Vous prenez du poid facilement?', 'Vous transpirez peu?', 'Votre peau est moite et froide?',
                        'Votre teint est pâle?','Vos cheveux sont épais, lustrés et brun?', 'Vos yeux sont grands?',
                        'Vos yeux sont bleu ou marron?', 'Vos dents sont blanches, vos gencives solides?', 'Vos dents sont grandes?',
                        'Vous vous déplacez lentement?', 'Vous avez une bonne endurance?', 'Vous avez une digestion normale?',
                        'Vous avez un appetit régulier et la capacité de sauter un repas?','Vous parlez lentement?',
                        'Vous préférez les aliments secs, sans graisse, sucrés et épicés?', 'Votre pouls est inférieur à 60 pour un homme, 70 pour une femme?'),
                    'multiple'  => true,
                    'expanded'  => true,
                    'required'  => false, ))
                ->add('Envoyer', 'submit')
                ->add('effacer', 'reset')
                ->getForm();

            $form->handleRequest($request);

            if ($form->isValid()) {

                $data=$form->getData();

                $a=$data['vata'];
                $b=$data['pitta'];
                $c=$data['kapha'];

                $vata=count($a);
                $pitta=count($b);
                $kapha=count($c);

                if ($vata>$pitta and $vata>$kapha){
                    return $app->redirect('vata');
                }
                if ($pitta>$vata and $pitta>$kapha){
                    return $app->redirect('pitta');
                }
                if ($kapha>$vata and $kapha>$pitta){
                    return $app->redirect('kapha');
                }
                if($kapha==0 && $vata==0 && $pitta==0){
                    $app['session']->getFlashBag()->add('message2', 'Veuillez cocher au minimum une case du questionnaire pour pouvoir connaitre votre dosha.');
                }
                else{
                    $app['session']->getFlashBag()->add('message', $vata.' case dans la section Vata, '.$pitta.' case dans la section Pitta, '.$kapha.' case dans la section Kapha');
                }

            }
            // display the form
            return $app['twig']->render('main/ayurveda-lyon.html.twig', array('form' => $form->createView()));
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

        $this->silex->get('/formation-ayurveda', function () use ($app) {
            return $app['twig']->render('main/formation-ayurveda.html.twig', array());
        })->bind('formation-ayurveda');

        $this->silex->get('/calendrier-formation', function () use ($app) {
            return $app['twig']->render('main/calendrier-formation.html.twig', array());
        })->bind('calendrier-formation');

        $this->silex->get('/massage-bienetre', function () use ($app) {
            return $app['twig']->render('main/massage-bienetre.html.twig', array());
        })->bind('massagebienetre');

        $this->silex->match('/massage-domicil-lyon', function (Request $request) use ($app) {
            $form = $app['form.factory']->createBuilder('form')
                ->add('Nom')
                ->add('email', 'email')
                ->add('message', 'textarea')
                ->add('Envoyer', 'submit')
                ->getForm();

            $form->handleRequest($request);

           if ($form->isValid()) {

               $data=$form->getData();

               $errors = $app['validator']->validateValue($data['email'], new Assert\Email());

               if (count($errors) > 0) {
                   $app['session']->getFlashBag()->add('message', 'Veuillez utiliser un mail valide');
               } else {
                   $message = \Swift_Message::newInstance()
                       ->setSubject($data['Nom'].' : Contact Ayurveda Concept')
                       ->setFrom(array($data['email']))
                       ->setTo(array('ayurveda.concept@gmail.com'))
                       ->setBody($data['message']);

                   $app['mailer']->send($message);
                   $app['session']->getFlashBag()->add('message', 'Merci pour votre message et à bientôt !');
                   return $app->redirect('/massage-domicil-lyon', 301);
               }
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

        $this->silex->get('/fasciatherapie-lyon', function () use ($app) {
            return $app['twig']->render('main/fasciatherapie-lyon.html.twig', array());
        })->bind('fasciatherapielyon');

        $this->silex->get('/fascia-lyon', function () use ($app) {
            return $app['twig']->render('main/fascia-lyon.html.twig', array());
        })->bind('fascialyon');

       $this->silex->get('/error', function () use ($app) {
           return $app['twig']->render('main/error.html.twig', array());
        })->bind('error');

       $this->silex->error(function (\Exception $e, $code) {
            switch ($code) {
                case 404:
                    $this->silex['session']->getFlashBag()->add('message', 'Page Introuvable.');
                    return new Response( $this->silex['twig']->render('main/error.html.twig'), 404);
                    break;
                default:
                    $this->silex['session']->getFlashBag()->add('message', 'Erreur interne');
                    return new Response( $this->silex['twig']->render('main/error.html.twig'), 500);
            }
       });
    }

    public function run()
    {
        $this->silex->run();
    }

} 
