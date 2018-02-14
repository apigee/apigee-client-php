<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\Property\DeveloperIdPropertyInterface;

/**
 * Interface DeveloperAppInterface.
 */
interface DeveloperAppInterface extends
    AppInterface,
    DeveloperIdPropertyInterface
{
}
