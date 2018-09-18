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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Entity\LegalEntityInterface;
use Apigee\Edge\Api\Monetization\Serializer\LegalEntitySerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;

/**
 * Parent class for listing developers and companies in Monetization.
 */
abstract class LegalEntityController extends OrganizationAwareEntityController implements LegalEntityControllerInterface
{
    use EntityListingControllerTrait;
    use EntityLoadOperationControllerTrait;
    use PaginatedEntityListingControllerAwareTrait;
    use PaginatedListingHelperTrait;

    /**
     * LegalEntityController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new LegalEntitySerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return LegalEntityInterface::class;
    }
}
