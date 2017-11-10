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

    public function getEntityByController(BaseEntityControllerInterface $entityController): EntityInterface
    {
        $interfaces = class_implements($entityController);
        // Last interface what we concerns us because it is the entity specific interface.
        // Example.: Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface
        $interface = end($interfaces);
        // Try to find it in the static cache first.
        if (isset(self::$mapping[$interface])) {
            return clone self::$mapping[$interface];
        }

        $fcdn_parts = explode('\\', $interface);
        $entityControllerInterface = array_pop($fcdn_parts);
        // Get rid of "Controller" from the namespace.
        array_pop($fcdn_parts);
        // Add "Entity" instead.
        $fcdn_parts[] = 'Entity';
        $entityControllerInterfaceParts = preg_split('/(?=[A-Z])/', $entityControllerInterface);
        // Add "Developer" from the interface name.
        $fcdn_parts[] = $entityControllerInterfaceParts[1];
        $fcdn = implode('\\', $fcdn_parts);
        if (!class_exists($fcdn)) {
            throw new EntityNotFoundException($fcdn);
        }
        // Add it to to object cache.
        self::$mapping[$interface] = new $fcdn();
        return clone self::$mapping[$interface];
    }
}
