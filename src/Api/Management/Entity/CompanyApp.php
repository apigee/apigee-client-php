<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Api\Management\Entity;

/**
 * Class CompanyApp.
 */
class CompanyApp extends App implements CompanyAppInterface
{
    /** @var string */
    protected $companyName;

    /**
     * @inheritdoc
     */
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    /**
     * Set company name from an Edge API response.
     *
     * @param string $companyName
     *
     * @internal
     */
    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }
}
