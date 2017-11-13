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
     * Object cache.
     *
     * @var EntityInterface[]
     */
    static private $mapping = [];

    /**
     * @inheritdoc
     */
    public function getEntityByController(BaseEntityControllerInterface $entityController): EntityInterface
    {
        $className = get_class($entityController);
        // Try to find it in the static cache first.
        if (isset(self::$mapping[$className])) {
            return clone self::$mapping[$className];
        }
        $fcdn_parts = explode('\\', $className);
        $entityControllerClass = array_pop($fcdn_parts);
        // Get rid of "Controller" from the namespace.
        array_pop($fcdn_parts);
        // Add "Entity" instead.
        $fcdn_parts[] = 'Entity';
        $entityControllerClassNameParts = preg_split('/(?=[A-Z])/', $entityControllerClass);
        // Add "Developer" from the interface name.
        $fcdn_parts[] = $entityControllerClassNameParts[1];
        $fcdn = implode('\\', $fcdn_parts);
        if (!class_exists($fcdn)) {
            throw new EntityNotFoundException($fcdn);
        }
        // Add it to to object cache.
        self::$mapping[$className] = new $fcdn();
        return clone self::$mapping[$className];
    }
}
