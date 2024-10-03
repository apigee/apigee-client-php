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

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Serializer\EntitySerializerInterface;

/**
 * Class EntityController.
 */
abstract class EntityController extends AbstractEntityController
{
    use OrganizationAwareControllerTrait;

    /** @var string Name of the organization that the entity belongs to. */
    protected $organization;

    /**
     * EntityController constructor.
     *
     * @param string $organization
     *   Name of the organization that the entities belongs to.
     * @param ClientInterface $client
     * @param EntitySerializerInterface|null $entitySerializer
     *
     * @psalm-suppress InvalidArgument - There is no issue with the arguments.
     */
    public function __construct(
        string $organization,
        ClientInterface $client,
        ?EntitySerializerInterface $entitySerializer = null,
    ) {
        $this->organization = $organization;
        parent::__construct($client, $entitySerializer);
    }

    public function getOrganisationName(): string
    {
        return $this->organization;
    }
}
