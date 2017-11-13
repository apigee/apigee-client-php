<?php

namespace Apigee\Edge\Entity;

/**
 * Class NonCpsEntityController.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
abstract class NonCpsEntityController extends AbstractEntityController implements NonCpsEntityControllerInterface
{
    /**
     * @inheritdoc
     */
    public function getEntities(): array
    {
        $entities = [];
        $query_params = [];
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        $responseArray = $this->parseResponseToArray($response);
        foreach ($responseArray as $item) {
            $tmp = $this->entityFactory->getEntityByController($this)::create($item);
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
            'expand' => 'true',
        ];
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        return $this->parseResponseToArray($response);
    }
}
