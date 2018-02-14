<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Trait StatusPropertyAwareTrait.
 *
 *
 * @see StatusPropertyInterface
 */
trait StatusPropertyAwareTrait
{
    /** @var string */
    protected $status;

    /**
     * @inheritdoc
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set status of this entity from an Edge API response.
     *
     * The status of an entity can not be changed by modifying the value of this property. Read more about this in
     * the header of the StatusPropertyInterface.
     *
     * @param string $status
     *   Status of the entity.
     *
     * @see \Apigee\Edge\Entity\Property\StatusPropertyInterface
     *
     * @internal
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
