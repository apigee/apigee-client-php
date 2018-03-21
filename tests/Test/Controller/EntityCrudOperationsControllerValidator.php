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

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\EntityNormalizer;
use Apigee\Edge\Exception\ApiResponseException;
use Apigee\Edge\Tests\Test\Mock\TestClientFactory;

/**
 * Class EntityCrudOperationsValidator.
 *
 * Helps in validation of all entity controllers that implements EntityCrudOperationsControllerInterface.
 *
 *
 * @see \Apigee\Edge\Entity\EntityCrudOperationsControllerInterface
 */
abstract class EntityCrudOperationsControllerValidator extends EntityControllerValidator
{
    /**
     * Returns the _same_ entity object that can be used for testing create operations.
     *
     * Always use `clone` to create a new copy from the object before you start work with it!
     *
     * @return \Apigee\Edge\Entity\EntityInterface
     */
    abstract public static function sampleDataForEntityCreate(): EntityInterface;

    /**
     * Returns the _same_ entity object that can be used for testing update operations.
     *
     * Always use `clone` to create a new copy from the object before you start work with it!
     *
     * Data should be the altered version of the returned data by static::sampleDataForEntityCreate().
     *
     * @return \Apigee\Edge\Entity\EntityInterface
     */
    abstract public static function sampleDataForEntityUpdate(): EntityInterface;

    /**
     * Validates that an empty entity object can be normalized with our custom entity normalizer.
     *
     * The assertion is a dummy one here, but this test is highly important because it ensures that entity properties
     * were properly initialized (ex.: $attributes is an AttributesProperty object and not null) and getters are
     * returning the type that they should.
     *
     * @group mock
     * @group offline
     * @small
     */
    public function testEntityCanBeNormalized(): void
    {
        $entity = static::$entityFactory->getEntityByController(static::getEntityController());
        $normalizer = new EntityNormalizer();
        $this->assertNotEmpty($normalizer->normalize($entity));
    }

    /**
     * @return string
     */
    public function testCreate()
    {
        // Data providers could not be used instead of directly calling this function, because this function would
        // require two input arguments: the entity values for creation and the expected values after entity has been
        // created. If we would use more than on data provider on this function then it would get the merged result
        // of providers as a _single_ value.
        /** @var EntityInterface $entity */
        $entity = clone static::sampleDataForEntityCreate();
        $this->getEntityController()->create($entity);
        static::$createdEntities[$entity->id()] = $entity;
        // Validate properties that values are either auto generated or we do not know in the current context.
        $this->assertEntityHasAllPropertiesSet($entity);
        $this->assertArraySubset(
            array_filter(static::$objectNormalizer->normalize(static::expectedAfterEntityCreate())),
            static::$objectNormalizer->normalize($entity)
        );

        return $entity->id();
    }

    /**
     * @depends testCreate
     *
     * @param string $entityId
     *
     * @return string
     */
    public function testLoad(string $entityId)
    {
        $entity = $this->getEntityController()->load($entityId);
        // Validate properties that values are either auto generated or we do not know in the current context.
        $this->assertEntityHasAllPropertiesSet($entity);
        $this->assertArraySubset(
            array_filter(static::$objectNormalizer->normalize(static::expectedAfterEntityCreate())),
            static::$objectNormalizer->normalize($entity)
        );

        return $entityId;
    }

    /**
     * Get the entityID from the testLoad() instead of testCreate() because load should pass before update.
     *
     * @depends testLoad
     *
     * @param string $entityId
     *
     * @return string
     */
    public function testUpdate(string $entityId)
    {
        /** @var EntityInterface $entity */
        $entity = static::sampleDataForEntityUpdate();
        call_user_func([$entity, 'set' . ucfirst(static::sampleDataForEntityUpdate()->idProperty())], $entityId);
        $this->getEntityController()->update($entity);
        // Validate properties that values are either auto generated or we do not know in the current context.
        $this->assertEntityHasAllPropertiesSet($entity);
        $entityAsArray = static::$objectNormalizer->normalize($entity);
        $changesAsArray = array_filter(static::$objectNormalizer->normalize(static::expectedAfterEntityUpdate()));
        $expectedToRemainTheSame = array_diff_key($entityAsArray, $changesAsArray);
        // Of course, this property's value will change.
        if (isset($expectedToRemainTheSame['lastModifiedAt'])) {
            unset($expectedToRemainTheSame['lastModifiedAt']);
        }
        $this->assertArraySubset(
            $changesAsArray,
            $entityAsArray
        );
        $this->assertArraySubset(
            $expectedToRemainTheSame,
            $entityAsArray
        );

        return $entityId;
    }

    public function testDelete(): void
    {
        if (TestClientFactory::isMockClient(static::$client)) {
            $this->markTestSkipped(static::$onlyOnlineClientSkipMessage);
        }
        /** @var EntityInterface $entity */
        $entity = clone static::sampleDataForEntityCreate();
        $entity->{'set' . ucfirst($entity->idProperty())}($entity->id() . '_delete');
        $this->getEntityController()->create($entity);
        static::$createdEntities[$entity->id()] = $entity;
        $this->getEntityController()->delete($entity->id());
        try {
            $this->getEntityController()->load($entity->id());
        } catch (ApiResponseException $e) {
            $this->assertContains(' does not exist', $e->getMessage());
            unset(static::$createdEntities[$entity->id()]);
        }
    }

    /**
     * Returns the expected values of an entity after it has been created.
     *
     * @return \Apigee\Edge\Entity\EntityInterface
     */
    protected static function expectedAfterEntityCreate(): EntityInterface
    {
        return clone static::sampleDataForEntityCreate();
    }

    /**
     * Returns the expected values of an entity after it has been updated.
     *
     * @return \Apigee\Edge\Entity\EntityInterface
     */
    protected static function expectedAfterEntityUpdate(): EntityInterface
    {
        return clone static::sampleDataForEntityUpdate();
    }

    /**
     * @param \Apigee\Edge\Entity\EntityInterface $entity
     */
    protected function assertEntityHasAllPropertiesSet(EntityInterface $entity): void
    {
        $ro = new \ReflectionClass(get_class($entity));
        foreach ($ro->getProperties() as $property) {
            $getter = 'get' . ucfirst($property->getName());
            if ($ro->hasMethod($getter)) {
                $this->assertObjectHasAttribute($property->getName(), $entity);
            }
        }
    }
}
