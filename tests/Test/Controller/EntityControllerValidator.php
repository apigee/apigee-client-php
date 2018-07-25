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

namespace Apigee\Edge\Tests\Test\Controller;

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Tests\Test\HttpClient\FileSystemMockClient;
use Apigee\Edge\Tests\Test\TestClientFactory;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class EntityControllerValidator.
 *
 * Base class that helps validation of entity controllers.
 */
abstract class EntityControllerValidator extends AbstractControllerValidator
{
    /** @var \Symfony\Component\Serializer\Normalizer\ObjectNormalizer */
    protected static $objectNormalizer;

    /** @var \Apigee\Edge\Entity\EntityInterface[] */
    protected static $createdEntities = [];

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$objectNormalizer = new ObjectNormalizer();
        static::$objectNormalizer->setSerializer(new Serializer([new DateTimeNormalizer('U'), static::$objectNormalizer]));
    }

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        if (TestClientFactory::isMockClient(static::$client)) {
            return;
        }

        // Remove created entities on Apigee Edge.
        try {
            foreach (static::$createdEntities as $entity) {
                static::getEntityController()->delete($entity->id());
                unset(static::$createdEntities[$entity->id()]);
            }
        } catch (\Exception $e) {
            printf(
                "Unable to delete %s entity with %s id.\n %s",
                strtolower(get_class($entity)),
                $entity->id(),
                $e->getMessage()
            );
        }
    }

    /**
     * Returns the entity controller that is being tested.
     *
     * It is recommended to use static cache on the controller instance, however it should not be added as a
     * property of a test class because it can be misleading later whether the static::$controller should be called in
     * a test method or this getter.
     *
     * @param \Apigee\Edge\ClientInterface|null $client
     *   Overrides default API client in test. Allows to switch from online to
     *   offline and vice-versa.
     *
     * @return \Apigee\Edge\Controller\EntityControllerInterface
     */
    abstract protected static function getEntityController(ClientInterface $client = null): EntityControllerInterface;

    /**
     * Returns an entity controller that uses the mock client.
     *
     * @return \Apigee\Edge\Controller\EntityControllerInterface
     */
    protected function getEntityControllerWithMockClient(): EntityControllerInterface
    {
        $factory = new TestClientFactory();

        return $this->getEntityController($factory->getClient(FileSystemMockClient::class));
    }
}
