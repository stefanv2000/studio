<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractController', 'dispatch', function($e) {
            $controller = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $controller->layout($moduleNamespace . '/layout');
        }, 100);
 
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
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
                'menuContent' => function ($serviceManager) {
                    $sectionMapper = $serviceManager->getServiceLocator()->get('Entities_SectionMapper');
                    $config = $serviceManager->getServiceLocator()->get("Config");
                    return new \Application\View\Helper\MenuContentHelper($sectionMapper,new \Frontend\Infrastructure\Cache\CacheJson($config['data.cache.json']));
                },
                'searchContent' => function ($serviceManager) {
                    $sectionMapper = $serviceManager->getServiceLocator()->get('Entities_SectionMapper');
                    $config = $serviceManager->getServiceLocator()->get("Config");
                    return new \Application\View\Helper\SearchContentHelper($sectionMapper,new \Frontend\Infrastructure\Cache\CacheJson($config['data.cache.json']));
                },
                'menuShow' => function ($serviceManager) {
                    return new \Application\View\Helper\MainMenuHelper($serviceManager->getServiceLocator()->get('Entities_SectionMapper'));
                }

            )
        );
    }
}
