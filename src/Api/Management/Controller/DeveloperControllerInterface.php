<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\DeveloperInterface;
use Apigee\Edge\Api\Management\Exception\DeveloperNotFoundException;
use Apigee\Edge\Entity\CpsLimitEntityControllerInterface;
use Apigee\Edge\Entity\StatusAwareEntityControllerInterface;

/**
 * Interface DeveloperControllerInterface.
 *
 * Describes methods available on developers.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @link https://docs.apigee.com/api/developers-0.
 */
interface DeveloperControllerInterface extends
    AttributesAwareEntityControllerInterface,
    CpsLimitEntityControllerInterface,
    StatusAwareEntityControllerInterface
{
    /**
     * Get developer entity by app.
     *
     * @param string $appName
     *
     * @return DeveloperInterface
     * @throws DeveloperNotFoundException
     * @link https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/developers-0
     */
    public function getDeveloperByApp(string $appName): DeveloperInterface;
}
