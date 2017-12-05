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

    /**
     * Status of an approved api product in ann app credential returned by Edge.
     *
     * The status that you should send to the API to change status of an api product in an app credential is in the
     * controller!
     * @see \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController.
     */
    public const STATUS_APPROVED = 'approved';

    /**
     * Status of a revoked api product in ann app credential returned by Edge.
     *
     * The status that you should send to the API to change status of an api product in an app credential is in the
     * controller!
     * @see \Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController.
     */
    public const STATUS_REVOKED = 'revoked';

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
