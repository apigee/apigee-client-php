<?php

/*
 * Copyright 2018 Google LLC
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

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Controller\CpsLimitEntityController;

/**
 * Common parent class for company- and developer app controllers.
 */
abstract class AppByOwnerController extends CpsLimitEntityController implements AppByOwnerControllerInterface
{
    /**
     * String that should be sent to the API to change the status of an app to approved.
     */
    public const STATUS_APPROVE = 'approve';

    /**
     * String that should be sent to the API to change the status of an app to revoked.
     */
    public const STATUS_REVOKE = 'revoke';
}
