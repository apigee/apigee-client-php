<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Controller\AbstractEntityController;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\NonCpsListingEntityControllerTrait;
use Psr\Http\Message\UriInterface;

/**
 * Class OrganizationController.
 *
 *
 * @see https://docs.apigee.com/api/organizations-0
 */
class OrganizationController extends AbstractEntityController implements OrganizationControllerInterface
{
    use EntityCrudOperationsControllerTrait;
    use NonCpsListingEntityControllerTrait;

    /**
     * @inheritdoc
     */
    public function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri('/organizations');
    }
}
