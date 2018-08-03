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

use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\EntityListingControllerTrait;
use Apigee\Edge\Controller\NonPaginatedEntityListingControllerTrait;
use Apigee\Edge\Controller\PaginatedEntityIdListingControllerTrait;
use Apigee\Edge\Controller\PaginationHelperTrait;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;

/**
 * Common parent class for company- and developer app controllers.
 */
abstract class AppByOwnerController extends EntityController implements AppByOwnerControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use AppControllerTrait;
    use EntityCrudOperationsControllerTrait;
    use EntityListingControllerTrait;
    use NonPaginatedEntityListingControllerTrait;
    use PaginatedEntityIdListingControllerTrait;
    use PaginationHelperTrait;
    use StatusAwareEntityControllerTrait;
}
