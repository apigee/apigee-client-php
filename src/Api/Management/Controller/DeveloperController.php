<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\Api\Management\Exception\DeveloperNotFoundException;
use Apigee\Edge\Controller\CpsLimitEntityController;
use Apigee\Edge\Controller\CpsListingEntityControllerTrait;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Psr\Http\Message\UriInterface;

/**
 * Class DeveloperController.
 */
class DeveloperController extends CpsLimitEntityController implements DeveloperControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use CpsListingEntityControllerTrait;
    use EntityCrudOperationsControllerTrait;
    use StatusAwareEntityControllerTrait;

    /**
     * @inheritdoc
     */
    public function getDeveloperByApp(string $appName): DeveloperInterface
    {
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query(['app' => $appName]));
        $responseArray = $this->responseToArray($this->client->get($uri));
        // When developer has not found by app we are still getting back HTTP 200 with an empty developer array.
        if (empty($responseArray['developer'])) {
            throw new DeveloperNotFoundException(
                $this->client->getJournal()->getLastResponse(),
                $this->client->getJournal()->getLastRequest()
            );
        }
        $values = reset($responseArray['developer']);

        return $this->entitySerializer->denormalize($values, $this->entityFactory->getEntityByController($this));
    }

    /**
     * Returns the API endpoint that the controller communicates with.
     *
     * In case of an entity that belongs to an organisation it should return organization/[orgName]/[endpoint].
     *
     * @return UriInterface
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf('/organizations/%s/developers', $this->organization));
    }
}
