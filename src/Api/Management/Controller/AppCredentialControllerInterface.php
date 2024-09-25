<?php

/*
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\AppCredentialInterface;
use Apigee\Edge\Controller\EntityControllerInterface;
use Apigee\Edge\Controller\StatusAwareEntityControllerInterface;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Describes common operations for company- and developer app credentials.
 */
interface AppCredentialControllerInterface extends
    AttributesAwareEntityControllerInterface,
    EntityControllerInterface,
    StatusAwareEntityControllerInterface
{
    /**
     * String that should be sent to the API to change the status of a credential to approved.
     */
    public const STATUS_APPROVE = 'approve';

    /**
     * String that should be sent to the API to change the status of a credential to revoked.
     */
    public const STATUS_REVOKE = 'revoke';

    /**
     * Creates a new consumer key and secret for an app.
     *
     * @param string $consumerKey
     * @param string $consumerSecret
     *
     * @throws \Apigee\Edge\Exception\ClientErrorException
     *
     * @return AppCredentialInterface
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
     * @param AttributesProperty $appAttributes
     *   Current attributes of the app. "In this API call, be sure to include any existing app attributes.
     *   If you don't, the existing attributes are deleted."
     * @param string $callbackUrl
     *   Current callback url of the app. (If you don't include it then the existing callback url gets deleted.)
     * @param string[] $scopes
     *   List of OAuth scopes (from API products).
     * @param string $keyExpiresIn
     *   In milliseconds. A value of -1 means the key/secret pair never expire.
     *
     * @return AppCredentialInterface
     */
    public function generate(
        array $apiProducts,
        AttributesProperty $appAttributes,
        string $callbackUrl,
        array $scopes = [],
        string $keyExpiresIn = '-1',
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
     * @return AppCredentialInterface
     */
    public function addProducts(string $consumerKey, array $apiProducts): AppCredentialInterface;

    /**
     * Approve or revoke specific key of a developer app.
     *
     * @see https://docs.apigee.com/management/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D-0
     *
     * @param string $entityId
     *   The consumer key
     * @param string $status
     */
    public function setStatus(string $entityId, string $status): void;

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
     * @return AppCredentialInterface
     */
    public function delete(string $consumerKey): AppCredentialInterface;

    /**
     * Remove API product for a consumer key for an developer app.
     *
     * @see https://docs.apigee.com/management/apis/delete/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D
     *
     * @param string $consumerKey
     * @param string $apiProduct
     *
     * @return AppCredentialInterface
     */
    public function deleteApiProduct(string $consumerKey, string $apiProduct): AppCredentialInterface;

    /**
     * Get key details for a developer app.
     *
     * @see https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/developers/%7Bdeveloper_email_or_id%7D/apps/%7Bapp_name%7D/keys/%7Bconsumer_key%7D
     *
     * @param string $consumerKey
     *
     * @return AppCredentialInterface
     */
    public function load(string $consumerKey): AppCredentialInterface;

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
     * @return AppCredentialInterface
     */
    public function overrideScopes(string $consumerKey, array $scopes): AppCredentialInterface;
}
