<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Entity\Property\ScopesPropertyInterface;
use Apigee\Edge\Entity\Property\StatusPropertyInterface;

/**
 * Interface AppCredentialInterface.
 */
interface AppCredentialInterface extends
    EntityInterface,
    AttributesPropertyInterface,
    ScopesPropertyInterface,
    StatusPropertyInterface
{
    /**
     * Get list of API products included in this credential with their statuses.
     *
     * @return \Apigee\Edge\Structure\CredentialProduct[]
     */
    public function getApiProducts(): array;

    /**
     * @return string
     */
    public function getConsumerKey(): string;

    /**
     * @return string
     */
    public function getConsumerSecret(): string;

    /**
     * @return null|\DateTimeImmutable
     */
    public function getExpiresAt(): ?\DateTimeImmutable;

    /**
     * @return null|\DateTimeImmutable
     */
    public function getIssuedAt(): ?\DateTimeImmutable;
}
