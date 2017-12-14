<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\EntityCrudOperationsControllerInterface;
use Apigee\Edge\Controller\NonCpsListingEntityControllerInterface;
use Apigee\Edge\Controller\StatusAwareEntityControllerInterface;

/**
 * Interface DeveloperAppControllerInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see https://docs.apigee.com/api/apps-developer
 */
interface DeveloperAppControllerInterface extends
    AttributesAwareEntityControllerInterface,
    EntityControllerInterface,
    EntityCrudOperationsControllerInterface,
    NonCpsListingEntityControllerInterface,
    StatusAwareEntityControllerInterface
{
}
