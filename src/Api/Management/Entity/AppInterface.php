<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Entity\Property\DescriptionPropertyInterface;
use Apigee\Edge\Entity\Property\DisplayNamePropertyInterface;
use Apigee\Edge\Entity\Property\NamePropertyInterface;

/**
 * Interface AppInterface.
 *
 * @package Apigee\Edge\Api\Management\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface AppInterface extends
    EntityInterface,
    AttributesPropertyInterface,
    CommonEntityPropertiesInterface,
    DescriptionPropertyInterface,
    DisplayNamePropertyInterface,
    NamePropertyInterface
{
    /**
     * Get OAuth scopes.
     *
     * Scopes of app can not be modified on the entity level therefore we could not extend the ScopesPropertyInterface
     * here.
     *
     * @return string[]
     */
    public function getScopes(): array;

    /**
     * @return string
     */
    public function getAppFamily(): ?string;

    /**
     * @param string $appFamily
     */
    public function setAppFamily(string $appFamily);

    /**
     * @return string
     */
    public function getAppId(): ?string;

    /**
     * @return string
     */
    public function getCallbackUrl(): ?string;

    /**
     * @param string $callbackUrl
     */
    public function setCallbackUrl(string $callbackUrl);

    /**
     * @return \Apigee\Edge\Api\Management\Entity\AppCredential[]
     */
    public function getCredentials(): array;
}
