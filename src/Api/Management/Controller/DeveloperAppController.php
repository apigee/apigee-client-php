<?php

/**
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

use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\NonCpsListingEntityControllerTrait;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\HttpClient\ClientInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class DeveloperAppController.
 */
class DeveloperAppController extends EntityController implements DeveloperAppControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use EntityCrudOperationsControllerTrait;
    use NonCpsListingEntityControllerTrait;
    use StatusAwareEntityControllerTrait;

    /** @var string Developer email or id. */
    protected $developerId;

    /**
     * DeveloperAppController constructor.
     *
     * @param string $organization
     * @param string $developerId
     * @param \Apigee\Edge\HttpClient\ClientInterface|null $client
     * @param \Apigee\Edge\Entity\EntityFactoryInterface|null $entityFactory
     */
    public function __construct(
        string $organization,
        string $developerId,
        ClientInterface $client = null,
        EntityFactoryInterface $entityFactory = null
    ) {
        $this->developerId = $developerId;
        parent::__construct($organization, $client, $entityFactory);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf('/organizations/%s/developers/%s/apps', $this->organization, $this->developerId));
    }
}
