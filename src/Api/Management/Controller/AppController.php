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

use Apigee\Edge\Api\Management\Entity\AppInterface;
use Apigee\Edge\Controller\CpsLimitEntityController;
use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\Structure\CpsListLimitInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class AppController.
 */
class AppController extends CpsLimitEntityController implements AppControllerInterface
{
    use AppControllerTrait;

    /**
     * String that should be sent to the API to change the status of a credential to approved.
     */
    public const STATUS_APPROVE = 'approve';

    /**
     * String that should be sent to the API to change the status of a credential to revoked.
     */
    public const STATUS_REVOKE = 'revoke';

    /**
     * AppController constructor.
     *
     * @param string $organization
     * @param \Apigee\Edge\HttpClient\ClientInterface $client
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $entityNormalizers
     * @param \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        ClientInterface $client,
        array $entityNormalizers = [],
        ?OrganizationControllerInterface $organizationController = null
    ) {
        $entityNormalizers = array_merge($entityNormalizers, $this->appEntityNormalizers());
        parent::__construct($organization, $client, $entityNormalizers, $organizationController);
    }

    /**
     * @inheritdoc
     */
    public function loadApp(string $appId): AppInterface
    {
        $response = $this->client->get($this->getEntityEndpointUri($appId));

        return $this->entityTransformer->denormalize(
            // Pass it as an object, because if serializer would have been used here (just as other places) it would
            // pass an object to the denormalizer and not an array.
            (object) $this->responseToArray($response),
            AppInterface::class
        );
    }

    /**
     * @inheritdoc
     */
    public function listAppIds(CpsListLimitInterface $cpsLimit = null): array
    {
        $queryParams = [
            'expand' => 'false',
        ];
        $response = $this->request($queryParams, $cpsLimit);

        return $this->responseToArray($response);
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress PossiblyNullArrayOffset $tmp->getAppId() is always not null here.
     */
    public function listApps(bool $includeCredentials = true, CpsListLimitInterface $cpsLimit = null): array
    {
        $entities = [];
        $queryParams = [
            'expand' => 'true',
            'includeCred' => $includeCredentials ? 'true' : 'false',
        ];
        $response = $this->request($queryParams, $cpsLimit);
        $responseArray = $this->responseToArray($response);
        // Ignore entity type key from response, ex.: developer.
        $responseArray = reset($responseArray);
        foreach ($responseArray as $item) {
            /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $tmp */
            $tmp = $this->entityTransformer->denormalize(
                $item,
                AppInterface::class
            );
            $entities[$tmp->getAppId()] = $tmp;
        }

        return $entities;
    }

    /**
     * @inheritdoc
     */
    public function listAppIdsByStatus(string $status, CpsListLimitInterface $cpsLimit = null): array
    {
        $queryParams = [
            'expand' => 'false',
            'status' => $status,
        ];
        $response = $this->request($queryParams, $cpsLimit);

        return $this->responseToArray($response);
    }

    /**
     * @inheritdoc
     *
     * @psalm-suppress PossiblyNullArrayOffset $tmp->getAppId() is always not null here.
     */
    public function listAppsByStatus(
        string $status,
        bool $includeCredentials = true,
        CpsListLimitInterface $cpsLimit = null
    ): array {
        $entities = [];
        $queryParams = [
            'expand' => 'false',
            'status' => $status,
            'includeCred' => $includeCredentials ? 'true' : 'false',
        ];
        $response = $this->request($queryParams, $cpsLimit);
        $responseArray = $this->responseToArray($response);
        // Ignore entity type key from response, ex.: developer.
        $responseArray = reset($responseArray);
        foreach ($responseArray as $item) {
            /** @var \Apigee\Edge\Api\Management\Entity\AppInterface $tmp */
            $tmp = $this->entityTransformer->denormalize(
                $item,
                AppInterface::class
            );
            $entities[$tmp->getAppId()] = $tmp;
        }

        return $entities;
    }

    /**
     * @inheritdoc
     */
    public function listAppIdsByType(string $appType, CpsListLimitInterface $cpsLimit = null): array
    {
        $queryParams = [
            'expand' => 'false',
            'apptype' => $appType,
        ];
        $response = $this->request($queryParams, $cpsLimit);

        return $this->responseToArray($response);
    }

    /**
     * @inheritdoc
     */
    public function listAppIdsByFamily(string $appFamily, CpsListLimitInterface $cpsLimit = null): array
    {
        $queryParams = [
            'expand' => 'false',
            'appfamily' => $appFamily,
        ];
        $response = $this->request($queryParams, $cpsLimit);

        return $this->responseToArray($response);
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()->createUri(sprintf('/organizations/%s/apps', $this->organization));
    }

    /**
     * @inheritdoc
     */
    protected function getEntityClass(): string
    {
        // It could be either a developer- or a company app. The AppDenormalizer can automatically decide which one
        // should be used.
        return '';
    }

    /**
     * Sends a request to the API endpoint with the required query parameters.
     *
     * @param array $queryParams
     *   Mandatory query parameters for an API call.
     * @param \Apigee\Edge\Structure\CpsListLimitInterface|null $cpsLimit
     *   Limit number of returned results.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function request(array $queryParams, CpsListLimitInterface $cpsLimit = null): ResponseInterface
    {
        if ($cpsLimit) {
            $queryParams['startKey'] = $cpsLimit->getStartKey();
            $queryParams['count'] = $cpsLimit->getLimit();
        }
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($queryParams));

        return $this->client->get($uri);
    }
}
