<?php

namespace Apigee\Edge\Api\Management\Entity;

/**
 * Interface CompanyAppInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface CompanyAppInterface extends AppInterface
{
    /**
     * @return string
     */
    public function getCompanyName(): ?string;
}
