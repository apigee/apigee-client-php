<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Entity\StatusAwareEntityController;
use Psr\Http\Message\UriInterface;

/**
 * Class DeveloperController.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class DeveloperController extends StatusAwareEntityController implements DeveloperControllerInterface
{
    /**
     * Returns the API endpoint that the controller communicates with.
     *
     * In case of an entity that belongs to an organisation it should return organization/[orgName]/[endpoint].
     *
     * @return UriInterface
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getHttpClientBuilder()->getUriFactory()
            ->createUri(sprintf('organizations/%s/developers', $this->organization));
    }
}
