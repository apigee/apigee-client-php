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

namespace Apigee\Edge\Api\Docstore\Serializer;

use Apigee\Edge\Api\Docstore\Denormalizer\CollectionDenormalizer;
use Apigee\Edge\Api\Docstore\Denormalizer\DocstoreDateDenormalizer;
use Apigee\Edge\Api\Docstore\Entity\DocstoreEntityInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Serializer\EntitySerializer;
use Psr\Http\Message\ResponseInterface;

class DocstoreSerializer extends EntitySerializer
{
    /**
     * DocstoreSerializer constructor.
     *
     * @param array $normalizers
     */
    public function __construct(array $normalizers = [])
    {
        $normalizers = array_merge($normalizers, [
            new CollectionDenormalizer(),
            new DocstoreDateDenormalizer(),
        ]);

        parent::__construct($normalizers);
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return parent::denormalize($data, $class, $format, $context);
    }

    public function setPropertiesFromResponse(ResponseInterface $response, EntityInterface $entity): void
    {
        parent::setPropertiesFromResponse($response, $entity);
        if (($entity instanceof DocstoreEntityInterface) && !empty($response->getHeader('ETag'))) {
            $entity->setEtag($response->getHeader('ETag')[0]);
        }
    }
}
