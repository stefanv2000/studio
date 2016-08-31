<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use Entities\Util\Serializer;
use Frontend\Controller\ApiController;
use Zend\Mvc\Controller\ControllerManager;

return array(
    'router' => array(
        'routes' => array(


            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Frontend\Controller',
                        'controller' => 'Index',
                        'action' => 'home',
                    ),
                ),
                'may_terminate' => true,

                'child_routes' => array(
                    'intro' => array(
                        'type' => 'Regex',
                        'options' => array(
                            'regex' => '(?<link>(artists|emerging-artists|production|spokespeople))',
                            'defaults' => array(
                                'action' => 'hometype',
                            ),
                            'spec' => '%link%',
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'profileintro' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:type/:name',
                                    'defaults' => array(
                                        'action' => 'profileintro',
                                    ),
                                ),
                            ),

                            'portfolioPro' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:type/:name/:portfolio[/:gallery][/:imageindex]',
                                    'constraints' => array(
                                        'portfolio' => 'portfolio',
                                        'imageindex' => '[0-9]*',
                                    ),
                                    'defaults' => array(
                                        'action' => 'portfolio',
                                        'imageindex' => 0
                                    ),
                                ),
                            ),

                            'videoPro' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:type/:name/:portfolio[/:gallery][/:imageindex]',
                                    'constraints' => array(
                                        'portfolio' => 'videos',
                                        'imageindex' => '[0-9]*',
                                        'gallery' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                        'action' => 'portfolio',
                                        'imageindex' => 0
                                    ),
                                ),
                            ),

                            'profilePro' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:type/:name/profile',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'action' => 'profile',
                                    ),
                                ),
                            ),

                            'pressPro' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:type/:name/:portfolio[/:imageindex]',
                                    'constraints' => array(
                                        'portfolio' => 'press',
                                        'imageindex' => '[0-9]*',
                                    ),
                                    'defaults' => array(
                                        'action' => 'portfolio',
                                    ),
                                ),
                            ),


                        ),
                    ),

                    'fooddrink' => array(
                        'type' => 'Regex',
                        'options' => array(
                            'regex' => '(?<link>(food-_-drink|food_drink|influencers1))',
                            'defaults' => array(
                                'action' => 'hometype'
                            ),
                            'spec' => '%link%',
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'profileintro' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:name',
                                    'defaults' => array(
                                        'action' => 'profileintrofood',
                                    ),
                                ),
                            ),

                            'portfolio' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:name/:portfolio[/:gallery][/:imageindex]',
                                    'constraints' => array(
                                        'portfolio' => 'portfolio',
                                        'imageindex' => '[0-9]*',
                                    ),
                                    'defaults' => array(
                                        'action' => 'portfoliofood',
                                        'imageindex' => 0
                                    ),
                                ),
                            ),

                            'video' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:name/:portfolio[/:gallery][/:imageindex]',
                                    'constraints' => array(
                                        'portfolio' => 'videos',
                                        'imageindex' => '[0-9]*',
                                        'gallery' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                        'action' => 'portfoliofood',
                                        'imageindex' => 0
                                    ),
                                ),
                            ),

                            'profile' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:name/profile',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'action' => 'profilefood',
                                    ),
                                ),
                            ),

                            'press' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:name/press',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'action' => 'pressfood',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'post' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:postname',
                                            'constraints' => array(
                                            ),
                                            'defaults' => array(

                                                'action' => 'pressFoodPost',
                                            ),
                                        ),
                                    ),
                                ),
                            )


                        ),
                    ),

                    'models' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'models',
                            'defaults' => array(
                                'action' => 'modelsmain',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'search' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/search',
                                    'defaults' => array(
                                        'action' => 'home',
                                    ),
                                ),
                                'priority' => 1000,
                            ),
                            'category' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:category',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'action' => 'modelcategory',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'modelPage' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:name',
                                            'constraints' => array(),
                                            'defaults' => array(
                                                'action' => 'modelPage',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                    ),

                                    'modelGallery' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:name/:gallery[/:index]',
                                            'constraints' => array(
                                                'index' => '[0-9]*',
                                            ),
                                            'defaults' => array(
                                                'action' => 'portfolioModel',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                    ),
                                ),


                            ),
                        ),
                    ),

                    'pages' => array(
                        'type' => 'Regex',
                        'options' => array(
                            'regex' => '(?<link>(contact|special-occasion|international|become-a-model))',
                            'defaults' => array(
                                'action' => 'pages',
                            ),
                            'spec' => '%link%',
                        ),
                        'may_terminate' => true,
                    ),


                    /*
                                        'catchall' => array(
                                            'type'    => 'Regex',
                                            'options' => array(
                                                'regex' => '(?<link>(.*))',
                                                'priority' => 100,
                                                'defaults' => array(
                                                    'action'     => 'home',
                                                ),
                                                'spec' => '%link%',
                                            ),
                                        ),
                    //*/
                    'becomemodelupload' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'becomemodel/uploadfile',
                            'defaults' => array(
                                'action' => 'becomeModelUpload',
                            ),
                        ),
                    ),


                    'redirects' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'redirects',
                            'defaults' => array(
                                'action' => 'redirects',
                            ),
                        ),
                    ),

                ),
            ),

            'generatePdf' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/generate/pdf/:id',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Frontend\Controller',
                        'controller' => 'Index',
                        'action' => 'generatePdf',
                    ),
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                ),
                'may_terminate' => true,
            ),

            'api' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Frontend\Controller',
                        'controller' => 'Api',
                    ),
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'intro' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/intro',
                            'defaults' => array(
                                'action' => 'home',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'homeMobile' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/mobile',
                                    'defaults' => array(
                                        'action' => 'homeMobile',
                                    ),
                                ),
                            ),
                        ),

                    ),
                    'artists' => array(
                        'type' => 'Regex',
                        'options' => array(
                            'regex' => '/(?<link>(artists|emerging-artists|production|spokespeople))',
                            'defaults' => array(
                                'action' => 'introtype'
                            ),
                            'spec' => '%link%',
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'profileintro' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:type/:name',
                                    'defaults' => array(
                                        'action' => 'profileintro',
                                    ),
                                ),
                            ),

                            'profile' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:type/:name/profile',
                                    'defaults' => array(
                                        'action' => 'profile',
                                    ),
                                ),
                            ),

                            'portfolio' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:type/:name/:portfolio[/:gallery][/:imageindex]',
                                    'constraints' => array(
                                        'portfolio' => 'portfolio'
                                    ),
                                    'defaults' => array(
                                        'action' => 'portfoliocontent',
                                    ),
                                ),
                            ),

                            'video' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:type/:name/:portfolio[/:gallery][/:imageindex]',
                                    'constraints' => array(
                                        'portfolio' => 'videos',
                                        'gallery' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                        'action' => 'portfoliocontent',
                                    ),
                                ),
                            ),

                            'press' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:type/:name/press[/:imageindex]',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'action' => 'portfoliocontent',
                                        'portfolio' => 'press'
                                    ),
                                ),
                            )


                        ),
                    ),



                    'fooddrink' => array(
                        'type' => 'Regex',
                        'options' => array(
                            'regex' => '/(?<link>(food_drink|food-_-drink|influencers1))',
                            'defaults' => array(
                                'action' => 'introtype'
                            ),
                            'spec' => '%link%',
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'profileintro' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:name',
                                    'defaults' => array(
                                        'action' => 'profileintroFD',
                                    ),
                                ),
                            ),

                            'profile' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:name/profile',
                                    'defaults' => array(
                                        'action' => 'profileFD',
                                    ),
                                ),
                            ),

                            'portfolio' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:name/:portfolio/:gallery[/:imageindex]',
                                    'constraints' => array(
                                        'portfolio' => 'portfolio'
                                    ),
                                    'defaults' => array(

                                        'action' => 'portfoliocontentFD',
                                    ),
                                ),
                            ),

                            'video' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:name/:portfolio[/:gallery][/:imageindex]',
                                    'constraints' => array(
                                        'portfolio' => 'videos',
                                        'gallery' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(

                                        'action' => 'portfoliocontentFD',
                                    ),
                                ),
                            ),

                            'press' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:name/press',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'action' => 'presscontentFD',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'post' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:postname',
                                            'constraints' => array(
                                            ),
                                            'defaults' => array(

                                                'action' => 'pressPostFD',
                                            ),
                                        ),
                                    ),
                                ),
                            )


                        ),
                    ),

                    'models' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/models',
                            'defaults' => array(
                                'action' => 'models'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'search' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/search',
                                    'defaults' => array(
                                        'action' => 'search'
                                    ),
                                ),
                                'priority' => 1000,
                            ),
                            'category' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/:category',
                                    'constraints' => array(),
                                    'defaults' => array(
                                        'action' => 'modelscategory',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'modelpage' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/:name',
                                            'constraints' => array(),
                                            'defaults' => array(
                                                'action' => 'modelPage',
                                            ),
                                        ),
                                        'may_terminate' => true,
                                        'child_routes' => array(
                                            'gallery' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                    'route' => '/:gallery[/:index]',
                                                    'constraints' => array(),
                                                    'defaults' => array(
                                                        'action' => 'modelGallery',
                                                    ),
                                                ),
                                                'may_terminate' => true,
                                            )
                                        ),

                                    )
                                ),

                            ),
                        ),
                    ),

                    'contact' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/contact',
                            'defaults' => array(
                                'action' => 'contact',
                            ),
                        ),
                    ),

                    'international' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/international',
                            'defaults' => array(
                                'action' => 'international',
                            ),
                        ),
                    ),

                    'becomeamodel' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/become-a-model',
                            'defaults' => array(
                                'action' => 'becomeamodel',
                            ),
                        ),
                    ),

                    'sendbecomeamodel' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/become-a-model/send',
                            'defaults' => array(
                                'action' => 'sendBecomeamodel',
                            ),
                        ),
                    ),


                    'specialoccasion' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/special-occasion',
                            'defaults' => array(
                                'action' => 'specialoccasion',
                            ),
                        ),
                    ),
                ),
            ),

        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(),
        'aliases' => array(),
        'factories' => array(

        ),
    ),

    'controllers' => array(
        'invokables' => array(
        ),
        'factories' => array(
            'Frontend\Controller\Index'    => function(ControllerManager $cm) {
                $sm   = $cm->getServiceLocator();
                $sectionMapper = $sm->get('Entities_SectionMapper');
                $serializer = new Serializer($sm->get("Doctrine\ORM\EntityManager"));
                $config = $sm->get('Config');
                $controller = new \Frontend\Controller\IndexController($sectionMapper,$serializer,new \Frontend\Infrastructure\Cache\CacheJson($config['data.cache.json']));

                return $controller;
            },
            'Frontend\Controller\Api'    => function(ControllerManager $cm) {
                $sm   = $cm->getServiceLocator();
                $sectionMapper = $sm->get('Entities_SectionMapper');
                $serializer = new Serializer($sm->get("Doctrine\ORM\EntityManager"));
                $config = $sm->get('Config');
                $controller = new ApiController($sectionMapper,$serializer,new \Frontend\Infrastructure\Cache\CacheJson($config['data.cache.json']));
                return $controller;
            },
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'Frontend/layout' => __DIR__ . '/../view/layout/frontendlayout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(),
        ),
    ),

);
