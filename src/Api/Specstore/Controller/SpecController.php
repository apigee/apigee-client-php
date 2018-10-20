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

namespace Apigee\Edge\Api\Specstore\Controller;

use Apigee\Edge\Api\Specstore\Entity\Spec;
use Apigee\Edge\Controller\EntityController;

/**
 * Class SpecController which allows for CRUD operations related to Spec
 * and also allows for upload and download of openAPI specifications
 *
 * @package Apigee\Edge\Api\Specstore\Controller
 */
class SpecController extends EntityController
{
    use SpecstoreEntityControllerTrait;

    /**
     * Upload a openAPI file to the backend
     *
     * @param Spec $entity
     * @param $content
     * @throws \Http\Client\Exception
     */
    public function uploadJsonSpec(Spec $entity, $content): void
    {
        $this->getClient()->put(
            $entity->getContent(),
            $content,
            ['Content-Type' => 'application/x-yaml']);
    }

    /**
     * Get the contents of the spec
     *
     * Always returns application/json
     *
     * @param Spec $entity
     * @return string
     * @throws \Http\Client\Exception
     */
    public function getSpecContents(Spec $entity)
    {
        $response = $this->getClient()->get($entity->getContent(), ['Accept' => 'application/json, text/plain, */*']);

        return (string)$response->getBody();
    }

    /**
     * Returns the API endpoint that the controller communicates with.
     *
     * @return \Psr\Http\Message\UriInterface
     */
    protected function getBaseEndpointUri(): \Psr\Http\Message\UriInterface
    {
        return $this->client->getUriFactory()->createUri('');
    }

    /**
     * Returns the fully-qualified class name of the entity that this controller works with.
     *
     * @return string
     */
    protected function getEntityClass(): string
    {
        return \Apigee\Edge\Api\Specstore\Entity\Spec::class;
    }

    protected function getCreateEndpointUri()
    {
        return 'specs/new';
    }
}
