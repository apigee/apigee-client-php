<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Interface AppsPropertyInterface.
 */
interface AppsPropertyInterface
{
    /**
     * @return array App names.
     */
    public function getApps(): array;

    /**
     * @param string $appName App name.
     *
     * @return bool
     */
    public function hasApp(string $appName): bool;
}
