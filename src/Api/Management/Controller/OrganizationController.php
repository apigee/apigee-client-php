<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Entity\NonCpsEntityController;
use Psr\Http\Message\UriInterface;

/**
 * Class OrganizationController.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class OrganizationController extends NonCpsEntityController implements OrganizationControllerInterface
{

    /**
     * @inheritdoc
     */
    public function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getHttpClientBuilder()->getUriFactory()->createUri('organizations');
    }
}
