<?php

namespace Apigee\Edge\Tests\Api\Management\Controller;

use Apigee\Edge\Api\Management\Controller\OrganizationController;
use Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface;
use Apigee\Edge\Api\Management\Entity\OrganizationInterface;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class OrganizationControllerTest.
 *
 * This test only covers the "Get Organization" API call, because that is the
 * only one which is available in Apigee Edge Cloud. Also other API calls,
 * like delete and create, should not be used for organization operations,
 * because these operations usually require extra configurations that can
 * not be solved by Management API calls.
 *
 * @see https://docs.apigee.com/api-services/latest/creating-organization-environment-and-virtual-host
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @group controller
 * @group offline
 * @group small
 */
class OrganizationControllerTest extends TestCase
{
    /** @var \Apigee\Edge\HttpClient\ClientInterface */
    protected static $client;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass(): void
    {
        // We always use the offline client for this test, because on
        // Apigee Edge Cloud we do not have permission to create
        // an organization.
        // https://docs.apigee.com/management/apis/post/organizations
        static::$client = (new TestClientFactory())->getClient('\Apigee\Edge\Tests\Test\Mock\FileSystemMockClient');
        parent::setUpBeforeClass();
    }

    public function testLoad(): void
    {
        /** @var OrganizationInterface $entity */
        $entity = static::getEntityController()->load('phpunit');
        $this->assertEquals('PHPUnit', $entity->getDisplayName());
        $this->assertEquals(['prod', 'test'], $entity->getEnvironments());
        $this->assertTrue($entity->hasProperty('self.service.virtual.host.enabled'));
        $this->assertEquals('true', $entity->getPropertyValue('features.isCpsEnabled'));
        $this->assertEquals('trial', $entity->getType());
        $this->assertEquals(new \DateTimeImmutable('@648345600'), $entity->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('@648345600'), $entity->getLastModifiedAt());
        $this->assertEquals('phpunit@example.com', $entity->getCreatedBy());
        $this->assertEquals('phpunit@example.com', $entity->getLastModifiedBy());
    }

    /**
     * Returns the controller that is being tested.
     */
    protected static function getEntityController(): OrganizationControllerInterface
    {
        static $controller;
        if (!$controller) {
            $controller = new OrganizationController(static::$client);
        }

        return $controller;
    }
}
