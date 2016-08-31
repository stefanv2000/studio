<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Frontend;

use Entities\Util\Serializer;
use Frontend\Controller\ApiController;
use Zend\EventManager\Event;
use Zend\Http\Response;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach('finish', function ($e)  {

            //return;
            /** @var MvcEvent $e */
            $matches    = $e->getRouteMatch();
            if ($matches==null) return;
            $controller = $matches->getParam('controller');
            
            if (false === strpos($controller, __NAMESPACE__)) {
                // not a controller from this module
                return;
            }

            /** @var Response $response */
            $response = $e->getResponse();
            $headers = $response->getHeaders();

            $date = gmdate( 'D, d M Y H:i:s',time()+60*60 ) . ' GMT';
            $headers->addHeaders(array(
                'Expires' => $date,
            ));

            $response->setHeaders($headers);
        });


        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);


    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getControllerConfig(){
        return array(
            'factories' => array(
                'Frontend\Controller\Api'    => function(ControllerManager $cm) {
                    $sm   = $cm->getServiceLocator();
                    $sectionMapper = $sm->get('Entities_SectionMapper');
                    $serializer = new Serializer($sm->get("Doctrine\ORM\EntityManager"));
                    $controller = new ApiController($sectionMapper,$serializer);
                    return $controller;
                },
            ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'seoInfo' => function ($serviceManager) {
                    return new \Frontend\View\Helper\SeoInfoViewHelper();
                }

            )
        );
    }
}
