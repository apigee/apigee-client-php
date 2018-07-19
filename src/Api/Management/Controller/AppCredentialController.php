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
use Apigee\Edge\Api\Management\Normalizer\AppCredentialNormalizer;
use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Apigee\Edge\Denormalizer\AttributesPropertyDenormalizer;
use Apigee\Edge\Denormalizer\CredentialProductDenormalizer;
use Apigee\Edge\Normalizer\CredentialProductNormalizer;
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

    public function __construct(string $organization, string $appName, \Apigee\Edge\ClientInterface $client, $entityNormalizers = [])
    {
        $this->appName = $appName;
        $entityNormalizers[] = new AppCredentialNormalizer();
        $entityNormalizers[] = new CredentialProductDenormalizer();
        $entityNormalizers[] = new CredentialProductNormalizer();
        $entityNormalizers[] = new AttributesPropertyDenormalizer();
        parent::__construct($organization, $client, $entityNormalizers);
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

        return $this->entityTransformer->deserialize(
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
        $response = $this->client->post(
            $this->getBaseEndpointUri(),
            (string) json_encode((object) [
                'apiProducts' => $apiProducts,
                'attributes' => $this->entityTransformer->normalize($appAttributes),
                'callbackUrl' => $callbackUrl,
                'keyExpiresIn' => $keyExpiresIn,
                'scopes' => $scopes,
            ])
        );
        // It returns a complete developer app entity, but we only returns the newly created credential for the
        // sake of consistency.
        $responseArray = $this->responseToArray($response);
        $credentialArray = reset($responseArray['credentials']);

        return $this->entityTransformer->denormalize(
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

        return $this->entityTransformer->deserialize(
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
        $uri = $this->getBaseEndpointUri()->withPath("{$this->getBaseEndpointUri()}/keys/{$consumerKey}/apiproducts/{$apiProduct}");
        $response = $this->client->delete($uri);

        return $this->entityTransformer->deserialize(
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

        return $this->entityTransformer->deserialize(
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

        return $this->entityTransformer->deserialize(
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

        return $this->entityTransformer->deserialize(
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
