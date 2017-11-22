<?php

namespace Apigee\Edge\Tests\Test\Controller;

/**
 * Class EntityControllerValidator.
 *
 * Helps in validation of all entity controllers that implements EntityControllerInterface.
 *
 * @package Apigee\Edge\Tests\Test\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see \Apigee\Edge\Entity\EntityControllerInterface
 */
abstract class EntityControllerValidator extends BaseEntityControllerValidator
{
    protected const DEFAULT_ORGANIZATION = 'phpunit';

    /** @var string */
    protected static $organization;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::$organization = getenv('APIGEE_PHP_SDK_ORGANIZATION') ?: self::DEFAULT_ORGANIZATION;
        parent::setUpBeforeClass();
    }

    /**
     * @depends testCreate
     */
    public function testGetEntityIds()
    {
        /** @var \Apigee\Edge\Entity\EntityControllerInterface $controller */
        $controller = $this->getEntityController();
        $this->assertNotEmpty($controller->getEntityIds());
    }
}
