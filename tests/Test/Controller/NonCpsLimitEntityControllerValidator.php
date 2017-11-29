<?php

namespace Apigee\Edge\Tests\Test\Controller;

/**
 * Class EntityControllerValidator.
 *
 * Helps in validation of all entity controllers that implements NonCpsLimitEntityControllerInterface.
 *
 * @package Apigee\Edge\Tests\Test\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\EntityCrudOperationsInterface
 */
abstract class NonCpsLimitEntityControllerValidator extends EntityCrudOperationsValidator
{
    /**
     * @depends testCreate
     */
    public function testGetEntityIds()
    {
        /** @var \Apigee\Edge\Entity\EntityCrudOperationsInterface $controller */
        $controller = $this->getEntityController();
        $this->assertNotEmpty($controller->getEntityIds());
    }
}
