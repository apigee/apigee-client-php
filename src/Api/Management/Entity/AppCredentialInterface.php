<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Entity\Property\ScopesPropertyInterface;
use Apigee\Edge\Entity\Property\StatusPropertyInterface;

/**
 * Interface AppCredentialInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface AppCredentialInterface extends
    EntityInterface,
    AttributesPropertyInterface,
    ScopesPropertyInterface,
    StatusPropertyInterface
{
    /**
     * Get list of API products included in this credential with their statuses.
     *
     * @return \Apigee\Edge\Structure\CredentialProduct[]
     */
    public function getApiProducts(): array;

    /**
     * @return string
     */
    public function getConsumerKey(): string;

    /**
     * @return string
     */
    public function getConsumerSecret(): string;

    /**
     * @return string
     */
    public function getExpiresAt(): string;

    /**
     * @return string
     */
    public function getIssuedAt(): string;
}
