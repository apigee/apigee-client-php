<?php

namespace Apigee\Edge\Tests\Test\Controller;

/**
 * Class NonCpsEntityControllerValidator.
 *
 * Helps in validation of those entity controllers that implements NonCpsEntityControllerInterface instead of
 * EntityControllerInterface.
 *
 * @package Apigee\Edge\Tests\Test\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\NonCpsEntityControllerInterface
 * @see \Apigee\Edge\Entity\EntityControllerInterface
 */
abstract class NonCpsEntityControllerValidator extends BaseEntityControllerValidator
{
    /**
     * @depends testCreate
     */
    public function testGetEntityIds()
    {
        /** @var \Apigee\Edge\Entity\NonCpsEntityControllerInterface $controller */
        $controller = $this->getEntityController();
        $this->assertNotEmpty($controller->getEntityIds());
    }
}
