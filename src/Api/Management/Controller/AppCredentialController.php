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

use Apigee\Edge\Api\Management\Entity\AppCredential;
use Apigee\Edge\Api\Management\Entity\AppCredentialInterface;
use Apigee\Edge\Api\Management\Serializer\AppCredentialSerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Apigee\Edge\Serializer\EntitySerializerInterface;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Common implementation for company- and developer app credential controllers.
 */
abstract class AppCredentialController extends EntityController implements AppCredentialControllerInterface
{
    use AttributesAwareEntityControllerTrait;
    use StatusAwareEntityControllerTrait;

    /** @var string App name. */
    protected $appName;

    /**
     * AppCredentialController constructor.
     *
     * @param string $organization
     * @param string $appName
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Apigee\Edge\Serializer\EntitySerializerInterface|null $entitySerializer
     */
    public function __construct(string $organization, string $appName, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $this->appName = $appName;
        $entitySerializer = $entitySerializer ?? new AppCredentialSerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }

    /**
     * @inheritdoc
     */
    public function create(string $consumerKey, string $consumerSecret): AppCredentialInterface
    {
        $response = $this->client->post(
            // Just to spare some extra lines of code.
            $this->getEntityEndpointUri('create'),
            (string) json_encode((object) ['consumerKey' => $consumerKey, 'consumerSecret' => $consumerSecret])
        );

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function generate(
        array $apiProducts,
        AttributesProperty $appAttributes,
        string $callbackUrl,
        array $scopes = [],
        string $keyExpiresIn = '-1'
    ): AppCredentialInterface {
        @trigger_error('The $appAttributes parameter is deprecated in version 2.0.7 and is removed from 3.0.0.', E_USER_DEPRECATED);
        @trigger_error('The $callbackUrl parameter is deprecated in version 2.0.7 and is removed from 3.0.0.', E_USER_DEPRECATED);
        $payload_as_array = [
            'apiProducts' => $apiProducts,
            'keyExpiresIn' => $keyExpiresIn,
            'scopes' => $scopes,
        ];
        // It is highly unlikely that a downstream developer would like to
        // remove attributes or callback urls from a parent app by passing
        // empty values here. If empty argument values were passed then it
        // probably happened by accident or it was a necessary workaround just
        // to be able set key expiry.
        $attributes_as_array = $this->entitySerializer->normalize($appAttributes);
        if (!empty($attributes_as_array)) {
            $payload_as_array['attributes'] = $attributes_as_array;
        }
        if (!empty(trim($callbackUrl))) {
            $payload_as_array['callbackUrl'] = $callbackUrl;
        }
        $response = $this->client->post(
            $this->getBaseEndpointUri(),
            (string) json_encode((object) $payload_as_array)
        );
        // It returns a complete developer app entity, but we only returns the newly created credential for the
        // sake of consistency.
        $responseArray = $this->responseToArray($response);
        $credentialArray = reset($responseArray['credentials']);

        return $this->entitySerializer->denormalize(
            $credentialArray,
            $this->getEntityClass()
        );
    }

    /**
     * @inheritdoc
     */
    public function addProducts(string $consumerKey, array $apiProducts): AppCredentialInterface
    {
        $response = $this->client->post(
            $this->getEntityEndpointUri($consumerKey),
            (string) json_encode((object) ['apiProducts' => $apiProducts])
        );

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function setApiProductStatus(string $consumerKey, string $apiProduct, string $status): void
    {
        $apiProduct = rawurlencode($apiProduct);
        $uri = $this->getBaseEndpointUri()
            ->withPath("{$this->getBaseEndpointUri()}/keys/{$consumerKey}/apiproducts/{$apiProduct}")
            ->withQuery(http_build_query(['action' => $status]));
        $this->client->post($uri, null, ['Content-Type' => 'application/octet-stream']);
    }

    /**
     * @inheritdoc
     */
    public function deleteApiProduct(string $consumerKey, string $apiProduct): AppCredentialInterface
    {
        $apiProduct = rawurlencode($apiProduct);
        $uri = $this->getBaseEndpointUri()->withPath("{$this->getBaseEndpointUri()}/keys/{$consumerKey}/apiproducts/{$apiProduct}");
        $response = $this->client->delete($uri);

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function overrideScopes(string $consumerKey, array $scopes): AppCredentialInterface
    {
        $response = $this->client->put(
            $this->getEntityEndpointUri($consumerKey),
            (string) json_encode((object) ['scopes' => $scopes])
        );

        return $this->entitySerializer->deserialize(
            (string) $response->getBody(),
            $this->getEntityClass(),
            'json'
        );
    }

    /**
     * @inheritdoc
     */
    public function load(string $entityId): AppCredentialInterface
    {
        $response = $this->client->get($this->getEntityEndpointUri($entityId));

        return $this->entitySerializer->deserialize(
        (string) $response->getBody(),
        $this->getEntityClass(),
        'json'
      );
    }

    /**
     * @inheritdoc
     */
    public function delete(string $entityId): AppCredentialInterface
    {
        $response = $this->client->delete($this->getEntityEndpointUri($entityId));

        return $this->entitySerializer->deserialize(
        (string) $response->getBody(),
        $this->getEntityClass(),
        'json'
      );
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return AppCredential::class;
    }
}
