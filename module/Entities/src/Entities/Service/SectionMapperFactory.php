<?php

namespace Entities\Service;

use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\FactoryInterface;
use Entities\Mapper\SectionMapper;

/**
 * 
 * @author Stefan Valea stefanvalea@gmail.com
 *
 */
class SectionMapperFactory implements FactoryInterface{
	/**
	 * creates an instance of the SectionMapper
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
		/** @var EntityManager $em */
		$em = $serviceLocator->get("Doctrine\ORM\EntityManager");
	
		$mapper = new SectionMapper($em);
		
		return $mapper;		
		
	}

	
}

?>
