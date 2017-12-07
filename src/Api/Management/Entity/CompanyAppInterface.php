<?php

namespace Apigee\Edge\Api\Management\Entity;

/**
 * Interface CompanyAppInterface.
 *
 * @package Apigee\Edge\Api\Management\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface CompanyAppInterface extends
    AppInterface
{
    /**
     * @return string
     */
    public function getCompanyName(): ?string;
}
