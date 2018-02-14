<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Trait EnvironmentsPropertyAwareTrait.
 */
trait EnvironmentsPropertyAwareTrait
{
    /** @var string[] */
    protected $environments = [];

    /**
     * @inheritdoc
     */
    public function getEnvironments(): array
    {
        return $this->environments;
    }

    /**
     * @param array $environments
     */
    public function setEnvironments(array $environments): void
    {
        $this->environments = $environments;
    }
}
