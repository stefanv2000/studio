<?php

namespace Entities\Mapper;

use Doctrine\ORM\EntityManager;

class AbstractMapper {
	
	/**
	 *
	 * @var EntityManager
	 */
	protected $em;
	
	/**
	 *
	 * @param EntityManager $em        	
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}
	
	/**
	 * get an entities repository based on type
	 *
	 * @param string $type
	 *        	the type of the entities for which the repository is returned
	 * @return \Doctrine\ORM\EntityRepository null
	 */
	public function getRepository($type) {
		switch ($type) {
			case "section" :
				return $this->em->getRepository ( "Entities\Entity\Section" );
		}
		return null;
	}
}

?>