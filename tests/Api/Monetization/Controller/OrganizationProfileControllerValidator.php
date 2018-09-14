<?php

/*
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Tests\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Controller\OrganizationProfileController;
use Apigee\Edge\Api\Monetization\Entity\OrganizationProfile;
use Apigee\Edge\Api\Monetization\Entity\OrganizationProfileInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Tests\Test\Controller\EntityControllerValidator;
use Apigee\Edge\Tests\Test\Controller\OrganizationAwareEntityControllerValidatorTrait;
use Apigee\Edge\Tests\Test\HttpClient\FileSystemMockClient;
use Apigee\Edge\Tests\Test\TestClientFactory;

class OrganizationProfileControllerValidator extends EntityControllerValidator
{
    use OrganizationAwareEntityControllerValidatorTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$client = (new TestClientFactory())->getClient(FileSystemMockClient::class);
    }

    public function testLoad(): OrganizationProfileInterface
    {
        $entity = $this->getEntityController()->load();
        $this->assertEquals($entity->id(), 'phpunit');
        $this->assertCount(2, $entity->getAddresses());
        $addresses = $entity->getAddresses();
        /** @var \Apigee\Edge\Api\Monetization\Structure\Address $address */
        $address = reset($addresses);
        $this->assertEquals('address1', $address->getAddress1());
        $this->assertTrue($address->isPrimary());
        $this->assertEquals(OrganizationProfile::STATUS_ACTIVE, $entity->getStatus());

        return $entity;
    }

    /**
     * @depends testLoad
     */
    public function testEntityTransformation(OrganizationProfileInterface $org): void
    {
        $client = static::$client;
        $class = new class('', $client) extends OrganizationProfileController {
            public function getEntitySerializer(): EntitySerializerInterface
            {
                return $this->entitySerializer;
            }
        };
        $output = $class->getEntitySerializer()->serialize($org, 'json');
        $obj = json_decode($output);
        $this->assertObjectHasAttribute('address', $obj);
        $this->assertObjectNotHasAttribute('addresses', $obj);
        $this->assertObjectHasAttribute('isPrimary', $obj->address[0]);
        $this->assertObjectNotHasAttribute('primary', $obj->address[0]);
    }

    /**
     * @inheritdoc
     */
    protected static function getEntityController(ClientInterface $client = null): EntityControllerInterface
    {
        static $controller;
        if (null === $client) {
            if (null === $controller) {
                $controller = new OrganizationProfileController(static::getOrganization(static::$client), static::$client);
            }

            return $controller;
        }

        return new OrganizationProfileController(static::getOrganization($client), $client);
    }
}
