<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\AppInterface;
use Apigee\Edge\Entity\CpsLimitEntityControllerInterface;
use Apigee\Edge\Entity\EntityControllerInterface;
use Apigee\Edge\Structure\CpsListLimitInterface;

/**
 * Interface AppControllerInterface.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 * @link https://docs.apigee.com/api/apps-0
 */
interface AppControllerInterface extends CpsLimitEntityControllerInterface, EntityControllerInterface
{
    /**
     * Loads a developer or a company app from Edge based on its UUID.
     *
     * @param string $appId
     *   UUID of an app (appId).
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppInterface
     *   A developer- or a company app entity.
     */
    public function loadApp(string $appId): AppInterface;

    /**
     * Returns list of app ids from Edge.
     *
     * @param \Apigee\Edge\Structure\CpsListLimitInterface|null $cpsLimit
     *   Number of results to return.
     *
     * @return string[]
     *   An array of developer- and company app ids.
     */
    public function listAppIds(CpsListLimitInterface $cpsLimit = null): array;

    /**
     * Returns list of app entities from Edge. The returned number of entities can be limited.
     *
     * @param bool $includeCredentials
     *   Whether to include consumer key and secret for each app in the response or not.
     * @param \Apigee\Edge\Structure\CpsListLimitInterface|null $cpsLimit
     *   Number of results to return.
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppInterface[]
     *   An array that can contain both developer- and company app entities.
     */
    public function listApps(bool $includeCredentials = false, CpsListLimitInterface $cpsLimit = null): array;

    /**
     * Returns a list of app ids filtered by status from Edge.
     *
     * @param string $status
     *   App status. (Recommended way is to use App entity constants.)
     * @param \Apigee\Edge\Structure\CpsListLimitInterface|null $cpsLimit
     *   Number of results to return.
     *
     * @return string[]
     *   An array of developer- and company app ids.
     */
    public function listAppIdsByStatus(string $status, CpsListLimitInterface $cpsLimit = null): array;

    /**
     * Returns a list of app entities filtered by status from Edge.
     *
     * @param string $status
     *   App status. (Recommended way is to use App entity constants.)
     * @param bool $includeCredentials
     *   Whether to include app credentials in the response or not.
     * @param \Apigee\Edge\Structure\CpsListLimitInterface|null $cpsLimit
     *   Number of results to return.
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppInterface[]
     *   An array that can contain both developer- and company app entities.
     */
    public function listAppsByStatus(
        string $status,
        bool $includeCredentials = true,
        CpsListLimitInterface $cpsLimit = null
    ): array;

    /**
     * Returns a list of app ids filtered by app type from Edge.
     *
     * @param string $appType
     *   Either "developer" or "company".
     * @param \Apigee\Edge\Structure\CpsListLimitInterface|null $cpsLimit
     *   Number of results to return.
     *
     * @return string[]
     *   An array of developer- and company app ids.
     */
    public function listAppIdsByType(string $appType, CpsListLimitInterface $cpsLimit = null): array;

    /**
     * Returns a list of app ids filtered by app family from Edge.
     *
     * @param string $appFamily
     *   App family, example: default.
     * @param \Apigee\Edge\Structure\CpsListLimitInterface|null $cpsLimit
     *   Number of results to return.
     *
     * @return string[]
     *   An array of developer- and company app ids.
     */
    public function listAppIdsByFamily(string $appFamily, CpsListLimitInterface $cpsLimit = null): array;
}
