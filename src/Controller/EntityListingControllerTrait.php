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

namespace Apigee\Edge\Controller;

/**
 * Trait EntityListingControllerTrait.
 */
trait EntityListingControllerTrait
{
    /**
     * Parse an API response array to array of entity objects.
     *
     * @param array $responseArray
     *   API response as an array without the entity type key, ex.: developer,
     *   apiproduct, etc.
     * @param string $keyGetter
     *   Getter method on the entity that should be used as array key. Default
     *   is id().
     *
     * @return \Apigee\Edge\Entity\EntityInterface[]
     *   Array of entity objects.
     */
    protected function responseArrayToArrayOfEntities(array $responseArray, string $keyGetter = 'id'): array
    {
        $entities = [];

        foreach ($responseArray as $item) {
            /** @var \Apigee\Edge\Entity\EntityInterface $tmp */
            $tmp = $this->entityTransformer->denormalize($item,
                $this->getEntityClass());
            $entities[$tmp->{$keyGetter}()] = $tmp;
        }

        return $entities;
    }
}
