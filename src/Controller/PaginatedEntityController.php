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

use Apigee\Edge\Api\Management\Controller\OrganizationController;
use Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Exception\CpsNotEnabledException;
use Apigee\Edge\Structure\PagerInterface;

/**
 * Class PaginatedEntityController.
 *
 * @see \Apigee\Edge\Controller\PaginatedEntityControllerInterface
 */
abstract class PaginatedEntityController extends EntityController implements PaginatedEntityControllerInterface
{
    /** @var \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface */
    protected $organizationController;

    /**
     * PaginatedEntityController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $entityNormalizers
     * @param OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        ClientInterface $client,
        array $entityNormalizers = [],
        OrganizationControllerInterface $organizationController = null
    ) {
        parent::__construct($organization, $client, $entityNormalizers);
        $this->organizationController = $organizationController ?: new OrganizationController($client);
    }

    /**
     * @inheritdoc
     */
    public function createPager(int $limit = 0, ?string $startKey = null): PagerInterface
    {
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $organization */
        $organization = $this->organizationController->load($this->organization);
        if (!$organization->getPropertyValue('features.isCpsEnabled')) {
            throw new CpsNotEnabledException($this->organization);
        }

        // Create an anonymous class here because this class should not exist and be in use
        // in those controllers that do not work with entities that belongs to an organization.
        $pager = new class() implements PagerInterface {
            protected $startKey;

            protected $limit;

            /**
             * @inheritdoc
             */
            public function getStartKey(): ?string
            {
                return $this->startKey;
            }

            /**
             * @inheritdoc
             */
            public function getLimit(): int
            {
                return $this->limit;
            }

            /**
             * @inheritdoc
             */
            public function setStartKey(?string $startKey): ?string
            {
                $this->startKey = $startKey;

                return $this->startKey;
            }

            /**
             * @inheritdoc
             */
            public function setLimit(int $limit): int
            {
                $this->limit = $limit;

                return $this->limit;
            }
        };

        $pager->setLimit($limit);
        $pager->setStartKey($startKey);

        return $pager;
    }
}
