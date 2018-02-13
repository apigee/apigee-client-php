<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Api\Management\Entity\AppDenormalizer;
use Apigee\Edge\Api\Management\Entity\AppInterface;
use Apigee\Edge\Controller\CpsLimitEntityController;
use Apigee\Edge\Structure\CpsListLimitInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class AppController.
 *
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

    /**
     * @inheritdoc
     */
    public function loadApp(string $appId): AppInterface
    {
        $response = $this->client->get($this->getEntityEndpointUri($appId));

        return $this->entitySerializer->denormalize(
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
            $tmp = $this->entitySerializer->denormalize(
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
            $tmp = $this->entitySerializer->denormalize(
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
    protected function entityNormalizers()
    {
        // Add our special AppDenormalizer to the top of the list.
        // This way enforce parent $this->entitySerializer calls to use it for apps primarily.
        return array_merge([new AppDenormalizer()], parent::entityNormalizers());
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
