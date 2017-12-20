<?php

namespace Apigee\Edge\Controller;

/**
 * Trait NonCpsListingEntityControllerTrait.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see \Apigee\Edge\Controller\NonCpsListingEntityControllerInterface
 */
trait NonCpsListingEntityControllerTrait
{
    /**
     * @inheritdoc
     *
     * @psalm-suppress PossiblyNullArrayOffset $tmp->id() is always not null here.
     */
    public function getEntities(): array
    {
        $entities = [];
        $query_params = [
            'expand' => 'true',
        ];
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        $responseArray = $this->responseToArray($response);
        foreach ($responseArray as $item) {
            /** @var \Apigee\Edge\Entity\EntityInterface $tmp */
            $tmp = $this->entitySerializer->denormalize($item, $this->entityFactory->getEntityTypeByController($this));
            $entities[$tmp->id()] = $tmp;
        }

        return $entities;
    }

    /**
     * @inheritdoc
     */
    public function getEntityIds(): array
    {
        $query_params = [
            'expand' => 'false',
        ];
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);

        return $this->responseToArray($response);
    }
}
