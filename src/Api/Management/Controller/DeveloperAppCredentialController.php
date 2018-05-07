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
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityController;
use Apigee\Edge\Controller\EntityCrudOperationsControllerTrait;
use Apigee\Edge\Controller\StatusAwareEntityControllerTrait;
use Apigee\Edge\Denormalizer\AttributesPropertyDenormalizer;
use Apigee\Edge\Denormalizer\CredentialProductDenormalizer;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Normalizer\CredentialProductNormalizer;
use Apigee\Edge\Normalizer\KeyValueMapNormalizer;
use Apigee\Edge\Structure\AttributesProperty;
use Psr\Http\Message\UriInterface;

/**
 * Class DeveloperAppCredentialController.
 */
class DeveloperAppCredentialController extends EntityController implements DeveloperAppCredentialControllerInterface
{
    use EntityCrudOperationsControllerTrait {
        // These methods are not supported on this endpoint in the same way as on the others so do not allow to
        // use them here.
        create as private privateCreate;
        update as private privateUpdate;
    }
    use StatusAwareEntityControllerTrait;

    /**
     * String that should be sent to the API to change the status of a credential to approved.
     */
    public const STATUS_APPROVE = 'approve';

    /**
     * String that should be sent to the API to change the status of a credential to revoked.
     */
    public const STATUS_REVOKE = 'revoke';

    /** @var string Developer email or id. */
    protected $developerId;

    /** @var string App name. */
    protected $appName;

    /**
     * DeveloperAppCredentialController constructor.
     *
     * @param string $organization
     * @param string $developerId
     * @param string $appName
     * @param \Apigee\Edge\ClientInterface $client
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $entityNormalizers
     */
    public function __construct(
        string $organization,
        string $developerId,
        string $appName,
        ClientInterface $client,
        array $entityNormalizers = []
    ) {
        $this->developerId = $developerId;
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
        array $scopes = [],
        string $keyExpiresIn = '-1'
    ): AppCredentialInterface {
        $response = $this->client->post(
            $this->getBaseEndpointUri(),
            (string) json_encode((object) [
                'apiProducts' => $apiProducts,
                'scopes' => $scopes,
                'keyExpiresIn' => $keyExpiresIn,
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
    public function overrideAttributes(string $consumerKey, AttributesProperty $attributes): AppCredentialInterface
    {
        $normalizer = new KeyValueMapNormalizer();
        $response = $this->client->post(
            $this->getEntityEndpointUri($consumerKey),
            (string) json_encode((object) ['attributes' => $normalizer->normalize($attributes)])
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
            ->withPath(sprintf('%s/keys/%s/apiproducts/%s', $this->getBaseEndpointUri(), $consumerKey, $apiProduct))
            ->withQuery(http_build_query(['action' => $status]));
        $this->client->post($uri, null, ['Content-Type' => 'application/octet-stream']);
    }

    /**
     * @inheritdoc
     */
    public function deleteApiProduct(string $consumerKey, string $apiProduct): EntityInterface
    {
        $uri = $this->getBaseEndpointUri()
            ->withPath(sprintf('%s/keys/%s/apiproducts/%s', $this->getBaseEndpointUri(), $consumerKey, $apiProduct));
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
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf(
                '/organizations/%s/developers/%s/apps/%s',
                $this->organization,
                $this->developerId,
                $this->appName
            ));
    }

    /**
     * @inheritdoc
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath(sprintf('%s/keys/%s', $this->getBaseEndpointUri(), $entityId));
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        return AppCredential::class;
    }
}
