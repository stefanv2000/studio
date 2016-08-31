<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use Application\Infrastructure\DatabaseUpdate\DatabaseUpdate;

return array(
    'router' => array(
        'routes' => array(
            'home1' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/1',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Admin',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/:action]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action'     => 'index',
                            ),
                        ),
                    ),

                    'update-database' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/update-database',
                            'defaults' => array(
                                'action'     => 'updateDatabase',
                            ),
                        ),
                    ),

                    'cleaning' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/cleaning',
                            'defaults' => array(
                                'action'     => 'cleaning',
                            ),
                        ),
                    ),

                ),
            ),

        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
        ),
        'factories' => array(
            'Application\Controller\Admin' => function(\Zend\Mvc\Controller\ControllerManager $cm){
                $em = $cm->getServiceLocator()->get("Doctrine\ORM\EntityManager");
                $em1 = $cm->getServiceLocator()->get("doctrine.entitymanager.ormtemp");
                $config = $cm->getServiceLocator()->get("Config");
                $sm   = $cm->getServiceLocator();
                $sectionMapper = $sm->get('Entities_SectionMapper');
                $dbup = new DatabaseUpdate($em1,
                    $config["remotedatabase"]["user_id"],
                    $config["remotedatabase"]["connection"]["servername"],
                    $config["remotedatabase"]["connection"]["database"],
                    $config["remotedatabase"]["connection"]["username"],
                    $config["remotedatabase"]["connection"]["password"]);
                $controller = new \Application\Controller\AdminController($dbup,$sectionMapper,new \Frontend\Infrastructure\Cache\CacheJson($config['data.cache.json']));
                return $controller;
            }
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'templatesPacker' => 'Application\View\Helper\TemplatesPacker',
            'displayMenu' => 'Application\View\Helper\MenuContentHelper',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'Application/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'strategies' => array(
        'ViewJsonStrategy',
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),

);
