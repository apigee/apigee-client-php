<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Entity\EntityControllerInterface;
use Apigee\Edge\Entity\EntityCrudOperationsControllerInterface;
use Apigee\Edge\Entity\NonCpsListingEntityControllerInterface;
use Apigee\Edge\Entity\StatusAwareEntityControllerInterface;

/**
 * Interface DeveloperAppControllerInterface.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @link https://docs.apigee.com/api/apps-developer
 */
interface DeveloperAppControllerInterface extends
    AttributesAwareEntityControllerInterface,
    EntityControllerInterface,
    EntityCrudOperationsControllerInterface,
    NonCpsListingEntityControllerInterface,
    StatusAwareEntityControllerInterface
{

}
