<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Entity\AbstractEntityController;
use Apigee\Edge\Entity\EntityCrudOperationsTrait;
use Apigee\Edge\Entity\NonCpsLimitEntityControllerTrait;
use Psr\Http\Message\UriInterface;

/**
 * Class OrganizationController.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @link https://docs.apigee.com/api/organizations-0
 */
class OrganizationController extends AbstractEntityController implements OrganizationControllerInterface
{
    use EntityCrudOperationsTrait;
    use NonCpsLimitEntityControllerTrait;

    /**
     * @inheritdoc
     */
    public function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri('/organizations');
    }
}
