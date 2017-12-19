<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesAwareTrait;
use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\AttributesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\ScopesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Class App.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
abstract class App extends Entity implements AppInterface
{
    use AttributesPropertyAwareTrait;
    use CommonEntityPropertiesAwareTrait;
    use NamePropertyAwareTrait;
    use StatusPropertyAwareTrait;
    use ScopesPropertyAwareTrait {
        // App entities should have only internal setter methods therefore we had to alias this one.
        setScopes as private privateSetScopes;
    }

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REVOKED = 'revoked';

    /**
     * We had to hard-code "default" here as default value because this property does not always returned by Edge.
     * Ex.: \Apigee\Edge\Controller\EntityCrudOperationsControllerInterface::create() does not return this for
     * a Developer app.
     *
     * @var string
     */
    protected $appFamily = 'default';

    /** @var string UUID */
    protected $appId;

    /** @var string Url, used for "three-legged" OAuth grant type flows. */
    protected $callbackUrl;

    /** @var \Apigee\Edge\Api\Management\Entity\AppCredential[] */
    protected $credentials = [];

    /**
     * App constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->attributes = new AttributesProperty();
        parent::__construct($values);
    }

    /**
     * @inheritdoc
     */
    public function idProperty(): string
    {
        // Even though the 'appId' should be the primary id field of an app, the majority of the Edge endpoints are
        // expecting the app name as a parameter. Ex.: All C.R.U.D. operations on developer apps.
        return parent::idProperty();
    }

    /**
     * Apps does not have description property as other entities, but they have an attribute that could contains it.
     *
     * Just like on the Management UI, we simulates the existence of this property in this SDK as well.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->getAttributeValue('Notes');
    }

    /**
     * Set the description of the app.
     *
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->setAttribute('Notes', $description);
    }

    /**
     * Apps does not have displayName property as other entities, but they have an attribute that could contains it.
     *
     * Just like on the Management UI, we simulates the existence of this property in this SDK as well.
     *
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        return $this->getAttributeValue('DisplayName');
    }

    /**
     * Set the display name of the app.
     *
     * @param string $displayName
     */
    public function setDisplayName(string $displayName): void
    {
        $this->setAttribute('DisplayName', $displayName);
    }

    /**
     * @inheritdoc
     */
    public function getAppFamily(): string
    {
        return $this->appFamily;
    }

    /**
     * @inheritdoc
     */
    public function setAppFamily(string $appFamily): void
    {
        $this->appFamily = $appFamily;
    }

    /**
     * @inheritdoc
     */
    public function getAppId(): ?string
    {
        return $this->appId;
    }

    /**
     * Set app id from an Edge API response.
     *
     * App id is an auto-generated value, it can not be changed.
     *
     * @param string $appId
     *
     * @internal
     */
    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @inheritdoc
     */
    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    /**
     * @inheritdoc
     */
    public function setCallbackUrl(string $callbackUrl): void
    {
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * @inheritdoc
     */
    public function getCredentials(): array
    {
        return $this->credentials;
    }

    /**
     * Set credentials from an Edge API response.
     *
     * Credentials, included in app, can not be changed by modifying them on the entity level.
     *
     * @param \Apigee\Edge\Api\Management\Entity\AppCredential[] $credentials
     *
     * @internal
     */
    public function setCredentials(array $credentials): void
    {
        $this->credentials = $credentials;
    }

    /**
     * Set OAuth scopes from an Edge API response.
     *
     * Scopes of an app should not be changed on the entity level. You should modify them by using the app credential
     * controllers.
     *
     * @param string[] $scopes
     *
     * @internal
     */
    public function setScopes(array $scopes): void
    {
        $this->privateSetScopes($scopes);
    }
}
