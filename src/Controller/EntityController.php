<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\HttpClient\ClientInterface;

/**
 * Class EntityController.
 */
abstract class EntityController extends AbstractEntityController
{
    use OrganizationAwareControllerTrait;

    /**
     * EntityController constructor.
     *
     * @param string $organization
     *   Name of the organization that the entities belongs to.
     * @param ClientInterface|null $client
     * @param \Apigee\Edge\Entity\EntityFactoryInterface|null $entityFactory
     */
    public function __construct(
        string $organization,
        ClientInterface $client = null,
        EntityFactoryInterface $entityFactory = null
    ) {
        $this->organization = $organization;
        parent::__construct($client, $entityFactory);
    }
}
