<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Structure;

use Apigee\Edge\Entity\Property\StatusPropertyInterface;

/**
 * Describes a item in the list of API products included in a credential.
 */
interface CredentialProductInterface extends StatusPropertyInterface
{
    /**
     * @return string
     */
    public function getApiproduct(): string;

    /**
     * @param string $apiproduct
     */
    public function setApiproduct(string $apiproduct);
}
