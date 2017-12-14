<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Entity\AbstractEntityController;
use Apigee\Edge\Entity\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Entity\NonCpsListingEntityControllerTrait;
use Psr\Http\Message\UriInterface;

/**
 * Class OrganizationController.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
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
