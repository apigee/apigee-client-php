<?php

/*
 * Copyright 2023 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Api\ApigeeX\Controller;

use Apigee\Edge\Api\ApigeeX\Entity\AppInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\PaginatedEntityControllerInterface;
use Apigee\Edge\Structure\PagerInterface;

/**
 * Interface AppControllerInterface.
 *
 * @see https://docs.apigee.com/api/apps-0
 */
interface AppControllerInterface extends EntityControllerInterface
{
    /**
     * Type of a developer app.
     */
    public const APP_TYPE_DEVELOPER = 'developer';

    /**
     * Type of a appgroup app.
     */
    public const APP_TYPE_COMPANY = 'company';

    /**
     * String that should be sent to the API to change the status of a
     * credential to approved.
     */
    public const STATUS_APPROVE = 'approve';

    /**
     * String that should be sent to the API to change the status of a
     * credential to revoked.
     */
    public const STATUS_REVOKE = 'revoke';

    /**
     * Loads a developer or a appgroup app from Edge based on its UUID.
     *
     * @param string $appId
     *   UUID of an app (appId).
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\AppInterface
     *   A developer- or a appgroup app entity.
     */
    public function loadAppGroup(string $appId): AppInterface;

    /**
     * Returns list of app ids from Edge.
     *
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
     *   Number of results to return.
     *
     * @return string[]
     *   An array of developer- and appgroup app ids.
     */
    public function listAppIds(PagerInterface $pager = null): array;

    /**
     * Returns list of app entities from Edge. The returned number of entities can be limited.
     *
     * @param bool $includeCredentials
     *   Whether to include consumer key and secret for each app in the response or not.
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
     *   Number of results to return.
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\AppInterface[]
     *   An array that can contain both developer- and appgroup app entities.
     */
    public function listApps(bool $includeCredentials = false, PagerInterface $pager = null): array;

    /**
     * Returns a list of app ids filtered by status from Edge.
     *
     * @param string $status
     *   App status. (Recommended way is to use App entity constants.)
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
     *   Number of results to return.
     *
     * @return string[]
     *   An array of developer- and appgroup app ids.
     */
    public function listAppIdsByStatus(string $status, PagerInterface $pager = null): array;

    /**
     * Returns a list of app entities filtered by status from Edge.
     *
     * @param string $status
     *   App status. (Recommended way is to use App entity constants.)
     * @param bool $includeCredentials
     *   Whether to include app credentials in the response or not.
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
     *   Number of results to return.
     *
     * @return \Apigee\Edge\Api\ApigeeX\Entity\AppInterface[]
     *   An array that can contain both developer- and appgroup app entities.
     */
    public function listAppsByStatus(
        string $status,
        bool $includeCredentials = true,
        PagerInterface $pager = null
    ): array;

    /**
     * Returns a list of app ids filtered by app type from Edge.
     *
     * @param string $appType
     *   Either "developer" or "appgroup".
     * @param \Apigee\Edge\Structure\PagerInterface|null $pager
     *   Number of results to return.
     *
     * @return string[]
     *   An array of developer- and appgroup app ids.
     */
    public function listAppIdsByType(string $appType, PagerInterface $pager = null): array;

}
