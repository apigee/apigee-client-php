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

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\AppDenormalizer;
use Apigee\Edge\Controller\CpsLimitEntityController;
use Psr\Http\Message\UriInterface;

/**
 * Class CompanyAppController.
 *
 * TODO Finalize this.
 */
class CompanyAppController extends CpsLimitEntityController implements CompanyAppControllerInterface
{
    /**
     * Name of an company.
     *
     * @var string
     */
    protected $companyName;

    /**
     * @inheritdoc
     */
    protected function entityNormalizers()
    {
        // Add our special AppDenormalizer to the top of the list.
        // This way enforce parent $this->entitySerializer calls to use it for apps primarily.
        return array_merge([new AppDenormalizer($this->entityFactory)], parent::entityNormalizers());
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf('/organizations/%s/companies/%s/apps', $this->organization, $this->companyName));
    }
}
