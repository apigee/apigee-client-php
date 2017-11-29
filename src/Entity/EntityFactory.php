<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\Exception\EntityNotFoundException;

/**
 * Class EntityFactory.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
final class EntityFactory implements EntityFactoryInterface
{
    /**
     * Stores mapping of entity classes by controllers.
     *
     * @var string[]
     */
    static private $classMappingCache = [];

    /**
     * Entity object cache.
     *
     * @var EntityInterface[]
     */
    static private $objectCache = [];

    /**
     * @inheritdoc
     */
    public function getEntityTypeByController($entityController): string
    {
        $className = $this->getClassName($entityController);
        // Try to find it in the static cache first.
        if (isset(self::$classMappingCache[$className])) {
            return self::$classMappingCache[$className];
        }
        $fcdn_parts = explode('\\', $className);
        $entityControllerClass = array_pop($fcdn_parts);
        // Get rid of "Controller" from the namespace.
        array_pop($fcdn_parts);
        // Add "Entity" instead.
        $fcdn_parts[] = 'Entity';
        $entityControllerClassNameParts = preg_split('/(?=[A-Z])/', $entityControllerClass);
        // First index is an empty string, the last one is "Controller". Let's get rid of those.
        array_shift($entityControllerClassNameParts);
        array_pop($entityControllerClassNameParts);
        $fcdn_parts[] = implode('', $entityControllerClassNameParts);
        $fcdn = implode('\\', $fcdn_parts);
        if (!class_exists($fcdn)) {
            throw new EntityNotFoundException($fcdn);
        }
        // Add it to to object cache.
        self::$classMappingCache[$className] = $fcdn;
        return self::$classMappingCache[$className];
    }

    /**
     * @inheritdoc
     */
    public function getEntityByController($entityController): EntityInterface
    {
        $className = $this->getClassName($entityController);
        $fcdn = self::getEntityTypeByController($entityController);
        // Add it to to object cache.
        self::$objectCache[$className] = new $fcdn();
        return clone self::$objectCache[$className];
    }

    /**
     * Helper function that returns the FQCN of a class.
     *
     * @param string|\Apigee\Edge\Entity\AbstractEntityController $entityController
     *   Fully qualified class name or an object.
     *
     * @return string
     */
    private function getClassName($entityController): string
    {
        $className = $entityController;
        if (is_object($entityController)) {
            $className = get_class($entityController);
        }
        return $className;
    }
}
