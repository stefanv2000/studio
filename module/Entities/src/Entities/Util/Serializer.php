<?php

namespace Entities\Util;

/**
 * Class Serializer
 *
 * @author Steffen Brem
 */
class Serializer
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $_em;

    /**
     * Constructor
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * Serialize entity to array
     *
     * @param $entityObject
     * @return array
     */
    public function serialize($entityObject)
    {
        $data = array();

        $className = get_class($entityObject);
        $metaData = $this->_em->getClassMetadata($className);

        foreach ($metaData->fieldMappings as $field => $mapping)
        {
            $method = "get" . ucfirst($field);
            $data[$field] = call_user_func(array($entityObject, $method));
        }

        foreach ($metaData->associationMappings as $field => $mapping)
        {
            break;//todo ???
            // Sort of entity object
            $object = $metaData->reflFields[$field]->getValue($entityObject);
            $data[$field] = $this->serialize($object);
        }

        return $data;
    }
}