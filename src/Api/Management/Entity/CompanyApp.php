<?php

namespace Apigee\Edge\Api\Management\Entity;

/**
 * Class CompanyApp.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
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
