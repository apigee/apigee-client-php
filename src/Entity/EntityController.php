<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\Exception\CpsNotEnabledException;
use Apigee\Edge\HttpClient\ClientInterface;
use Apigee\Edge\Structure\CpsListLimitInterface;

/**
 * Class EntityController.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
abstract class EntityController extends AbstractEntityController implements EntityControllerInterface
{
    /** @var string Name of the organization that the entity belongs to. */
    protected $organization;

    /** @var \Apigee\Edge\Entity\EntityControllerFactory */
    protected $entityControllerFactory;

    /**
     * EntityController constructor.
     *
     * @param string $organization
     *   Name of the organization that the entities belongs to.
     * @param ClientInterface|null $client
     * @param EntityFactoryInterface|null $entityFactory
     * @param EntityControllerFactoryInterface $entityControllerFactory
     */
    public function __construct(
        string $organization,
        ClientInterface $client = null,
        EntityFactoryInterface $entityFactory = null,
        EntityControllerFactoryInterface $entityControllerFactory = null
    ) {
        $this->organization = $organization;
        parent::__construct($client, $entityFactory);
        $this->entityControllerFactory = $entityControllerFactory ?:
            new EntityControllerFactory($organization, $this->client, $this->entityFactory);
    }

    /**
     * @inheritdoc
     */
    public function getOrganisation(): string
    {
        return $this->organization;
    }

    /**
     * @inheritdoc
     */
    public function setOrganisation(string $orgName): string
    {
        $this->organization = $orgName;
        return $this->organization;
    }

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
            $tmp = $this->entitySerializer->denormalize($item, $this->entityFactory->getEntityByController($this));
            $entities[$tmp->id()] = $tmp;
        }
        return $entities;
    }

    /**
     * @inheritdoc
     */
    public function getEntityIds(CpsListLimitInterface $cpsLimit = null): array
    {
        $query_params = [];
        if ($cpsLimit) {
            $query_params['startKey'] = $cpsLimit->getStartKey();
            $query_params['count'] = $cpsLimit->getLimit();
        }
        $uri = $this->getBaseEndpointUri()->withQuery(http_build_query($query_params));
        $response = $this->client->get($uri);
        return $this->parseResponseToArray($response);
    }

    /**
     * @inheritdoc
     */
    public function createCpsLimit(string $startKey, int $limit): CpsListLimitInterface
    {
        $orgController = $this->entityControllerFactory->getControllerByEndpoint('organizations');
        $organization = $orgController->load($this->organization);
        if (!$organization->getPropertyValue('features.isCpsEnabled')) {
            throw new CpsNotEnabledException($this->organization);
        }

        // Create an anonymous class here because this class should not exist and be in use
        // in those controllers that do not work with entities that belongs to an organization.
        $cpsLimit = new class() implements CpsListLimitInterface
        {
            protected $startKey;

            protected $limit;

            /**
             * @return string The primary key of the entity that the list should start.
             */
            public function getStartKey(): string
            {
                return $this->startKey;
            }

            /**
             * @return int Number of entities to return.
             */
            public function getLimit(): int
            {
                return $this->limit;
            }

            public function setStartKey(string $startKey): string
            {
                $this->startKey = $startKey;
                return $this->startKey;
            }

            public function setLimit(int $limit): int
            {
                $this->limit = $limit;
                return $this->limit;
            }
        };
        $cpsLimit->setStartKey($startKey);
        $cpsLimit->setLimit($limit);
        return $cpsLimit;
    }
}
