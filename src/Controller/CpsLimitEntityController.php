<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Controller;

use Apigee\Edge\Api\Management\Controller\OrganizationController;
use Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface;
use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\Exception\CpsNotEnabledException;
use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\Structure\CpsListLimitInterface;

/**
 * Class CpsLimitEntityController.
 *
 *
 * @see \Apigee\Edge\Controller\CpsLimitEntityControllerInterface
 */
abstract class CpsLimitEntityController extends EntityController
{
    /** @var \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface */
    protected $organizationController;

    /**
     * CpsLimitEntityController constructor.
     *
     * @param string $organization
     * @param ClientInterface|null $client
     * @param EntityFactoryInterface|null $entityFactory
     * @param OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        ClientInterface $client = null,
        EntityFactoryInterface $entityFactory = null,
        OrganizationControllerInterface $organizationController = null
    ) {
        parent::__construct($organization, $client, $entityFactory);
        $this->organizationController = $organizationController ?: new OrganizationController($client, $entityFactory);
    }

    /**
     * @inheritdoc
     */
    public function createCpsLimit(string $startKey, int $limit): CpsListLimitInterface
    {
        /** @var \Apigee\Edge\Api\Management\Entity\OrganizationInterface $organization */
        $organization = $this->organizationController->load($this->organization);
        if (!$organization->getPropertyValue('features.isCpsEnabled')) {
            throw new CpsNotEnabledException($this->organization);
        }

        // Create an anonymous class here because this class should not exist and be in use
        // in those controllers that do not work with entities that belongs to an organization.
        $cpsLimit = new class() implements CpsListLimitInterface {
            protected $startKey;

            protected $limit;

            /**
             * @return string The primary key of the entity that the list should start.
             */
            public function getStartKey(): string
            {
                return $this->startKey;
            }

            /**
             * @return int Number of entities to return.
             */
            public function getLimit(): int
            {
                return $this->limit;
            }

            public function setStartKey(string $startKey): string
            {
                $this->startKey = $startKey;

                return $this->startKey;
            }

            public function setLimit(int $limit): int
            {
                $this->limit = $limit;

                return $this->limit;
            }
        };
        $cpsLimit->setStartKey($startKey);
        $cpsLimit->setLimit($limit);

        return $cpsLimit;
    }
}
