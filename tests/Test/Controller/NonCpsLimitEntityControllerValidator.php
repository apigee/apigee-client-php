<?php

namespace Apigee\Edge\Tests\Test\Controller;

/**
 * Class NonCpsLimitEntityControllerValidator.
 *
 * Helps in validation of all entity controllers that implements NonCpsLimitEntityControllerInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see \Apigee\Edge\Entity\NonCpsListingEntityControllerInterface
 */
abstract class NonCpsLimitEntityControllerValidator extends EntityCrudOperationsControllerValidator
{
    /**
     * @depends testCreate
     */
    public function testGetEntityIds(): void
    {
        /** @var \Apigee\Edge\Controller\NonCpsListingEntityControllerInterface $controller */
        $controller = $this->getEntityController();
        $this->assertNotEmpty($controller->getEntityIds());
    }
}
