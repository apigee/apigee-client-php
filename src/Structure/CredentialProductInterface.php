<?php

/**
 * @file
 * CredentialProductInterface.php
 */

namespace Apigee\Edge\Structure;

use Apigee\Edge\Entity\Property\StatusPropertyInterface;

/**
 * Describes a item in the list of API products included in a credential.
 *
 * @package Apigee\Edge\Structure
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface CredentialProductInterface extends StatusPropertyInterface
{
    /**
     * @return string
     */
    public function getApiproduct(): string;

    /**
     * @param string $apiproduct
     */
    public function setApiproduct(string $apiproduct);
}
