<?php

namespace Apigee\Edge\Structure;

use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;

/**
 * Describes a item in the list of API products included in a credential.
 *
 * @package Apigee\Edge\Structure
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class CredentialProduct implements CredentialProductInterface
{
    use StatusPropertyAwareTrait;

    /** @var string Name of the API product entity. */
    protected $apiproduct;

    /**
     * CredentialProduct constructor.
     *
     * @param string $apiproduct
     * @param string $status
     */
    public function __construct(string $apiproduct, string $status)
    {
        $this->apiproduct = $apiproduct;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getApiproduct(): string
    {
        return $this->apiproduct;
    }

    /**
     * @param string $apiproduct
     */
    public function setApiproduct(string $apiproduct)
    {
        $this->apiproduct = $apiproduct;
    }
}
