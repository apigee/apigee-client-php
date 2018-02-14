<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Entity\Property;

/**
 * Interface CompaniesPropertyInterface.
 */
interface CompaniesPropertyInterface
{
    /**
     * @return string[] Company names that this entity belongs.
     */
    public function getCompanies(): array;

    /**
     * @param string $companyName Company name
     *
     * @return bool
     */
    public function hasCompany(string $companyName): bool;
}
