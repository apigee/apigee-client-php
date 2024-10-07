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

namespace Apigee\Edge\Tests\Test\Utility;

use Apigee\Edge\Api\Management\Controller\ApiProductControllerInterface;
use Apigee\Edge\Controller\EntityDeleteOperationControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Tests\Test\HttpClient\Exception\MockHttpClientException;
use Exception;
use InvalidArgumentException;
use ReflectionObject;

/**
 * Stores created test entities meanwhile a test run.
 *
 * Created test entities should be purged after a test has finished if an online
 * client was in use.
 */
final class EntityStorage
{
    /**
     * @var self
     */
    private static $instance;

    /** @var object[] */
    private static $controllers = [];

    /** @var \Apigee\Edge\Entity\EntityInterface[] */
    private static $createdEntities = [];

    /**
     * EntityStorage constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return EntityStorage
     */
    public static function getInstance(): EntityStorage
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param EntityInterface $entity
     * @param object $controller
     */
    public function addEntity(EntityInterface $entity, $controller): void
    {
        if (!is_object($controller)) {
            throw new InvalidArgumentException('Controller must be an object.');
        }
        $controllerId = $this->getControllerId($controller);
        if (!array_key_exists($controllerId, self::$controllers)) {
            self::$controllers[$controllerId] = $controller;
        }
        self::$createdEntities[$controllerId][$entity->id()] = $entity;
    }

    /**
     * @param string $entityId
     * @param object $controller
     */
    public function removeEntity(string $entityId, $controller): void
    {
        if (!is_object($controller)) {
            throw new InvalidArgumentException('Controller must be an object.');
        }
        $controllerId = $this->getControllerId($controller);
        unset(self::$createdEntities[$controllerId][$entityId]);
        // Little memory saving.
        if (empty(self::$createdEntities[$controllerId])) {
            unset(self::$controllers[$controllerId]);
        }
    }

    /**
     * @param object $controller
     *
     * @return \Apigee\Edge\Entity\EntityInterface[]
     */
    public function getCreatedEntitiesByStorage($controller): array
    {
        if (!is_object($controller)) {
            throw new InvalidArgumentException('Controller must be an object.');
        }

        return self::$createdEntities[$this->getControllerId($controller)]['entity'] ?? [];
    }

    /**
     * Removes created entities if an online client were being used by a
     * controller.
     */
    public function purgeCreatedEntities(): void
    {
        // API products must be deleted the latest because they can not be
        // deleted until there is one developer/company app that uses them.
        uasort(static::$controllers, function ($a, $b) {
            if (!$a instanceof ApiProductControllerInterface && !$b instanceof ApiProductControllerInterface) {
                return 0;
            }

            return $a instanceof ApiProductControllerInterface ? 1 : -1;
        });
        foreach (static::$controllers as $controllerId => $controller) {
            if ($controller instanceof EntityDeleteOperationControllerInterface) {
                /** @var EntityInterface $entity */
                foreach (self::$createdEntities[$controllerId] as $entity) {
                    try {
                        $controller->delete($entity->id());
                    } catch (MockHttpClientException $e) {
                        // Nothing to do, if an offline client was being used
                        // there is no need for a clean up.
                    } catch (Exception $e) {
                        // Do not fail test if the removal of a test entity
                        // failed but log the failed attempt.
                        // It could happen that an entity has been already
                        // removed (ex.: if a developer gets removed then all
                        // apps that s/he owned removed as well.)
                        if (false === strpos($e->getMessage(), 'does not exist')) {
                            printf("Unable to delete %s entity with %s id.  %s\n", strtolower(get_class($entity)), $entity->id(), $e->getMessage());
                        }
                    }
                }
                $this->resetStorageByController($controller);
            }
        }
    }

    /**
     * @param object $controller
     */
    private function resetStorageByController($controller): void
    {
        if (!is_object($controller)) {
            throw new InvalidArgumentException('Controller must be an object.');
        }
        $controllerId = $this->getControllerId($controller);
        unset(self::$createdEntities[$controllerId]);
        unset(self::$controllers[$controllerId]);
    }

    /**
     * @param object $controller
     *
     * @return string
     */
    private function getControllerId($controller): string
    {
        if (!is_object($controller)) {
            throw new InvalidArgumentException('Controller must be an object.');
        }

        $ro = new ReflectionObject($controller);

        return $ro->getName() . '-' . spl_object_hash($controller);
    }
}
