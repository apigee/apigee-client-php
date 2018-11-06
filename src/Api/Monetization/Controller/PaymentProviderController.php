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

use Apigee\Edge\Api\Monetization\Entity\PaymentProvider;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Psr\Http\Message\UriInterface;

class PaymentProviderController extends OrganizationAwareEntityController implements PaymentProviderControllerInterface
{
    use EntityCrudOperationsControllerTrait;
    use EntityListingControllerAwareTrait;
    use EntityListingControllerTrait;
    use ListingHelperTrait;

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri("/mint/organizations/{$this->organization}/providers");
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return PaymentProvider::class;
    }
}
