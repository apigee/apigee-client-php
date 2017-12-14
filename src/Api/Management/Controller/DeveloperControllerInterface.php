<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\Api\Management\Exception\DeveloperNotFoundException;
use Apigee\Edge\Entity\CpsLimitEntityControllerInterface;
use Apigee\Edge\Entity\CpsListingEntityControllerInterface;
use Apigee\Edge\Entity\EntityControllerInterface;
use Apigee\Edge\Entity\EntityCrudOperationsControllerInterface;
use Apigee\Edge\Entity\StatusAwareEntityControllerInterface;

/**
 * Interface DeveloperControllerInterface.
 *
 * Describes methods available on developers.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see https://docs.apigee.com/api/developers-0
 */
interface DeveloperControllerInterface extends
    AttributesAwareEntityControllerInterface,
    CpsLimitEntityControllerInterface,
    CpsListingEntityControllerInterface,
    EntityControllerInterface,
    EntityCrudOperationsControllerInterface,
    StatusAwareEntityControllerInterface
{
    /**
     * Get developer entity by app.
     *
     * @param string $appName
     *
     * @throws DeveloperNotFoundException
     *
     * @return DeveloperInterface
     *
     * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/developers-0
     */
    public function getDeveloperByApp(string $appName): DeveloperInterface;
}
