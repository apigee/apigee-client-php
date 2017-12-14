<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\Structure\CpsListLimitInterface;

/**
 * Trait CpsListingEntityControllerTrait.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see \Apigee\Edge\Entity\CpsListingEntityControllerInterface
 */
trait CpsListingEntityControllerTrait
{
    /**
     * @inheritdoc
     */
    public function getEntities(CpsListLimitInterface $cpsLimit = null): array
    {
        $entities = [];
        $query_params = [
            'expand' => 'true',
        ];
        if ($cpsLimit) {
            $query_params['startKey'] = $cpsLimit->getStartKey();
            $query_params['count'] = $cpsLimit->getLimit();
        }
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        $responseArray = $this->parseResponseToArray($response);
        // Ignore entity type key from response, ex.: developer.
        $responseArray = reset($responseArray);
        foreach ($responseArray as $item) {
            /** @var \Apigee\Edge\Entity\EntityInterface $tmp */
            $tmp = $this->entitySerializer->denormalize(
                $item,
                $this->entityFactory->getEntityTypeByController($this)
            );
            $entities[$tmp->id()] = $tmp;
        }

        return $entities;
    }

    /**
     * @inheritdoc
     */
    public function getEntityIds(CpsListLimitInterface $cpsLimit = null): array
    {
        $query_params = [
            'expand' => 'false',
        ];
        if ($cpsLimit) {
            $query_params['startKey'] = $cpsLimit->getStartKey();
            $query_params['count'] = $cpsLimit->getLimit();
        }
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);

        return $this->parseResponseToArray($response);
    }
}
