<?php

/*
 * Copyright 2023 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Apigee\Edge\Api\Management\Controller\AttributesAwareEntityControllerTrait;
use Apigee\Edge\Api\Management\Serializer\AppEntitySerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;

/**
 * Common parent class for appgroup and developer app controllers.
 */
abstract class AppByOwnerController extends PaginatedEntityController implements AppByOwnerControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use EntityCrudOperationsControllerTrait;
    use EntityListingControllerTrait;
    use PaginatedEntityListingControllerTrait;
    use PaginationHelperTrait;
    use StatusAwareEntityControllerTrait;

    /**
     * AppByOwnerController constructor.
     *
     * @param string $organization
     * @param ClientInterface $client
     * @param EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new AppEntitySerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }
}
