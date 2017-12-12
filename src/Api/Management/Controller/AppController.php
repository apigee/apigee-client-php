<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\AppDenormalizer;
use Apigee\Edge\Api\Management\Entity\AppInterface;
use Apigee\Edge\Entity\CpsLimitEntityController;
use Apigee\Edge\Structure\CpsListLimitInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class AppController.
 *
 * @package Apigee\Edge\Api\Management\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class AppController extends CpsLimitEntityController implements AppControllerInterface
{
    /**
     * String that should be sent to the API to change the status of a credential to approved.
     */
    public const STATUS_APPROVE = 'approve';

    /**
     * String that should be sent to the API to change the status of a credential to revoked.
     */
    public const STATUS_REVOKE = 'revoke';

    /** @var \Apigee\Edge\Api\Management\Entity\AppDenormalizer */
    protected $appEntityDenormalizer;

    /**
     * AppController constructor.
     *
     * @param string $organization
     * @param null $client
     * @param null $entityFactory
     * @param \Apigee\Edge\Api\Management\Controller\OrganizationControllerInterface|null $organizationController
     */
    public function __construct(
        string $organization,
        $client = null,
        $entityFactory = null,
        OrganizationControllerInterface $organizationController = null
    ) {
        parent::__construct($organization, $client, $entityFactory, $organizationController);
        $this->appEntityDenormalizer = new AppDenormalizer();
    }

    /**
     * @inheritdoc
     */
    protected function getBaseEndpointUri(): UriInterface
    {
        return $this->client->getUriFactory()
            ->createUri(sprintf('/organizations/%s/apps', $this->organization));
    }

    /**
     * @inheritdoc
     */
    public function loadApp(string $appId): AppInterface
    {
        $response = $this->client->get($this->getEntityEndpointUri($appId));
        return $this->appEntityDenormalizer->denormalize(
            $this->parseResponseToArray($response),
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
        return $this->parseResponseToArray($response);
    }

    /**
     * @inheritdoc
     */
    public function listApps(bool $includeCredentials = true, CpsListLimitInterface $cpsLimit = null): array
    {
        $entities = [];
        $queryParams = [
            'expand' => 'true',
            'includeCred' => $includeCredentials ? 'true' : 'false',
        ];
        $response = $this->request($queryParams, $cpsLimit);
        $responseArray = $this->parseResponseToArray($response);
        // Ignore entity type key from response, ex.: developer.
        $responseArray = reset($responseArray);
        foreach ($responseArray as $item) {
            /** @var \Apigee\Edge\Entity\EntityInterface $tmp */
            $tmp = $this->appEntityDenormalizer->denormalize(
                $item,
                AppInterface::class
            );
            $entities[$tmp->id()] = $tmp;
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
        return $this->parseResponseToArray($response);
    }

    /**
     * @inheritdoc
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
        $responseArray = $this->parseResponseToArray($response);
        // Ignore entity type key from response, ex.: developer.
        $responseArray = reset($responseArray);
        foreach ($responseArray as $item) {
            /** @var \Apigee\Edge\Entity\EntityInterface $tmp */
            $tmp = $this->appEntityDenormalizer->denormalize(
                $item,
                AppInterface::class
            );
            $entities[$tmp->id()] = $tmp;
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
        return $this->parseResponseToArray($response);
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
        return $this->parseResponseToArray($response);
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
