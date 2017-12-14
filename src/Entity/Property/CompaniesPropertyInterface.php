<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface CompaniesPropertyInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
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
