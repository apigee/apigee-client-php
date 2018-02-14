<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Interface EnvironmentsPropertyInterface.
 */
interface EnvironmentsPropertyInterface
{
    /**
     * @return string[]
     */
    public function getEnvironments(): array;

    /**
     * @param string[] $environments
     */
    public function setEnvironments(array $environments): void;
}
