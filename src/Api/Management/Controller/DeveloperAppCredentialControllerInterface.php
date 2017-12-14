<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\AppCredentialInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\StatusAwareEntityControllerInterface;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Interface DeveloperAppCredentialControllerInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see link https://docs.apigee.com/api/developer-app-keys
 */
interface DeveloperAppCredentialControllerInterface extends
    EntityControllerInterface,
    StatusAwareEntityControllerInterface
{
    /**
     * Creates a new consumer key and secret for an app.
     *
     * @param string $consumerKey
     * @param string $consumerSecret
     *
     * @throws \Apigee\Edge\Exception\ClientErrorException
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppCredentialInterface
     *
     * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/create
     */
    public function create(string $consumerKey, string $consumerSecret): AppCredentialInterface;

    /**
     * Generates a new key pair for an app.
     *
     * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D-0
     *
     * @param string[] $apiProducts
     *   API Product names.
     * @param string[] $scopes
     *   List of OAuth scopes (from API products).
     * @param string $keyExpiresIn
     *   In milliseconds. A value of -1 means the key/secret pair never expire.
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppCredentialInterface
     */
    public function generate(
        array $apiProducts,
        array $scopes = [],
        string $keyExpiresIn = '-1'
    ): AppCredentialInterface;

    /**
     * Adds API products to a consumer key.
     *
     * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D
     *
     * Modifying attributes of a consumer key is intentionally separated because attributes can not just be added but
     * existing ones can be removed if they are missing from the payload.
     *
     * @param string $consumerKey
     *   The consumer key to modify.
     * @param string[] $apiProducts
     *   API Product names.
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppCredentialInterface
     */
    public function addProducts(string $consumerKey, array $apiProducts): AppCredentialInterface;

    /**
     * Approve or revoke specific key of a developer app.
     *
     * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D-0
     *
     * @param string $consumerKey
     * @param string $status
     */
    public function setStatus(string $consumerKey, string $status): void;

    /**
     * Approve or revoke API product for an API key.
     *
     * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D/apiproducts/%7Bapiproduct_name%7D
     *
     * @param string $consumerKey
     * @param string $apiProduct
     * @param string $status
     */
    public function setApiProductStatus(string $consumerKey, string $apiProduct, string $status): void;

    /**
     * Delete key for an developer app.
     *
     * @see https://docs.apigee.com/management/apis/delete/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D
     *
     * @param string $consumerKey
     *
     * @return \Apigee\Edge\Entity\EntityInterface
     */
    public function delete(string $consumerKey): EntityInterface;

    /**
     * Remove API product for a consumer key for an developer app.
     *
     * @see https://docs.apigee.com/management/apis/delete/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D
     *
     * @param string $consumerKey
     * @param string $apiProduct
     *
     * @return \Apigee\Edge\Entity\EntityInterface
     */
    public function deleteApiProduct(string $consumerKey, string $apiProduct): EntityInterface;

    /**
     * Get key details for a developer app.
     *
     * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D
     *
     * @param string $consumerKey
     *
     * @return \Apigee\Edge\Entity\EntityInterface
     */
    public function load(string $consumerKey);

    /**
     * Modify (override) attributes of a customer key.
     *
     * It is called override, because previous attributes can be removed if those are not included in the
     * passed $attributes variable.
     *
     * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D
     *
     * @param string $consumerKey
     *   The consumer key to modify.
     * @param \Apigee\Edge\Structure\AttributesProperty $attributes
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppCredentialInterface
     */
    public function overrideAttributes(string $consumerKey, AttributesProperty $attributes): AppCredentialInterface;

    /**
     * Modify (override) scopes of a customer key.
     *
     * It is called override, because previous scopes can be removed if those are not included in the
     * passed $scopes variable.
     *
     * @see https://docs.apigee.com/management/apis/put/organizations/{org_name}/developers/{developer_email_or_id}/apps/{app_name}/keys/{consumer_key}
     *
     * @param string $consumerKey
     *   The consumer key to modify.
     * @param string[] $scopes
     *
     * @return \Apigee\Edge\Api\Management\Entity\AppCredentialInterface
     */
    public function overrideScopes(string $consumerKey, array $scopes): AppCredentialInterface;
}
