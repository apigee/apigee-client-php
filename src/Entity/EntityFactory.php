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

namespace Apigee\Edge\Entity;

use Apigee\Edge\Utility\ClassMatcher;

/**
 * Class EntityFactory.
 */
final class EntityFactory implements EntityFactoryInterface
{
    /**
     * Stores mapping of entity classes by controllers.
     *
     * @var string[]
     */
    private static $classMappingCache = [
        // Special handling of DeveloperAppCredentialController and CompanyAppCredentialController entity controllers,
        // because those uses the same entity, AppCredential.
        'Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController' => 'Apigee\Edge\Api\Management\Entity\AppCredential',
        'Apigee\Edge\Api\Management\Controller\CompanyAppCredentialController' => 'Apigee\Edge\Api\Management\Entity\AppCredential',
    ];

    /**
     * Entity object cache.
     *
     * @var EntityInterface[]
     */
    private static $objectCache = [];

    /**
     * @inheritdoc
     */
    public function getEntityTypeByController($entityController): string
    {
        $fqcn = $this->getClassName($entityController);
        // Try to find it in the static cache first.
        if (!isset(static::$classMappingCache[$fqcn])) {
            static::$classMappingCache[$fqcn] = ClassMatcher::getClass($entityController, ['Controller' => 'Entity'], ['Controller' => '']);
        }

        return static::$classMappingCache[$fqcn];
    }

    /**
     * @inheritdoc
     */
    public function getEntityByController($entityController): EntityInterface
    {
        $className = $this->getClassName($entityController);
        $fqcn = $this->getEntityTypeByController($entityController);
        // Add it to to object cache.
        static::$objectCache[$className] = new $fqcn();

        return clone static::$objectCache[$className];
    }

    /**
     * Helper function that returns the FQCN of a class.
     *
     * @param string|\Apigee\Edge\Controller\AbstractEntityController $entityController
     *   Fully qualified class name or an object.
     *
     * @return string
     *   Fully qualified class name.
     */
    private function getClassName($entityController): string
    {
        return is_object($entityController) ? get_class($entityController) : $entityController;
    }
}
